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
        $akunKas = \App\Models\Akun::where('nama', 'Kas')->first();
        $akunModal = \App\Models\Akun::where('nama', 'Modal')->first();

        if ($akunKas && $akunModal) {
            $jurnal = \App\Models\Jurnal::create([
                'tanggal' => $setoran->tanggal,
                'keterangan' => 'Setoran Modal: ' . ($setoran->keterangan ?? 'Setoran'),
                'ref_type' => \App\Models\SetoranModal::class,
                'ref_id' => $setoran->id,
            ]);

            \App\Models\JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $akunKas->id,
                'debit' => $setoran->jumlah,
                'kredit' => 0,
            ]);

            \App\Models\JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $akunModal->id,
                'debit' => 0,
                'kredit' => $setoran->jumlah,
            ]);
        }

        return redirect()->route('modal.index')
            ->with('success', 'Setoran modal berhasil ditambahkan');
    }

}
