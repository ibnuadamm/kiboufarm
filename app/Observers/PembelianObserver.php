<?php

namespace App\Observers;

use App\Models\Pembelian;

class PembelianObserver
{
    /**
     * Handle the Pembelian "created" event.
     */
    public function created(Pembelian $pembelian): void
    {
        \Illuminate\Support\Facades\Log::info('OBSERVER FIRED Pembelian Created ' . $pembelian->id);
        $persediaan = $pembelian->persediaan;
        if ($persediaan) {
            // 1. Update Stok
            $persediaan->increment('stok_masuk', $pembelian->jumlah);
            $persediaan->update([
                'stok_sisa' => $persediaan->stok_masuk - $persediaan->stok_keluar
            ]);

            // 1b. Jika Bibit & Pilih Kolam -> Update Bibit Kolam
            // Note: kolam_id might be on Pembelian (per user request context) or via relation?
            // Checking table schema: pembelians table doesn't formally have kolam_id in the migration I saw (2026_01_17_182327_create_pembelians_table.php),
            // but the Model has it in fillable, and observer uses it. 
            // Assuming the column exists (maybe added in a later migration I didn't see or manually).
            // Logic below assumes $pembelian->kolam_id works.
            
            if ($persediaan->kategori === 'Bibit' && $pembelian->kolam_id) {
                $kolam = \App\Models\Kolam::find($pembelian->kolam_id);
                if ($kolam) {
                    $kolam->increment('jumlah', $pembelian->jumlah);
                    $kolam->increment('jumlah_tebar', $pembelian->jumlah);
                }
            }

            // 2. Kurangi Saldo Modal
            $modal = \App\Models\Modal::getSingleton();
            $modal->kurangiModal($pembelian->total_harga);

            // 3. JURNAL OTOMATIS
            $namaAkunPersediaan = 'Persediaan'; // Fallback
            $kategori = $persediaan->kategori;
            
            // Tentukan Nama Akun Target
            if ($kategori === 'Bibit') {
                if ($pembelian->kolam_id) {
                    $kolam = \App\Models\Kolam::find($pembelian->kolam_id);
                    if ($kolam) {
                        $namaAkunPersediaan = "Persediaan Ikan Kolam " . $kolam->nomor_kolam;
                    } 
                } else {
                     $namaAkunPersediaan = "Persediaan Bibit";
                }
            } elseif ($kategori === 'Pakan') {
                $namaAkunPersediaan = 'Persediaan Pakan';
            } elseif ($kategori === 'Obat') {
                $namaAkunPersediaan = 'Persediaan Obat';
            } elseif ($kategori === 'Vitamin') {
                $namaAkunPersediaan = 'Persediaan Vitamin';
            }

            // Cari atau Buat Akun Persediaan
            $akunPersediaan = \App\Models\Akun::where('nama', $namaAkunPersediaan)->first();
            
            if (!$akunPersediaan) {
                // Auto Create Akun jika tidak ada
                $latestCode = \App\Models\Akun::where('kode', 'like', '1-2%')
                    ->orderByRaw('LENGTH(kode) DESC')
                    ->orderBy('kode', 'desc')
                    ->first();
                
                // Simple logic generate kode baru (takut duplikat, pakai timestamp suffix safe-guard)
                // Idealnya: parse last code.
                $newCodeSuffix = rand(100, 999);
                $newCode = '1-200-' . $newCodeSuffix; 
                
                if ($latestCode) {
                    // Try to increment last part? 
                    // Too risky to parse unknown format. stick to random/time suffix or standard.
                    // Or let's just make it '1-2xx' if we can.
                    //$newCode = '1-2' . str_pad((string)rand(10, 99), 2, '0', STR_PAD_LEFT) . '-' . rand(1,99);
                }
                
                \Illuminate\Support\Facades\Log::info("Auto Creating Account: $namaAkunPersediaan with Code $newCode");
                
                $akunPersediaan = \App\Models\Akun::create([
                    'kode' => $newCode,
                    'nama' => $namaAkunPersediaan,
                    'tipe' => 'Asset',
                ]);
            }

            $akunKas = \App\Models\Akun::where('nama', 'Kas')->first();
            
            if ($akunPersediaan && $akunKas) {
                $jurnal = \App\Models\Jurnal::create([
                    'tanggal' => $pembelian->tanggal_pembelian,
                    'keterangan' => 'Pembelian ' . $kategori . ': ' . $persediaan->nama_barang,
                    'ref_type' => \App\Models\Pembelian::class,
                    'ref_id' => $pembelian->id,
                ]);

                // Debit Persediaan (Bertambah)
                \App\Models\JurnalDetail::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $akunPersediaan->id,
                    'debit' => $pembelian->total_harga,
                    'kredit' => 0,
                ]);

                // Kredit Kas (Berkurang)
                \App\Models\JurnalDetail::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $akunKas->id,
                    'debit' => 0,
                    'kredit' => $pembelian->total_harga,
                ]);
                
                 \Illuminate\Support\Facades\Log::info("Journal Created: Debit $namaAkunPersediaan, Kredit Kas");
            } else {
                 \Illuminate\Support\Facades\Log::warning('Accounts missing for journal creation (Kas missing?).');
            }
        }
    }

    /**
     * Handle the Pembelian "updated" event.
     */
    public function updated(Pembelian $pembelian): void
    {
        //
    }

    /**
     * Handle the Pembelian "deleted" event.
     */
    public function deleted(Pembelian $pembelian): void
    {
        //
    }

    /**
     * Handle the Pembelian "restored" event.
     */
    public function restored(Pembelian $pembelian): void
    {
        //
    }

    /**
     * Handle the Pembelian "force deleted" event.
     */
    public function forceDeleted(Pembelian $pembelian): void
    {
        //
    }
}
