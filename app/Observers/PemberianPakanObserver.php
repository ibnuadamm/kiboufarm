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
            $persediaan->stok_sisa = $persediaan->stok_masuk - $persediaan->stok_keluar;
            $persediaan->save();
            
            // 2. Determine Cost (FIFO Logic)
            $totalExpense = 0;
            $remainingToCost = $pemberianPakan->jumlah;

            $pastUsages = \App\Models\PemberianPakan::where('persediaan_id', $persediaan->id)
                ->where('id', '<', $pemberianPakan->id)
                ->sum('jumlah');

            $purchases = \App\Models\Pembelian::where('persediaan_id', $persediaan->id)
                ->orderBy('tanggal_pembelian', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $skippedUsage = $pastUsages;

            foreach ($purchases as $purchase) {
                if ($remainingToCost <= 0) break;

                $purchaseQty = $purchase->jumlah;

                if ($skippedUsage >= $purchaseQty) {
                    $skippedUsage -= $purchaseQty;
                    continue;
                }

                $availableQty = $purchaseQty - $skippedUsage;
                $skippedUsage = 0; 
                
                $pricePerUnit = $purchaseQty > 0 ? ($purchase->total_harga / $purchaseQty) : 0;
                
                $takeQty = min($availableQty, $remainingToCost);
                $totalExpense += ($takeQty * $pricePerUnit);
                $remainingToCost -= $takeQty;
            }

            if ($remainingToCost > 0) {
                 $latestPurchase = \App\Models\Pembelian::where('persediaan_id', $persediaan->id)
                    ->latest('tanggal_pembelian')
                    ->first();
                 $fallbackPrice = 0;
                 if ($latestPurchase && $latestPurchase->jumlah > 0) {
                     $fallbackPrice = $latestPurchase->total_harga / $latestPurchase->jumlah;
                 }
                 $totalExpense += ($remainingToCost * $fallbackPrice);
            }
            
            \Illuminate\Support\Facades\Log::info('PemberianPakan FIFO calculation', [
                'persediaan_id' => $persediaan->id, 
                'totalExpense' => $totalExpense,
                'pastUsages' => $pastUsages
            ]);

            // 3. Create Journal
            if ($totalExpense > 0) {
                $kategori = $persediaan->kategori; // 'Pakan', 'Obat', 'Vitamin'
                
                // DEBIT SIDE: Beban Pakan/Obat/Vitamin Kolam X
                $debitAccountName = "Beban {$kategori}";
                if ($kolam) {
                    $debitAccountName = "Beban {$kategori} Kolam " . $kolam->nomor_kolam;
                }
                
                // CREDIT SIDE: Persediaan Pakan/Obat/Vitamin
                $creditAccountName = "Persediaan {$kategori}";
                if ($kategori === 'Bibit') {
                    $creditAccountName = "Persediaan Bibit"; 
                    if ($kolam) {
                        $creditAccountName = "Persediaan Bibit Kolam " . $kolam->nomor_kolam;
                    }
                }

                $coaDebit = \App\Models\Coa::where('nama', $debitAccountName)->first();
                $coaKredit = \App\Models\Coa::where('nama', $creditAccountName)->first();

                if (!$coaDebit) {
                    $kodeBeban = '51';
                    if ($kategori === 'Vitamin') $kodeBeban = '511';
                    elseif ($kategori === 'Obat') $kodeBeban = '512';
                    elseif ($kategori === 'Pakan') $kodeBeban = '513';
                    
                    if ($kolam) {
                        $kodeBeban .= $kolam->nomor_kolam;
                    } else {
                        $kodeBeban .= '0'; 
                    }

                    $coaDebit = \App\Models\Coa::create([
                        'kode' => $kodeBeban,
                        'nama' => $debitAccountName,
                        'tipe' => 'Expense',
                    ]);
                }

                if (!$coaKredit) {
                    $kodePersediaan = '1130';
                    if ($kategori === 'Vitamin') $kodePersediaan = '1132';
                    elseif ($kategori === 'Obat') $kodePersediaan = '1133';
                    elseif ($kategori === 'Pakan') $kodePersediaan = '1134';

                    $coaKredit = \App\Models\Coa::create([
                        'kode' => $kodePersediaan,
                        'nama' => $creditAccountName,
                        'tipe' => 'Asset',
                    ]);
                }

                if ($coaDebit && $coaKredit) {
                     $jurnal = \App\Models\Jurnal::create([
                        'tanggal' => $pemberianPakan->tanggal,
                        'keterangan' => 'Pemakaian ' . $persediaan->nama_barang . ' untuk Kolam ' . ($kolam ? $kolam->nomor_kolam : '-'),
                        'ref_type' => \App\Models\PemberianPakan::class,
                        'ref_id' => $pemberianPakan->id,
                     ]);

                    // Debit Beban Kolam X
                    \App\Models\JurnalDetail::create([
                        'jurnal_id' => $jurnal->id,
                        'coa_id' => $coaDebit->id,
                        'debit' => $totalExpense,
                        'kredit' => 0,
                    ]);

                    // Kredit Persediaan Pakan/Obat/Vitamin
                    \App\Models\JurnalDetail::create([
                        'jurnal_id' => $jurnal->id,
                        'coa_id' => $coaKredit->id,
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

    /**
     * Handle the PemberianPakan "updated" event.
     */
    public function updated(PemberianPakan $pemberianPakan): void
    {
        //
    }

    /**
     * Handle the PemberianPakan "deleted" event.
     */
    public function deleted(PemberianPakan $pemberianPakan): void
    {
        //
    }

    /**
     * Handle the PemberianPakan "restored" event.
     */
    public function restored(PemberianPakan $pemberianPakan): void
    {
        //
    }

    /**
     * Handle the PemberianPakan "force deleted" event.
     */
    public function forceDeleted(PemberianPakan $pemberianPakan): void
    {
        //
    }
}
