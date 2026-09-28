<?php

namespace App\Http\Controllers;

use App\Models\Kolam;
use App\Models\Persediaan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KolamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Load with relations and sum of deaths
        $kolams = Kolam::with(['persediaan'])
            ->withSum('kematianIkans as total_mati', 'jumlah_ekor')
            ->get();
            
        return view('kolam.index', compact('kolams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kolam.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_kolam' => 'required|integer|unique:kolam,nomor_kolam',
        ]);

        Kolam::create([
            'nomor_kolam' => $request->nomor_kolam,
            'persediaan_id' => null,
            'tanggal_tebar' => null,
            'jumlah' => 0,
            'jumlah_tebar' => 0,
            'jumlah_mati' => 0,
            'ukuran_ikan' => null,
            'status' => 'kosong', // Default status
        ]);

        return redirect()->route('kolam.index')
            ->with('success', 'Kolam baru berhasil didaftarkan dengan status Kosong.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kolam $kolam)
    {
        return view('kolam.show', compact('kolam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kolam $kolam)
    {
        return view('kolam.edit', compact('kolam'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kolam $kolam)
    {
        $request->validate([
            'status' => 'required|in:panen',
        ]);

        $updateData = [
            'status' => 'panen',
        ];

        // Jika status sebelumnya bukan panen, isi tanggal_panen otomatis dengan hari ini
        if ($kolam->status !== 'panen') {
            $updateData['tanggal_panen'] = now()->toDateString();
        }

        $kolam->update($updateData);

        return redirect()->route('kolam.index')
            ->with('success', 'Status kolam berhasil diubah menjadi Panen.');
    }

}
