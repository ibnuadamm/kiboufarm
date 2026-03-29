<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kolam_id',
        'tanggal_penjualan',
        'pembeli',
        'jumlah_ekor',
        'harga_per_ekor', // Diganti dari harga_per_kg
        'total_harga',
        'catatan',
    ];

    protected $casts = [
        'tanggal_penjualan' => 'date',
        'harga_per_ekor' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    public function kolam()
    {
        return $this->belongsTo(Kolam::class);
    }
}
