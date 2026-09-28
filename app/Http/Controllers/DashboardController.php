<?php

namespace App\Http\Controllers;

use App\Models\KematianIkan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. Ringkasan Penjualan (Bulan Ini)
        $penjualanBulanIni = Penjualan::whereBetween('tanggal_penjualan', [$startOfMonth, $endOfMonth]);
        
        // Menghitung total beban (Pakan, Obat, Vitamin) untuk seluruh kolam di bulan ini
        $calculateUsageValue = function ($category) use ($startOfMonth, $endOfMonth) {
            $accountNamePrefix = "Beban {$category}%";
            return \App\Models\JurnalDetail::whereHas('coa', function ($q) use ($accountNamePrefix) {
                    $q->where('nama', 'LIKE', $accountNamePrefix); 
                })
                ->whereHas('jurnal', function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
                })->sum('debit');
        };

        $totalBebanPakan = $calculateUsageValue('Pakan');
        $totalBebanObat = $calculateUsageValue('Obat');
        $totalBebanVitamin = $calculateUsageValue('Vitamin');
        
        $totalBebanKematian = \App\Models\JurnalDetail::whereHas('coa', function($q) {
                $q->where('nama', 'Beban Kematian Ikan')->orWhere('nama', 'Susut Ikan');
            })
            ->whereHas('jurnal', function($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                  ->where('ref_type', \App\Models\KematianIkan::class);
            })->sum('debit');

        $totalBebanBibit = \App\Models\Pembelian::whereHas('persediaan', function ($q) {
                $q->where('kategori', 'Bibit');
            })
            ->whereBetween('tanggal_pembelian', [$startOfMonth, $endOfMonth])
            ->sum('total_harga');

        $totalBebanOperasional = \App\Models\BiayaOperasional::whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');
        
        $totalOmset = $penjualanBulanIni->sum('total_harga');
        $pendapatanBersih = $totalOmset - ($totalBebanPakan + $totalBebanObat + $totalBebanVitamin + $totalBebanKematian + $totalBebanBibit + $totalBebanOperasional);
        $summary = [
            'total_omset' => $totalOmset,
            'total_transaksi' => $penjualanBulanIni->count(),
            'total_ikan_terjual' => $penjualanBulanIni->sum('jumlah_ekor'),
            'pendapatan_bersih' => $pendapatanBersih,
        ];

        // 2. Grafik Populasi Ikan Hidup (Harian di Bulan Ini)
        // Formula: Total Ikan Hidup = (Total Tebar) - (Total Kematian) - (Total Penjualan)
        
        // 1. Initial Balance before this month
        $initialTebar = \App\Models\Kolam::where('tanggal_tebar', '<', $startOfMonth)->sum('jumlah_tebar');
        $initialKematian = KematianIkan::where('tanggal', '<', $startOfMonth)->sum('jumlah_ekor');
        $initialPenjualan = Penjualan::where('tanggal_penjualan', '<', $startOfMonth)->sum('jumlah_ekor');
        
        $currentBalance = $initialTebar - $initialKematian - $initialPenjualan;

        // 2. Daily Activities in current month
        $tebarDaily = \App\Models\Kolam::select(DB::raw('DATE(tanggal_tebar) as date'), DB::raw('SUM(jumlah_tebar) as total'))
            ->whereBetween('tanggal_tebar', [$startOfMonth, $endOfMonth])
            ->groupBy('date')->get();

        $kematianDaily = KematianIkan::select(DB::raw('DATE(tanggal) as date'), DB::raw('SUM(jumlah_ekor) as total'))
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->groupBy('date')->get();

        $penjualanDaily = Penjualan::select(DB::raw('DATE(tanggal_penjualan) as date'), DB::raw('SUM(jumlah_ekor) as total'))
            ->whereBetween('tanggal_penjualan', [$startOfMonth, $endOfMonth])
            ->groupBy('date')->get();

        $labels = [];
        $data = [];
        $dataKematian = [];
        
        $currentDate = $startOfMonth->copy();
        while ($currentDate <= $endOfMonth) {
            $dateString = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('d M'); // 01 Jan
            
            $tebarToday = $tebarDaily->firstWhere('date', $dateString)->total ?? 0;
            $kematianToday = $kematianDaily->firstWhere('date', $dateString)->total ?? 0;
            $penjualanToday = $penjualanDaily->firstWhere('date', $dateString)->total ?? 0;
            
            $currentBalance += ($tebarToday - $kematianToday - $penjualanToday);
            $data[] = max(0, $currentBalance); // Ensure not negative due to data inconsistency
            $dataKematian[] = $kematianToday;
            
            $currentDate->addDay();
        }

        return view('dashboard.index', compact('summary', 'labels', 'data', 'dataKematian'));
    }
}
