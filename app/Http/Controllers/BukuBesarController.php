<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\JurnalDetail;
use Illuminate\Http\Request;

class BukuBesarController extends Controller
{
    public function index(Request $request)
    {
        $akuns = Akun::orderBy('kode')->get();
        $selectedAkun = null;
        $details = collect();
        
        if ($request->has('akun_id') && $request->akun_id) {
            $selectedAkun = Akun::find($request->akun_id);
            
            if ($selectedAkun) {
                $query = JurnalDetail::with(['jurnal'])
                    ->where('akun_id', $selectedAkun->id)
                    ->whereHas('jurnal', function($q) use ($request) {
                        if ($request->start_date) {
                            $q->where('tanggal', '>=', $request->start_date);
                        }
                        if ($request->end_date) {
                            $q->where('tanggal', '<=', $request->end_date);
                        }
                    });
                
                // Get all records to calculate running balance correctly
                // Sorting: Oldest first to calculate balance, then we can display newest first if needed?
                // Usually Ledger is displayed Oldest -> Newest.
                $details = $query->get()->sortBy(function($detail) {
                    return $detail->jurnal->tanggal . '-' . $detail->jurnal->created_at;
                });
            }
        }

        return view('buku_besar.index', compact('akuns', 'selectedAkun', 'details'));
    }
}
