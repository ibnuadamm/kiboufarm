<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\JurnalDetail;
use Illuminate\Http\Request;

class LabaRugiController extends Controller
{
    public function index(Request $request)
    {
        // Default Filters (Current Month if not specified)
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-t');

        // 1. PENDAPATAN (REVENUE) - From Pendapatan Bersih Seluruh Kolam
        // Menghitung total penjualan
        $totalPenjualan = \App\Models\Penjualan::whereBetween('tanggal_penjualan', [$startDate, $endDate])->sum('total_harga');

        // Menghitung total beban (Pakan, Obat, Vitamin) untuk seluruh kolam
        $calculateUsageValue = function ($category) use ($startDate, $endDate) {
            $accountNamePrefix = "Beban {$category}%";
            return \App\Models\JurnalDetail::whereHas('coa', function ($q) use ($accountNamePrefix) {
                    $q->where('nama', 'LIKE', $accountNamePrefix); 
                })
                ->whereHas('jurnal', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal', [$startDate, $endDate]);
                })->sum('debit');
        };

        $totalBebanPakan = $calculateUsageValue('Pakan');
        $totalBebanObat = $calculateUsageValue('Obat');
        $totalBebanVitamin = $calculateUsageValue('Vitamin');

        // Tambahan Beban Kematian & Bibit
        $totalBebanKematian = \App\Models\JurnalDetail::whereHas('coa', function($q) {
                $q->where('nama', 'Beban Kematian Ikan')->orWhere('nama', 'Susut Ikan');
            })
            ->whereHas('jurnal', function($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate])
                  ->where('ref_type', \App\Models\KematianIkan::class);
            })->sum('debit');

        $totalBebanBibit = \App\Models\Pembelian::whereHas('persediaan', function ($q) {
                $q->where('kategori', 'Bibit');
            })
            ->whereBetween('tanggal_pembelian', [$startDate, $endDate])
            ->sum('total_harga');

        $totalAllBeban = $totalBebanPakan + $totalBebanObat + $totalBebanVitamin + $totalBebanKematian + $totalBebanBibit;
        $pendapatanBersih = $totalPenjualan - $totalAllBeban;
        // Buat mock object supaya sesuai dengan tampilan blade yang iterasi foreach ($pendapatanAccounts as $coa)
        $pendapatanAccounts = [
            (object) [
                'kode' => '-',
                'nama' => 'Pendapatan Bersih Seluruh Kolam',
                'saldo_periode' => $pendapatanBersih
            ]
        ];

        $totalPendapatan = $pendapatanBersih;

        // 2. HARGA POKOK PENJUALAN (HPP) - Dihapus sesuai request
        $totalHPP = 0;

        // 3. BEBAN OPERASIONAL (EXPENSE) - From Jurnal
        // Hanya Beban Gaji, Beban Listrik, Beban Air sesuai request
        $bebanAccounts = Coa::where('tipe', 'Expense')
            ->where(function ($query) {
                $query->where('nama', 'LIKE', '%Gaji%')
                      ->orWhere('nama', 'LIKE', '%Listrik%')
                      ->orWhere('nama', 'LIKE', '%Air%');
            })
            ->where('nama', 'NOT LIKE', '%Kematian%') // Pastikan tidak ada kematian ikan
            ->orderBy('kode')->get();

        $totalBebanOperasional = 0;

        foreach ($bebanAccounts as $coa) {
            $debit = JurnalDetail::where('coa_id', $coa->id)
                ->whereHas('jurnal', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal', [$startDate, $endDate]);
                })->sum('debit');

            $kredit = JurnalDetail::where('coa_id', $coa->id)
                ->whereHas('jurnal', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal', [$startDate, $endDate]);
                })->sum('kredit');

            $coa->saldo_periode = $debit - $kredit;
            
            if ($coa->saldo_periode != 0) {
                 $totalBebanOperasional += $coa->saldo_periode;
            }
        }

        // 4. LABA BERSIH
        // Revenue - Operating Expenses (HPP is removed)
        $labaBersih = $totalPendapatan - $totalBebanOperasional;

        return view('laba_rugi.index', compact(
            'pendapatanAccounts', 
            'totalPendapatan', 
            'bebanAccounts', 
            'totalBebanOperasional', 
            'labaBersih',
            'startDate',
            'endDate'
        ));
    }
}
