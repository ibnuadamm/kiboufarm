<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use Illuminate\Http\Request;

class JurnalController extends Controller
{
    public function index(Request $request)
    {
        $query = Jurnal::with('details.coa');

        if ($request->start_date) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $jurnals = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->paginate(15);
        return view('jurnal.index', compact('jurnals'));
    }
}
