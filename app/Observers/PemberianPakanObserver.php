<?php

namespace App\Observers;

use App\Models\PemberianPakan;

class PemberianPakanObserver
{
    /**
     * Handle the PemberianPakan "created" event.
     */
    public function created(PemberianPakan $pemberianPakan): void
    {
        \Illuminate\Support\Facades\Log::info('PemberianPakanObserver::created triggered', ['id' => $pemberianPakan->id]);

        $persediaan = $pemberianPakan->persediaan;
        $kolam = $pemberianPakan->kolam;

        if ($persediaan) {
            // 1. Update Stok Logic
            $persediaan->stok_keluar += $pemberianPakan->jumlah;
            // Explicitly calculate remaining stock to ensure accuracy immediately
            $persediaan->stok_sisa = $persediaan->stok_masuk - $persediaan->stok_keluar;
            $persediaan->save();
            
            // 2. Determine Cost (Nearest Purchase Logic)
            $costPerUnit = 0;

            // Strategy A: Historical Purchase (Strict)
            $purchase = \App\Models\Pembelian::where('persediaan_id', $persediaan->id)
                ->whereDate('tanggal_pembelian', '<=', $pemberianPakan->tanggal) // Use whereDate only
                ->latest('tanggal_pembelian')
                ->first();

            // Strategy B: Any Purchase (Fallback if strict fails)
            if (!$purchase) {
                $purchase = \App\Models\Pembelian::where('persediaan_id', $persediaan->id)
                    ->latest('tanggal_pembelian')
                    ->first();
            }

            if ($purchase && $purchase->jumlah > 0) {
                $costPerUnit = $purchase->total_harga / $purchase->jumlah;
            } else {
                // Strategy C: Global Average (Last Resort)
                 $globalPembelians = $persediaan->pembelians; // Ensure relationship works
                 if ($globalPembelians->count() > 0) {
                     $globalTotalHarga = $globalPembelians->sum('total_harga');
                     $globalTotalQty = $globalPembelians->sum('jumlah');
    
                     if ($globalTotalQty > 0) {
                         $costPerUnit = $globalTotalHarga / $globalTotalQty;
                     }
                 }
            }

            $totalExpense = $costPerUnit * $pemberianPakan->jumlah;
            
            \Illuminate\Support\Facades\Log::info('PemberianPakan calculation', [
                'persediaan_id' => $persediaan->id, 
                'costPerUnit' => $costPerUnit, 
                'totalExpense' => $totalExpense,
                'found_purchase' => $purchase ? 'yes' : 'no'
            ]);

            // 3. Create Journal
            if ($totalExpense > 0) {
                // DEBIT SIDE: Persediaan Ikan Kolam X (Capitalization)
                $debitAccountName = 'Persediaan Ikan';
                if ($kolam) {
                    $specificAccountName = "Persediaan Ikan Kolam " . $kolam->nomor_kolam;
                    if (\App\Models\Akun::where('nama', $specificAccountName)->exists()) {
                        $debitAccountName = $specificAccountName;
                    }
                }
                
                // CREDIT SIDE: Specific Inventory
                $creditAccountName = 'Persediaan'; // Fallback
                if ($persediaan->kategori === 'Pakan') {
                    $creditAccountName = 'Persediaan Pakan';
                } elseif ($persediaan->kategori === 'Obat') {
                    $creditAccountName = 'Persediaan Obat';
                } elseif ($persediaan->kategori === 'Vitamin') {
                    $creditAccountName = 'Persediaan Vitamin';
                }

                $akunDebit = \App\Models\Akun::where('nama', $debitAccountName)->first();
                $akunKredit = \App\Models\Akun::where('nama', $creditAccountName)->first();

                if ($akunDebit && $akunKredit) {
                     $jurnal = \App\Models\Jurnal::create([
                        'tanggal' => $pemberianPakan->tanggal,
                        'keterangan' => 'Pemakaian ' . $persediaan->nama_barang . ' (Kapitalisasi ke Ikan)',
                        'ref_type' => \App\Models\PemberianPakan::class,
                        'ref_id' => $pemberianPakan->id,
                    ]);

                    // Debit Persediaan Ikan
                    \App\Models\JurnalDetail::create([
                        'jurnal_id' => $jurnal->id,
                        'akun_id' => $akunDebit->id,
                        'debit' => $totalExpense,
                        'kredit' => 0,
                    ]);

                    // Kredit Persediaan Pakan/Obat/Vitamin
                    \App\Models\JurnalDetail::create([
                        'jurnal_id' => $jurnal->id,
                        'akun_id' => $akunKredit->id,
                        'debit' => 0,
                        'kredit' => $totalExpense,
                    ]);
                    
                    \Illuminate\Support\Facades\Log::info('Journal Created', ['jurnal_id' => $jurnal->id]);
                } else {
                    \Illuminate\Support\Facades\Log::warning('Accounts not found', ['debit' => $debitAccountName, 'credit' => $creditAccountName]);
                }
            } else {
                 \Illuminate\Support\Facades\Log::warning('Total Expense is 0, skipping journal', ['costPerUnit' => $costPerUnit]);
            }
        }
    }
}
