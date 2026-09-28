<?php

namespace App\Observers;

use App\Models\Penjualan;

class PenjualanObserver
{
    /**
     * Handle the Penjualan "created" event.
     */
    public function created(Penjualan $penjualan): void
    {
        // 1. Get Kolam and Update Persediaan Stock
        $kolam = $penjualan->kolam;
        if ($kolam) {
            // 1b. Tambah Stok Keluar di Persediaan (Bibit)
            $persediaan = $kolam->persediaan;
            if ($persediaan) {
                $persediaan->stok_keluar += $penjualan->jumlah_ekor;
                $persediaan->stok_sisa = $persediaan->stok_masuk - $persediaan->stok_keluar;
                $persediaan->save();
            }
        }

        // 2. Tambah Saldo Modal (Pemasukan)
        $modal = \App\Models\Modal::getSingleton();
        $modal->tambahModal($penjualan->total_harga);

        // 3. Buat Jurnal Otomatis (Penjualan & HPP)
        $coaKas = \App\Models\Coa::where('nama', 'Kas')->orWhere('kode', '111')->first();
        if ($coaKas) {
            $coaKas->update(['kode' => '111', 'nama' => 'Kas']);
        } else {
            $coaKas = \App\Models\Coa::create(['kode' => '111', 'nama' => 'Kas', 'tipe' => 'Asset']);
        }

        $coaPendapatan = null;
        if ($kolam) {
            $namaCoaPendapatan = "Penjualan Ikan Kolam " . $kolam->nomor_kolam;
            $kodeCoaPendapatan = "411" . $kolam->nomor_kolam;
        
            $coaPendapatan = \App\Models\Coa::where('kode', $kodeCoaPendapatan)->orWhere('nama', $namaCoaPendapatan)->first();
            if ($coaPendapatan) {
                $coaPendapatan->update(['kode' => $kodeCoaPendapatan, 'nama' => $namaCoaPendapatan, 'tipe' => 'Revenue']);
            } else {
                $coaPendapatan = \App\Models\Coa::create(['kode' => $kodeCoaPendapatan, 'nama' => $namaCoaPendapatan, 'tipe' => 'Revenue']);
            }
        }

        // --- 3a. Jurnal Penjualan (Pemasukan) ---
        if ($coaKas && $coaPendapatan) {
            $jurnalJual = \App\Models\Jurnal::create([
                'tanggal' => $penjualan->tanggal_penjualan,
                'keterangan' => 'Penjualan Panen Kolam ' . ($kolam->nomor_kolam ?? '?'),
                'ref_type' => Penjualan::class,
                'ref_id' => $penjualan->id
            ]);

            // Debit Kas
            \App\Models\JurnalDetail::create([
                'jurnal_id' => $jurnalJual->id,
                'coa_id' => $coaKas->id,
                'debit' => $penjualan->total_harga,
                'kredit' => 0
            ]);

            // Kredit Pendapatan
            \App\Models\JurnalDetail::create([
                'jurnal_id' => $jurnalJual->id,
                'coa_id' => $coaPendapatan->id,
                'debit' => 0,
                'kredit' => $penjualan->total_harga
            ]);
        }

        if ($kolam) {
            $coaBarangJadi = \App\Models\Coa::where('nama', 'Persediaan Barang Jadi')->first();
            if (!$coaBarangJadi) {
                $coaBarangJadi = \App\Models\Coa::create([
                    'kode' => '1135',
                    'nama' => 'Persediaan Barang Jadi',
                    'tipe' => 'Asset'
                ]);
            }
            $coaHPP = \App\Models\Coa::where('nama', 'Beban Pokok Penjualan')->orWhere('nama', 'Harga Pokok Penjualan')->first();
            
            if ($coaBarangJadi && $coaHPP) {
                // Hitung Total Beban Kolam (Sesuai Laporan Pendapatan per Kolam)
                $getBeban = function($kategori) use ($kolam) {
                    $coaName = "Beban {$kategori} Kolam " . $kolam->nomor_kolam;
                    $coa = \App\Models\Coa::where('nama', $coaName)->first();
                    if (!$coa) return 0;
                
                    return \App\Models\JurnalDetail::where('coa_id', $coa->id)
                        ->selectRaw('SUM(debit) - SUM(kredit) as total')
                        ->value('total') ?? 0;
                };
                
                $bebanVitamin = $getBeban('Vitamin');
                $bebanObat = $getBeban('Obat');
                $bebanPakan = $getBeban('Pakan');
                
                $kematianIds = \App\Models\KematianIkan::where('kolam_id', $kolam->id)->pluck('id');
                $bebanKematian = \App\Models\JurnalDetail::whereHas('coa', function($q) {
                        $q->where('nama', 'Beban Kematian Ikan')->orWhere('nama', 'Susut Ikan');
                    })
                    ->whereHas('jurnal', function($q) use ($kematianIds) {
                        $q->where('ref_type', \App\Models\KematianIkan::class)
                          ->whereIn('ref_id', $kematianIds);
                    })->sum('debit');
                
                $bebanBibit = \App\Models\Pembelian::whereHas('persediaan', function ($q) {
                        $q->where('kategori', 'Bibit');
                    })
                    ->where('kolam_id', $kolam->id)
                    ->sum('total_harga');
                
                $totalHPP = $bebanVitamin + $bebanObat + $bebanPakan + $bebanKematian + $bebanBibit;
                
                // Decrement Stock Logic
                $kolam->decrement('jumlah', $penjualan->jumlah_ekor);

                if ($totalHPP > 0) {
                    $jurnalHPP = \App\Models\Jurnal::create([
                        'tanggal' => $penjualan->tanggal_penjualan,
                        'keterangan' => 'HPP Penjualan Kolam ' . $kolam->nomor_kolam . ' (' . $penjualan->jumlah_ekor . ' ekor)',
                        'ref_type' => Penjualan::class,
                        'ref_id' => $penjualan->id
                    ]);

                    // Debit HPP
                    \App\Models\JurnalDetail::create([
                        'jurnal_id' => $jurnalHPP->id,
                        'coa_id' => $coaHPP->id,
                        'debit' => $totalHPP,
                        'kredit' => 0
                    ]);

                    // Kredit Persediaan Barang Jadi (Asset Reduction)
                    \App\Models\JurnalDetail::create([
                        'jurnal_id' => $jurnalHPP->id,
                        'coa_id' => $coaBarangJadi->id,
                        'debit' => 0,
                        'kredit' => $totalHPP
                    ]);
                }
            }

            // Setelah panen terjual, kembalikan status kolam menjadi kosong dan reset nilainya
            $kolam->update([
                'status' => 'kosong',
                'persediaan_id' => null,
                'tanggal_tebar' => null,
                'ukuran_ikan' => null,
                'jumlah' => 0,
                'jumlah_tebar' => 0,
                'jumlah_mati' => 0,
            ]);
        }
    }

    /**
     * Handle the Penjualan "updated" event.
     */
    public function updated(Penjualan $penjualan): void
    {
        //
    }

    /**
     * Handle the Penjualan "deleted" event.
     */
    public function deleted(Penjualan $penjualan): void
    {
        //
    }

    /**
     * Handle the Penjualan "restored" event.
     */
    public function restored(Penjualan $penjualan): void
    {
        //
    }

    /**
     * Handle the Penjualan "force deleted" event.
     */
    public function forceDeleted(Penjualan $penjualan): void
    {
        //
    }
}
