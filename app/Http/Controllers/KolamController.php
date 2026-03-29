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
        // Filter persediaan: Category 'Bibit' only
        $bibits = Persediaan::where('kategori', 'Bibit')->get();
        return view('kolam.create', compact('bibits'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_kolam' => [
                'required',
                'integer',
                // Custom validation: check if active cycle exists for this pond number
                function ($attribute, $value, $fail) {
                    $activeCycleExists = Kolam::where('nomor_kolam', $value)
                        ->where('status', '!=', 'panen')
                        ->exists();
                    
                    if ($activeCycleExists) {
                        $fail('Kolam nomor ' . $value . ' masih aktif (belum panen). Tidak bisa membuat siklus baru.');
                    }
                },
            ],
            'persediaan_id' => 'required|exists:persediaans,id',
            'tanggal_tebar' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'ukuran_ikan' => 'required|string|max:255',
        ]);

        Kolam::create([
            'nomor_kolam' => $request->nomor_kolam,
            'persediaan_id' => $request->persediaan_id,
            'tanggal_tebar' => $request->tanggal_tebar,
            'jumlah' => $request->jumlah,
            'ukuran_ikan' => $request->ukuran_ikan,
            'status' => 'masuk_bibit', // Default status
        ]);

        return redirect()->route('kolam.index')
            ->with('success', 'Siklus Kolam berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kolam $kolam)
    {
        return view('kolam.show', compact('kolam'));
    }
    
    // ... (Show method)

}
