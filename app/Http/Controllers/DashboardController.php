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
        
        $summary = [
            'total_omset' => $penjualanBulanIni->sum('total_harga'),
            'total_transaksi' => $penjualanBulanIni->count(),
            'total_ikan_terjual' => $penjualanBulanIni->sum('jumlah_ekor'),
        ];

        // 2. Grafik Kematian Ikan (Harian di Bulan Ini)
        // Group by Date
        $kematianDaily = KematianIkan::select(
                DB::raw('DATE(tanggal) as date'), 
                DB::raw('SUM(jumlah_ekor) as total')
            )
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Prepare data for Chart.js
        // We want to ensure every day is represented even if 0? 
        // Or just show days with data? User said "setiap hari di bulan saat ini".
        // It's better to show all days including 0 for a better line chart.
        
        $labels = [];
        $data = [];
        
        $currentDate = $startOfMonth->copy();
        while ($currentDate <= $endOfMonth) {
            $dateString = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('d M'); // 01 Jan
            
            $record = $kematianDaily->firstWhere('date', $dateString);
            $data[] = $record ? $record->total : 0;
            
            $currentDate->addDay();
        }

        return view('dashboard.index', compact('summary', 'labels', 'data'));
    }
}
