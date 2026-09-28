<?php

namespace App\Observers;

use App\Models\KematianIkan;

class KematianIkanObserver
{
    /**
     * Handle the KematianIkan "created" event.
     */
    public function created(KematianIkan $kematianIkan): void
    {
        $kolam = $kematianIkan->kolam;
        
        if ($kolam) {
            // 1. Kurangi Jumlah Ikan di Kolam (Live Count)
            if ($kolam->jumlah >= $kematianIkan->jumlah_ekor) {
                $kolam->decrement('jumlah', $kematianIkan->jumlah_ekor);
            } else {
                $kolam->jumlah = 0;
                $kolam->save();
            }

            // 2. Tambah Counter Kematian
            $kolam->increment('jumlah_mati', $kematianIkan->jumlah_ekor);

            // 3. Tambah Stok Keluar di Persediaan (Bibit)
            $persediaan = $kolam->persediaan;
            if ($persediaan) {
                $persediaan->stok_keluar += $kematianIkan->jumlah_ekor;
                $persediaan->stok_sisa = $persediaan->stok_masuk - $persediaan->stok_keluar;
                $persediaan->save();
            }

            // 4. Create Journal (Debit Beban, Credit Persediaan)
            $pembelians = \App\Models\Pembelian::where('kolam_id', $kolam->id)->get();
            $totalHarga = $pembelians->sum('total_harga');
            $totalEkor = $pembelians->sum('jumlah');

            $costPerEkor = 0;
            if ($totalEkor > 0) {
                $costPerEkor = $totalHarga / $totalEkor;
            } else {
                if ($persediaan) {
                     $nearestPurchase = \App\Models\Pembelian::where('persediaan_id', $persediaan->id)
                        ->where('tanggal_pembelian', '<=', $kolam->tanggal_tebar)
                        ->latest('tanggal_pembelian')
                        ->first();
                    
                     if ($nearestPurchase && $nearestPurchase->jumlah > 0) {
                          $costPerEkor = $nearestPurchase->total_harga / $nearestPurchase->jumlah;
                     } else {
                          $globalPembelians = $persediaan->pembelians;
                          $globalTotalHarga = $globalPembelians->sum('total_harga');
                          $globalTotalQty = $globalPembelians->sum('jumlah');
    
                          if ($globalTotalQty > 0) {
                              $costPerEkor = $globalTotalHarga / $globalTotalQty;
                          }
                     }
                }
            }

            $lossAmount = $costPerEkor * $kematianIkan->jumlah_ekor;

            if ($lossAmount > 0) {
                $coaBeban = \App\Models\Coa::where('nama', 'Beban Kematian Ikan')->first();
                if (!$coaBeban) {
                    $coaBeban = \App\Models\Coa::where('nama', 'Susut Ikan')->first();
                }
                
                if (!$coaBeban) {
                    $coaBeban = \App\Models\Coa::create([
                        'kode' => '517',
                        'nama' => 'Beban Kematian Ikan',
                        'tipe' => 'Expense'
                    ]);
                }

                $specificAccountName = "Persediaan Bibit Kolam " . $kolam->nomor_kolam;
                $coaPersediaan = \App\Models\Coa::where('nama', $specificAccountName)->first();

                if (!$coaPersediaan) {
                    $coaPersediaan = \App\Models\Coa::where('nama', 'Persediaan Bibit')->first();
                }
                
                if (!$coaPersediaan) {
                     $coaPersediaan = \App\Models\Coa::where('nama', 'Persediaan')->first();
                }

                if ($coaBeban && $coaPersediaan) {
                    $jurnal = \App\Models\Jurnal::create([
                        'tanggal' => $kematianIkan->tanggal,
                        'keterangan' => 'Kematian Ikan Kolam ' . $kolam->nomor_kolam . ' (' . $kematianIkan->jumlah_ekor . ' ekor)',
                        'ref_type' => \App\Models\KematianIkan::class,
                        'ref_id' => $kematianIkan->id,
                    ]);

                    // Debit Beban Susut
                    \App\Models\JurnalDetail::create([
                        'jurnal_id' => $jurnal->id,
                        'coa_id' => $coaBeban->id,
                        'debit' => $lossAmount,
                        'kredit' => 0,
                    ]);

                    // Kredit Persediaan Ikan Kolam X
                    \App\Models\JurnalDetail::create([
                        'jurnal_id' => $jurnal->id,
                        'coa_id' => $coaPersediaan->id,
                        'debit' => 0,
                        'kredit' => $lossAmount,
                    ]);
                }
            }
        }
    }

    /**
     * Handle the KematianIkan "updated" event.
     */
    public function updated(KematianIkan $kematianIkan): void
    {
        //
    }

    /**
     * Handle the KematianIkan "deleted" event.
     */
    public function deleted(KematianIkan $kematianIkan): void
    {
        //
    }

    /**
     * Handle the KematianIkan "restored" event.
     */
    public function restored(KematianIkan $kematianIkan): void
    {
        //
    }

    /**
     * Handle the KematianIkan "force deleted" event.
     */
    public function forceDeleted(KematianIkan $kematianIkan): void
    {
        //
    }
}
