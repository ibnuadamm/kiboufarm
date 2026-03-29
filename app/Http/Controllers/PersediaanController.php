<?php

namespace App\Http\Controllers;

use App\Models\Persediaan;
use Illuminate\Http\Request;

class PersediaanController extends Controller
{
    public function index()
    {
        $persediaans = Persediaan::latest()->paginate(10);
        return view('persediaan.index', compact('persediaans'));
    }

    public function create()
    {
        return view('persediaan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|unique:persediaans,nama_barang',
            'kategori' => 'required|in:Pakan,Obat,Vitamin,Alat,Bibit,Lainnya',
            'satuan' => 'required|string',
        ]);

        Persediaan::create($validated);

        return redirect()->route('persediaan.index')->with('success', 'Master data barang berhasil ditambahkan.');
    }

    public function edit(Persediaan $persediaan)
    {
        return view('persediaan.edit', compact('persediaan'));
    }

    public function update(Request $request, Persediaan $persediaan)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|unique:persediaans,nama_barang,' . $persediaan->id,
            'kategori' => 'required|in:Pakan,Obat,Vitamin,Alat,Bibit,Lainnya',
            'satuan' => 'required|string',
        ]);

        $persediaan->update($validated);

        return redirect()->route('persediaan.index')->with('success', 'Master data barang berhasil diperbarui.');
    }

    public function destroy(Persediaan $persediaan)
    {
        $persediaan->delete();
        return redirect()->route('persediaan.index')->with('success', 'Master data barang berhasil dihapus.');
    }
}
