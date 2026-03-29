<?php

namespace App\Http\Controllers;

use App\Models\PemberianPakan;
use App\Models\Kolam;
use App\Models\Persediaan;
use Illuminate\Http\Request;

class PemberianPakanController extends Controller
{
    public function index()
    {
        $pemberians = PemberianPakan::with(['kolam', 'persediaan'])->latest()->paginate(10);
        return view('pemberian_pakan.index', compact('pemberians'));
    }

    public function create()
    {
        $kolams = Kolam::where('status', '!=', 'panen')->get();
        // Filter persediaan: Only Pakan, Obat, Vitamin
        $barangs = Persediaan::whereIn('kategori', ['Pakan', 'Obat', 'Vitamin'])->where('stok_sisa', '>', 0)->get();
        
        return view('pemberian_pakan.create', compact('kolams', 'barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kolam_id' => 'required|exists:kolam,id',
            'persediaan_id' => 'required|exists:persediaans,id',
            'tanggal' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'catatan' => 'nullable|string',
        ]);

        // Validate stock availability
        $barang = Persediaan::find($request->persediaan_id);
        if ($barang->stok_sisa < $request->jumlah) {
            return back()->withInput()->withErrors(['stok' => 'Stok tidak mencukupi. Sisa: ' . $barang->stok_sisa]);
        }

        PemberianPakan::create($request->all());

        return redirect()->route('pemberian-pakan.index')->with('success', 'Data pemberian pakan/obat berhasil dicatat.');
    }
}
