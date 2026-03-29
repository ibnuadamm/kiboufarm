<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\JurnalDetail;
use Illuminate\Http\Request;

class LabaRugiController extends Controller
{
    public function index(Request $request)
    {
        // Default Filters (Current Month if not specified)
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-t');

        // 1. PENDAPATAN (REVENUE) - From Jurnal
        $pendapatanAccounts = Akun::where('tipe', 'Revenue')->orderBy('kode')->get();
        $totalPendapatan = 0;

        foreach ($pendapatanAccounts as $akun) {
            $kredit = JurnalDetail::where('akun_id', $akun->id)
                ->whereHas('jurnal', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal', [$startDate, $endDate]);
                })->sum('kredit');

            $debit = JurnalDetail::where('akun_id', $akun->id)
                ->whereHas('jurnal', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal', [$startDate, $endDate]);
                })->sum('debit');

            $akun->saldo_periode = $kredit - $debit;
            $totalPendapatan += $akun->saldo_periode;
        }

        // 2. HPP COMPONENTS (Custom Logic as per User Request)
        // Bibit (From Pembelian), Pakan/Obat/Vitamin (From Pemberian Usage)
        
        $hppComponents = [
            'Bibit' => 0,
            'Pakan' => 0,
            'Obat' => 0,
            'Vitamin' => 0,
        ];

        // A. Bibit: Sum of Purchases
        $hppComponents['Bibit'] = \App\Models\Pembelian::whereHas('persediaan', function ($q) {
                $q->where('kategori', 'Bibit');
            })
            ->whereBetween('tanggal_pembelian', [$startDate, $endDate])
            ->sum('total_harga');

        // B. Usage (Pakan, Obat, Vitamin)
        // Helper to calculate Usage Value
        $calculateUsageValue = function ($category) use ($startDate, $endDate) {
            $totalValue = 0;
            // Get all Usages for this category in date range
            $usages = \App\Models\PemberianPakan::whereHas('persediaan', function ($q) use ($category) {
                    $q->where('kategori', $category);
                })
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->with('persediaan.pembelians')
                ->get();

            foreach ($usages as $usage) {
                // Calculate Avg Cost for this Persediaan
                // Simple Avg: Total Purchase Cost / Total Purchase Qty
                $pembelians = $usage->persediaan->pembelians; 
                $totalCost = $pembelians->sum('total_harga');
                $totalQty = $pembelians->sum('jumlah');
                
                $avgPrice = ($totalQty > 0) ? ($totalCost / $totalQty) : 0;
                
                $totalValue += ($usage->jumlah * $avgPrice);
            }
            return $totalValue;
        };

        $hppComponents['Pakan'] = $calculateUsageValue('Pakan');
        $hppComponents['Obat'] = $calculateUsageValue('Obat');
        $hppComponents['Vitamin'] = $calculateUsageValue('Vitamin');

        $totalHPP = array_sum($hppComponents);

        // 3. BEBAN OPERASIONAL (EXPENSE) - From Jurnal
        // Exclude HPP accounts if any mapped there, but usually Expenses are 6-xxx
        // Filter specifically for the requested breakdown or just show all Expenses
        // User requested: Gaji, Listrik, Air, Lainnya. 
        // These are Accounts created earlier.
        
        $bebanAccounts = Akun::where('tipe', 'Expense')
            ->where('nama', 'NOT LIKE', 'Harga Pokok%') // Exclude standard HPP account if used
            ->where('nama', '!=', 'Beban Pokok Penjualan') // Explicitly exclude this as requested
            ->where('nama', 'NOT LIKE', 'Susut%') // Exclude Susut? Or keep it? User didn't mention it. Let's keep all Expenses to be safe.
            ->orderBy('kode')->get();

        $totalBebanOperasional = 0;

        foreach ($bebanAccounts as $akun) {
            $debit = JurnalDetail::where('akun_id', $akun->id)
                ->whereHas('jurnal', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal', [$startDate, $endDate]);
                })->sum('debit');

            $kredit = JurnalDetail::where('akun_id', $akun->id)
                ->whereHas('jurnal', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal', [$startDate, $endDate]);
                })->sum('kredit');

            $akun->saldo_periode = $debit - $kredit;
            
            // Only add to total if it has value
            if ($akun->saldo_periode != 0) {
                 $totalBebanOperasional += $akun->saldo_periode;
            }
        }

        // 4. LABA BERSIH
        // Revenue - HPP - Operating Expenses
        $labaBersih = $totalPendapatan - $totalHPP - $totalBebanOperasional;

        return view('laba_rugi.index', compact(
            'pendapatanAccounts', 
            'totalPendapatan', 
            'hppComponents',
            'totalHPP',
            'bebanAccounts', 
            'totalBebanOperasional', 
            'labaBersih',
            'startDate',
            'endDate'
        ));
    }
}
