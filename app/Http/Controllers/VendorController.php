<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendors = Vendor::latest()->get();
        return view('vendor.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vendor.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'deskripsi' => 'nullable|string',
        ]);

        Vendor::create($validated);

        return redirect()->route('vendor.index')
            ->with('success', 'Vendor berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vendor $vendor)
    {
        return view('vendor.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor)
    {
        return view('vendor.edit', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'deskripsi' => 'nullable|string',
        ]);

        $vendor->update($validated);

        return redirect()->route('vendor.index')
            ->with('success', 'Data Vendor berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('vendor.index')
            ->with('success', 'Vendor berhasil dihapus');
    }
}
