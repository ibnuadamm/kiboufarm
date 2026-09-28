<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Kolam;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penjualans = Penjualan::with('kolam')->latest('tanggal_penjualan')->paginate(10);
        return view('penjualan.index', compact('penjualans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get harvested ponds only for harvest selection
        $kolams = Kolam::where('status', 'panen')->get();
        $pelanggans = \App\Models\Pelanggan::orderBy('nama')->get();
        return view('penjualan.create', compact('kolams', 'pelanggans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kolam_id' => 'required|exists:kolam,id',
            'tanggal_penjualan' => 'required|date',
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'jumlah_ekor' => 'required|integer|min:1',
            'harga_per_ekor' => 'required|numeric|min:0',
            'total_harga' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        $pelanggan = \App\Models\Pelanggan::find($validated['pelanggan_id']);
        $validated['pembeli'] = $pelanggan->nama;

        // Validasi Logic: Jumlah Penjualan <= Jumlah di Kolam
        $kolam = \App\Models\Kolam::find($validated['kolam_id']);
        if ($kolam && $validated['jumlah_ekor'] > $kolam->jumlah) {
            return back()
                ->withInput()
                ->withErrors(['jumlah_ekor' => "Jumlah panen ({$validated['jumlah_ekor']} ekor) melebihi jumlah tebar di kolam ({$kolam->jumlah} ekor)."]);
        }

        // Create Penjualan
        Penjualan::create($validated);

        return redirect()->route('penjualan.index')
            ->with('success', 'Penjualan berhasil dicatat. Status kolam berubah menjadi Panen.');
    }
}
