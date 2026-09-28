<?php

namespace App\Http\Controllers;

use App\Models\Modal;
use App\Models\SetoranModal;
use Illuminate\Http\Request;

class ModalController extends Controller
{
    /**
     * Display modal balance and setoran history
     */
    public function index()
    {
        $modal = Modal::getSingleton();
        $setorans = SetoranModal::orderBy('tanggal', 'desc')->get();
        
        return view('modal.index', compact('modal', 'setorans'));
    }

    /**
     * Show form for creating new setoran
     */
    public function create()
    {
        return view('modal.create');
    }

    /**
     * Store new setoran modal
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $setoran = SetoranModal::create($validated);

        // MANUAL LOGIC (Observer replacement)
        // 1. Tambah Saldo Modal
        $modal = \App\Models\Modal::getSingleton();
        $modal->tambahModal($setoran->jumlah);

        // 2. Buat Jurnal
        $coaKas = \App\Models\Coa::where('nama', 'Kas')->orWhere('kode', '111')->first();
        if ($coaKas) {
            $coaKas->update(['kode' => '111', 'nama' => 'Kas']);
        } else {
            $coaKas = \App\Models\Coa::create(['kode' => '111', 'nama' => 'Kas', 'tipe' => 'Asset']);
        }

        $coaModal = \App\Models\Coa::where('nama', 'Modal')->orWhere('kode', '311')->first();
        if ($coaModal) {
            $coaModal->update(['kode' => '311', 'nama' => 'Modal']);
        } else {
            $coaModal = \App\Models\Coa::create(['kode' => '311', 'nama' => 'Modal', 'tipe' => 'Equity']);
        }

        if ($coaKas && $coaModal) {
            $jurnal = \App\Models\Jurnal::create([
                'tanggal' => $setoran->tanggal,
                'keterangan' => 'Setoran Modal: ' . ($setoran->keterangan ?? 'Setoran'),
                'ref_type' => \App\Models\SetoranModal::class,
                'ref_id' => $setoran->id,
            ]);

            \App\Models\JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $coaKas->id,
                'debit' => $setoran->jumlah,
                'kredit' => 0,
            ]);

            \App\Models\JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $coaModal->id,
                'debit' => 0,
                'kredit' => $setoran->jumlah,
            ]);
        }

        return redirect()->route('modal.index')
            ->with('success', 'Setoran modal berhasil ditambahkan');
    }

}
