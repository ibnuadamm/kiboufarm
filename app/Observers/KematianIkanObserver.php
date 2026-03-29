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
            // Mengurangi 'jumlah' karena 'jumlah' ini dipakai sebagai referensi stok hidup saat ini di aplikasi ini (berdasarkan logika validasi penjualan sebelumnya)
            // ATAU apakah 'jumlah' itu 'jumlah_tebar_awal'? 
            // Validasi penjualan sebelumnya membandingkan ($jumlah_ekor > $kolam->jumlah).
            // Jadi $kolam->jumlah = Current Population.
            if ($kolam->jumlah >= $kematianIkan->jumlah_ekor) {
                $kolam->decrement('jumlah', $kematianIkan->jumlah_ekor);
            } else {
                // Warning logic handled at Controller ideally, but here just safe decrement
                $kolam->jumlah = 0;
                $kolam->save();
            }

            // 2. Tambah Counter Kematian
            $kolam->increment('jumlah_mati', $kematianIkan->jumlah_ekor);

            // 3. Tambah Stok Keluar di Persediaan (Bibit)
            $persediaan = $kolam->persediaan;
            if ($persediaan) {
                // User Request: Update Stok Keluar and Stok Sisa on Fish Death
                $persediaan->stok_keluar += $kematianIkan->jumlah_ekor;
                $persediaan->stok_sisa = $persediaan->stok_masuk - $persediaan->stok_keluar;
                $persediaan->save();
            }

            // 4. Create Journal (Debit Beban, Credit Persediaan)
            // Calculate Cost Per Ekor
            // Find Pembelian linked to this Kolam to get price info
            // Assumption: Use Average Cost if multiple purchases
            $pembelians = \App\Models\Pembelian::where('kolam_id', $kolam->id)->get();
            $totalHarga = $pembelians->sum('total_harga');
            $totalEkor = $pembelians->sum('jumlah');

            $costPerEkor = 0;
            if ($totalEkor > 0) {
                // Priority 1: Specific Linked Purchase (Best Accuracy)
                $costPerEkor = $totalHarga / $totalEkor;
            } else {
                // Priority 2: Smart Fallback - Nearest Purchase Record before Tebar
                // Logic: If manual kolam, user likely bought bibit recently.
                if ($persediaan) {
                     $nearestPurchase = \App\Models\Pembelian::where('persediaan_id', $persediaan->id)
                        ->where('tanggal_pembelian', '<=', $kolam->tanggal_tebar)
                        ->latest('tanggal_pembelian')
                        ->first();
                    
                     if ($nearestPurchase && $nearestPurchase->jumlah > 0) {
                         $costPerEkor = $nearestPurchase->total_harga / $nearestPurchase->jumlah;
                     } else {
                         // Priority 3: Global Average (Last Resort)
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
                // DEBIT SIDE: 'Beban Susut Ikan' (previously Beban Kerugian Kematian Ikan)
                // DEBIT SIDE: 'Susut Ikan'
                $akunBeban = \App\Models\Akun::where('nama', 'Susut Ikan')->first();
                if (!$akunBeban) {
                    // Create if missing (Smart Auto-Correction)
                    $akunBeban = \App\Models\Akun::create([
                        'kode' => '6-106', // Next available expense code
                        'nama' => 'Susut Ikan',
                        'tipe' => 'Expense'
                    ]);
                }

                // CREDIT SIDE: 'Persediaan Ikan Kolam X'
                $akunPersediaan = null;
                $specificAccountName = "Persediaan Ikan Kolam " . $kolam->nomor_kolam;
                $akunPersediaan = \App\Models\Akun::where('nama', $specificAccountName)->first();

                if (!$akunPersediaan) {
                    // Fallback to Global Bibit if specific pond account not found
                    $akunPersediaan = \App\Models\Akun::where('nama', 'Persediaan Bibit')->first();
                }
                
                if (!$akunPersediaan) {
                     $akunPersediaan = \App\Models\Akun::where('nama', 'Persediaan')->first();
                }

                if ($akunBeban && $akunPersediaan) {
                    $jurnal = \App\Models\Jurnal::create([
                        'tanggal' => $kematianIkan->tanggal,
                        'keterangan' => 'Kematian Ikan Kolam #' . $kolam->nomor_kolam . ' (' . $kematianIkan->jumlah_ekor . ' ekor)',
                        'ref_type' => \App\Models\KematianIkan::class,
                        'ref_id' => $kematianIkan->id,
                    ]);

                    // Debit Beban Susut
                    \App\Models\JurnalDetail::create([
                        'jurnal_id' => $jurnal->id,
                        'akun_id' => $akunBeban->id,
                        'debit' => $lossAmount,
                        'kredit' => 0,
                    ]);

                    // Kredit Persediaan Ikan Kolam X
                    \App\Models\JurnalDetail::create([
                        'jurnal_id' => $jurnal->id,
                        'akun_id' => $akunPersediaan->id,
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
