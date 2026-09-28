<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kolam extends Model
{
    use HasFactory;

    protected $table = 'kolam'; // Force table name per user migration

    protected $fillable = [
        'nomor_kolam',
        'persediaan_id',
        'tanggal_tebar',
        'tanggal_panen',
        'jumlah', // Saldo Stock Saat Ini
        'jumlah_tebar', // Total Tebar Awal (History)
        'ukuran_ikan',
        'status', // masuk_bibit, dalam_pemeliharaan, sortir, panen
    ];

    protected $casts = [
        'tanggal_tebar' => 'date',
        'tanggal_panen' => 'date',
    ];

    // Relasi ke bibit (Persediaan)
    public function persediaan()
    {
        return $this->belongsTo(Persediaan::class);
    }

    // Relasi ke penjualan (panen)
    public function penjualans()
    {
        return $this->hasMany(Penjualan::class);
    }

    // Helper Method for HPP Logic (Yield Costing)
    public function calculateHppPerEkor()
    {
        $pondAccountName = "Persediaan Bibit Kolam " . $this->nomor_kolam;
        $coaPond = \App\Models\Coa::where('nama', $pondAccountName)->first();

        if (!$coaPond) return 0;

        // 1. Net Asset Value (Debit - Credit)
        $debitTotal = \App\Models\JurnalDetail::where('coa_id', $coaPond->id)->sum('debit');
        $kreditTotal = \App\Models\JurnalDetail::where('coa_id', $coaPond->id)->sum('kredit');
        $netValue = $debitTotal - $kreditTotal;

        // 2. Add Back Death Credits (Yield Logic: Survivors absorb death cost)
        $deathCredits = \App\Models\JurnalDetail::where('coa_id', $coaPond->id)
            ->whereHas('jurnal', function($q) {
                $q->where('ref_type', \App\Models\KematianIkan::class);
            })
            ->sum('kredit');

        $totalCostPool = $netValue + $deathCredits;
        
        // 3. Divide by Current Live Stock
        // Note: usage context matters. If used BEFORE stock decrement, use jumlah.
        if ($this->jumlah > 0) {
            return $totalCostPool / $this->jumlah;
        }

        return 0;
    }

    // Relasi ke kematian ikan
    public function kematianIkans()
    {
        return $this->hasMany(KematianIkan::class);
    }
}
