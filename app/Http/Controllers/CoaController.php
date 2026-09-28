<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use Illuminate\Http\Request;

class CoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coas = Coa::orderBy('kode', 'asc')->paginate(20);
        return view('coa.index', compact('coas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('coa.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|unique:coas,kode|max:20',
            'nama' => 'required|string|max:255',
            'tipe' => 'required|string',
        ]);

        Coa::create($request->all());

        return redirect()->route('coa.index')->with('success', 'COA berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coa $coa)
    {
        return view('coa.edit', compact('coa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coa $coa)
    {
        $request->validate([
            'kode' => 'required|string|max:20|unique:coas,kode,' . $coa->id,
            'nama' => 'required|string|max:255',
            'tipe' => 'required|string',
        ]);

        $coa->update($request->all());

        return redirect()->route('coa.index')->with('success', 'COA berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coa $coa)
    {
        try {
            $coa->delete();
            return redirect()->route('coa.index')->with('success', 'COA berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()->route('coa.index')->with('error', 'COA tidak dapat dihapus karena sudah memiliki riwayat transaksi di jurnal.');
            }
            throw $e;
        }
    }
}
