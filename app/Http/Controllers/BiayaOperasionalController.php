<?php

namespace App\Http\Controllers;

use App\Models\BiayaOperasional;
use App\Models\Modal;
use Illuminate\Http\Request;

class BiayaOperasionalController extends Controller
{
    public function index()
    {
        $biayas = BiayaOperasional::latest()->paginate(10);
        $saldoModal = Modal::getSingleton()->saldo_modal;
        return view('biaya_operasional.index', compact('biayas', 'saldoModal'));
    }

    public function create()
    {
        $saldoModal = Modal::getSingleton()->saldo_modal;
        return view('biaya_operasional.create', compact('saldoModal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'kategori' => 'required|string', // Listrik, Gaji, dll
        ]);

        // 1. Validasi Saldo Modal
        $modal = Modal::getSingleton();
        if ($modal->saldo_modal < $request->jumlah) {
            return back()
                ->withInput()
                ->withErrors(['saldo' => 'Saldo Modal tidak cukup. Sisa Saldo: Rp ' . number_format($modal->saldo_modal, 0, ',', '.')]);
        }

        // 2. Simpan Data (Observer akan menangani pengurangan saldo & jurnal)
        BiayaOperasional::create($request->all());

        return redirect()->route('biaya-operasional.index')->with('success', 'Biaya Operasional berhasil dicatat.');
    }
}
