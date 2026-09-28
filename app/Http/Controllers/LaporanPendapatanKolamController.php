<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kolam;
use App\Models\Penjualan;
use App\Models\Coa;
use App\Models\JurnalDetail;
use Carbon\Carbon;

class LaporanPendapatanKolamController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $kolamIdFilter = $request->input('kolam_id', 'all');

        $kolamQuery = Kolam::query();
        
        if ($kolamIdFilter !== 'all') {
            $kolamQuery->where('id', $kolamIdFilter);
        }

        $kolams = $kolamQuery->get();
        
        $laporanData = [];

        foreach ($kolams as $kolam) {
            // 1. Pendapatan (Diambil dari penjualan)
            $pendapatan = Penjualan::where('kolam_id', $kolam->id)
                ->whereBetween('tanggal_penjualan', [$startDate, $endDate])
                ->sum('total_harga');

            // Helper for fetching Beban dari Jurnal
            $getBeban = function($kategori) use ($kolam, $startDate, $endDate) {
                $coaName = "Beban {$kategori} Kolam " . $kolam->nomor_kolam;
                $coa = Coa::where('nama', $coaName)->first();
                if (!$coa) return 0;

                return JurnalDetail::where('coa_id', $coa->id)
                    ->whereHas('jurnal', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal', [$startDate, $endDate]);
                    })
                    ->selectRaw('SUM(debit) - SUM(kredit) as total')
                    ->value('total') ?? 0;
            };

            // 2. Beban
            $bebanVitamin = $getBeban('Vitamin');
            $bebanObat = $getBeban('Obat');
            $bebanPakan = $getBeban('Pakan');

            // 2b. Beban Kematian Ikan
            $kematianIds = \App\Models\KematianIkan::where('kolam_id', $kolam->id)->pluck('id');
            $bebanKematian = \App\Models\JurnalDetail::whereHas('coa', function($q) {
                    $q->where('nama', 'Beban Kematian Ikan')->orWhere('nama', 'Susut Ikan');
                })
                ->whereHas('jurnal', function($q) use ($startDate, $endDate, $kematianIds) {
                    $q->whereBetween('tanggal', [$startDate, $endDate])
                      ->where('ref_type', \App\Models\KematianIkan::class)
                      ->whereIn('ref_id', $kematianIds);
                })->sum('debit');

            // 2c. Beban Bibit (Pembelian)
            $bebanBibit = \App\Models\Pembelian::whereHas('persediaan', function ($q) {
                    $q->where('kategori', 'Bibit');
                })
                ->where('kolam_id', $kolam->id)
                ->whereBetween('tanggal_pembelian', [$startDate, $endDate])
                ->sum('total_harga');

            $totalBeban = $bebanVitamin + $bebanObat + $bebanPakan + $bebanKematian + $bebanBibit;
            
            // 3. Pendapatan Bersih
            $pendapatanBersih = $pendapatan - $totalBeban;

            // Only show if there is any activity (pendapatan or beban > 0)
            if ($pendapatan > 0 || $totalBeban > 0) {
                $laporanData[] = [
                    'kolam' => $kolam,
                    'pendapatan' => $pendapatan,
                    'beban_vitamin' => $bebanVitamin,
                    'beban_obat' => $bebanObat,
                    'beban_pakan' => $bebanPakan,
                    'beban_kematian' => $bebanKematian,
                    'beban_bibit' => $bebanBibit,
                    'total_beban' => $totalBeban,
                    'pendapatan_bersih' => $pendapatanBersih
                ];
            } else if ($kolamIdFilter !== 'all') {
                // If explicitly filtered to this kolam, show it even if 0
                 $laporanData[] = [
                    'kolam' => $kolam,
                    'pendapatan' => 0,
                    'beban_vitamin' => 0,
                    'beban_obat' => 0,
                    'beban_pakan' => 0,
                    'beban_kematian' => 0,
                    'beban_bibit' => 0,
                    'total_beban' => 0,
                    'pendapatan_bersih' => 0
                ];
            }
        }

        $allKolams = Kolam::all();

        return view('laporan_pendapatan_kolam.index', compact(
            'laporanData', 
            'startDate', 
            'endDate', 
            'kolamIdFilter',
            'allKolams'
        ));
    }
}
