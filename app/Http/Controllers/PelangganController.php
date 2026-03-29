<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::latest()->get();
        return view('pelanggan.index', compact('pelanggans'));
    }

    public function create()
    {
        return view('pelanggan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'tipe' => 'required|string', // Umum/Tengkulak
        ]);

        Pelanggan::create($validated);

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan');
    }

    public function edit(Pelanggan $pelanggan)
    {
        return view('pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
         $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'tipe' => 'required|string',
        ]);
        
        $pelanggan->update($validated);
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil diupdate');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        $pelanggan->delete();
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan dihapus');
    }
}
