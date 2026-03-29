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
        // 1. Update Status Kolam (Existing)
        $kolam = $penjualan->kolam;
        if ($kolam) {
            $kolam->update([
                'status' => 'panen',
                'tanggal_panen' => $penjualan->tanggal_penjualan
            ]);
        }

        // 2. Tambah Saldo Modal (Pemasukan)
        $modal = \App\Models\Modal::getSingleton();
        $modal->tambahModal($penjualan->total_harga);

        // 3. Buat Jurnal Otomatis (Penjualan & HPP)
        
        $akunKas = \App\Models\Akun::where('nama', 'Kas')->first();
        $akunPendapatan = \App\Models\Akun::where('nama', 'Pendapatan Penjualan')->first();
        
        // --- 3a. Jurnal Penjualan (Pemasukan) ---
        if ($akunKas && $akunPendapatan) {
            $jurnalJual = \App\Models\Jurnal::create([
                'tanggal' => $penjualan->tanggal_penjualan,
                'keterangan' => 'Penjualan Panen Kolam #' . ($kolam->nomor_kolam ?? '?'),
                'ref_type' => Penjualan::class,
                'ref_id' => $penjualan->id
            ]);

            // Debit Kas
            \App\Models\JurnalDetail::create([
                'jurnal_id' => $jurnalJual->id,
                'akun_id' => $akunKas->id,
                'debit' => $penjualan->total_harga,
                'kredit' => 0
            ]);

            // Kredit Pendapatan
            \App\Models\JurnalDetail::create([
                'jurnal_id' => $jurnalJual->id,
                'akun_id' => $akunPendapatan->id,
                'debit' => 0,
                'kredit' => $penjualan->total_harga
            ]);
        }

        // --- 3b. Jurnal HPP (Pengeluaran Pokok) ---
        // Logic: Calculate Actual Cost per Fish for this Pond
        if ($kolam) {
            $pondAccountName = "Persediaan Ikan Kolam " . $kolam->nomor_kolam;
            $akunPond = \App\Models\Akun::where('nama', $pondAccountName)->first();
            $akunHPP = \App\Models\Akun::where('nama', 'Beban Pokok Penjualan')->first();
            
            if ($akunPond && $akunHPP) {
                // Use Centralized Yield Costing Logic from Model
                // Note: calcHppPerEkor uses current $kolam->jumlah (which includes sold fish at this point)
                $hppPerUnit = $kolam->calculateHppPerEkor();
                
                $totalHPP = $hppPerUnit * $penjualan->jumlah_ekor;
                
                // Decrement Stock Logic (Important for Partial Harvest)
                $kolam->decrement('jumlah', $penjualan->jumlah_ekor);

                if ($totalHPP > 0) {
                    $jurnalHPP = \App\Models\Jurnal::create([
                        'tanggal' => $penjualan->tanggal_penjualan,
                        'keterangan' => 'HPP Penjualan Kolam #' . $kolam->nomor_kolam . ' (' . $penjualan->jumlah_ekor . ' ekor)',
                        'ref_type' => Penjualan::class,
                        'ref_id' => $penjualan->id
                    ]);

                    // Debit HPP
                    \App\Models\JurnalDetail::create([
                        'jurnal_id' => $jurnalHPP->id,
                        'akun_id' => $akunHPP->id,
                        'debit' => $totalHPP,
                        'kredit' => 0
                    ]);

                    // Kredit Persediaan Ikan (Asset Reduction)
                    \App\Models\JurnalDetail::create([
                        'jurnal_id' => $jurnalHPP->id,
                        'akun_id' => $akunPond->id,
                        'debit' => 0,
                        'kredit' => $totalHPP
                    ]);
                }
            }
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
