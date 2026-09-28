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
            $kolam = null;
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
            $namaCoaPersediaan = 'Persediaan'; // Fallback
            $kategori = $persediaan->kategori;
            
            // Tentukan Nama COA Target
            if ($kategori === 'Bibit') {
                if ($pembelian->kolam_id) {
                    if ($kolam) {
                        $namaCoaPersediaan = "Persediaan Bibit Kolam " . $kolam->nomor_kolam;
                    }
                } else {
                     $namaCoaPersediaan = "Persediaan Bibit";
                }
            } elseif ($kategori === 'Pakan') {
                $namaCoaPersediaan = 'Persediaan Pakan';
            } elseif ($kategori === 'Obat') {
                $namaCoaPersediaan = 'Persediaan Obat';
            } elseif ($kategori === 'Vitamin') {
                $namaCoaPersediaan = 'Persediaan Vitamin';
            }

            // Cari atau Buat COA Persediaan
            $coaPersediaan = \App\Models\Coa::where('nama', $namaCoaPersediaan)->first();
            
            if (!$coaPersediaan) {
                $newCode = '1130'; // Fallback
                if ($kategori === 'Bibit') {
                    if ($pembelian->kolam_id && $kolam) {
                        $newCode = '1131' . $kolam->nomor_kolam;
                    } else {
                        $newCode = '11310';
                    }
                } elseif ($kategori === 'Vitamin') {
                    $newCode = '1132';
                } elseif ($kategori === 'Obat') {
                    $newCode = '1133';
                } elseif ($kategori === 'Pakan') {
                    $newCode = '1134';
                }

                \Illuminate\Support\Facades\Log::info("Auto Creating COA: $namaCoaPersediaan with Code $newCode");
                
                $coaPersediaan = \App\Models\Coa::create([
                    'kode' => $newCode,
                    'nama' => $namaCoaPersediaan,
                    'tipe' => 'Asset',
                ]);
            }

            $coaKas = \App\Models\Coa::where('nama', 'Kas')->first();
            
            if ($coaPersediaan && $coaKas) {
                $jurnal = \App\Models\Jurnal::create([
                    'tanggal' => $pembelian->tanggal_pembelian,
                    'keterangan' => 'Pembelian ' . $kategori . ': ' . $persediaan->nama_barang,
                    'ref_type' => \App\Models\Pembelian::class,
                    'ref_id' => $pembelian->id,
                ]);

                // Debit Persediaan (Bertambah)
                \App\Models\JurnalDetail::create([
                    'jurnal_id' => $jurnal->id,
                    'coa_id' => $coaPersediaan->id,
                    'debit' => $pembelian->total_harga,
                    'kredit' => 0,
                ]);

                // Kredit Kas (Berkurang)
                \App\Models\JurnalDetail::create([
                    'jurnal_id' => $jurnal->id,
                    'coa_id' => $coaKas->id,
                    'debit' => 0,
                    'kredit' => $pembelian->total_harga,
                ]);
                
                 \Illuminate\Support\Facades\Log::info("Journal Created: Debit $namaCoaPersediaan, Kredit Kas");
            } else {
                 \Illuminate\Support\Facades\Log::warning('COA missing for journal creation (Kas missing?).');
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
