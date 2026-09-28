<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\JurnalDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BukuBesarController extends Controller
{
    public function index(Request $request)
    {
        $coas = Coa::orderBy('kode')->get();
        $selectedCoa = null;
        $details = collect();
        $saldoAwal = 0;
        
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        if ($request->has('coa_id') && $request->coa_id) {
            $selectedCoa = Coa::find($request->coa_id);
            
            if ($selectedCoa) {
                $isDebitNormal = in_array($selectedCoa->tipe, ['Asset', 'Expense']);

                $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
                $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth();

                // Calculate Saldo Awal
                $saldoAwalQuery = JurnalDetail::where('coa_id', $selectedCoa->id)
                    ->whereHas('jurnal', function($q) use ($startDate) {
                        $q->where('tanggal', '<', $startDate);
                    })->get();

                $totalDebitAwal = $saldoAwalQuery->sum('debit');
                $totalKreditAwal = $saldoAwalQuery->sum('kredit');

                if ($isDebitNormal) {
                    $saldoAwal = $totalDebitAwal - $totalKreditAwal;
                } else {
                    $saldoAwal = $totalKreditAwal - $totalDebitAwal;
                }

                // Get transactions for the current month
                $query = JurnalDetail::with(['jurnal', 'coa'])
                    ->where('coa_id', $selectedCoa->id)
                    ->whereHas('jurnal', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('tanggal', [$startDate, $endDate]);
                    });
                
                $details = $query->get()->sortBy(function($detail) {
                    return $detail->jurnal->tanggal . '-' . $detail->jurnal->created_at;
                });
            }
        }

        return view('buku_besar.index', compact('coas', 'selectedCoa', 'details', 'saldoAwal', 'bulan', 'tahun'));
    }
}
