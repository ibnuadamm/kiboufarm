<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Persediaan;
use Illuminate\Http\Request;

class PembelianController extends Controller
{
    public function index()
    {
        $pembelians = Pembelian::with('persediaan')->latest()->paginate(10);
        return view('pembelian.index', compact('pembelians'));
    }

    public function create()
    {
        $persediaans = Persediaan::all();
        $modal = \App\Models\Modal::getSingleton();
        $kolams = \App\Models\Kolam::where('status', '!=', 'panen')->get(); // Existing logic just in case useful
        $vendors = \App\Models\Vendor::all();
        return view('pembelian.create', compact('persediaans', 'modal', 'kolams', 'vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'persediaan_id' => 'required|exists:persediaans,id',
            'vendor_id' => 'required|exists:vendors,id',
            'jumlah' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'tanggal_pembelian' => 'required|date',
            // Optional Kolam fields (conditional validation manually handled or checked if present)
            'nomor_kolam' => 'nullable|integer',
            'ukuran_ikan' => 'nullable|string'
        ]);

        $validated['total_harga'] = $validated['jumlah'] * $validated['harga_satuan'];

        // Validasi saldo modal
        $modal = \App\Models\Modal::getSingleton();
        if ($modal->saldo_modal < $validated['total_harga']) {
            return back()
                ->withInput()
                ->withErrors(['saldo' => 'Saldo modal tidak cukup. Saldo saat ini: Rp ' . number_format($modal->saldo_modal, 0, ',', '.')]);
        }

        $kolamId = null;

        // Cek Logic Kolam (Bibit)
        $persediaan = \App\Models\Persediaan::find($validated['persediaan_id']);
        
        if ($persediaan && $persediaan->kategori === 'Bibit' && !empty($request->nomor_kolam)) {
            // Cek apakah ada siklus aktif untuk nomor kolam ini
            $existingKolam = \App\Models\Kolam::where('nomor_kolam', $request->nomor_kolam)
                ->where('status', '!=', 'panen')
                ->latest('tanggal_tebar') // Ambil yang paling baru jika ada multiple (should be 1 active)
                ->first();

            if ($existingKolam) {
                // Attach to existing active cycle (Restocking)
                $kolamId = $existingKolam->id;
            } else {
                // Create New Cycle
                $newKolam = \App\Models\Kolam::create([
                    'nomor_kolam' => $request->nomor_kolam,
                    'persediaan_id' => $persediaan->id,
                    'tanggal_tebar' => $validated['tanggal_pembelian'],
                    'jumlah' => 0, // Akan di-increment oleh Observer
                    'jumlah_tebar' => 0, // Akan di-increment oleh Observer
                    'ukuran_ikan' => $request->ukuran_ikan ?? '-',
                    'status' => 'dalam_pemeliharaan'
                ]);
                $kolamId = $newKolam->id;
            }
        }

        $pembelian = Pembelian::create([
            'persediaan_id' => $validated['persediaan_id'],
            'vendor_id' => $validated['vendor_id'],
            'jumlah' => $validated['jumlah'],
            'harga_satuan' => $validated['harga_satuan'],
            'total_harga' => $validated['total_harga'],
            'tanggal_pembelian' => $validated['tanggal_pembelian'],
            'kolam_id' => $kolamId, // Pass ID directly
            'user_id' => 1, // Default user
        ]);

        $message = 'Pembelian berhasil dicatat.';
        if ($kolamId) {
            $message .= ' Stok ditambahkan ke Kolam #' . $request->nomor_kolam . '.';
        }

        return redirect()->route('pembelian.index')->with('success', $message);
    }
}
