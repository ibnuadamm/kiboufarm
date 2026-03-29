<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use Illuminate\Http\Request;

class CoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $akuns = Akun::orderBy('kode', 'asc')->paginate(20);
        return view('coa.index', compact('akuns'));
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
            'kode' => 'required|string|unique:akuns,kode|max:20',
            'nama' => 'required|string|max:255',
            'tipe' => 'required|string',
        ]);

        Akun::create($request->all());

        return redirect()->route('coa.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Akun $coa) // Route parameter likely 'coa'
    {
        return view('coa.edit', compact('coa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Akun $coa)
    {
        $request->validate([
            'kode' => 'required|string|max:20|unique:akuns,kode,' . $coa->id,
            'nama' => 'required|string|max:255',
            'tipe' => 'required|string',
        ]);

        $coa->update($request->all());

        return redirect()->route('coa.index')->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Akun $coa)
    {
        // Prevent deletion of system accounts if needed, but for now simple delete
        $coa->delete();
        return redirect()->route('coa.index')->with('success', 'Akun berhasil dihapus.');
    }
}
