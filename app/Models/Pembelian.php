<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembelian extends Model
{
    use HasFactory;

    protected $fillable = [
        'persediaan_id',
        'vendor_id',
        'jumlah',
        'harga_satuan',
        'total_harga',
        'tanggal_pembelian',
        'kolam_id',
        'user_id',
    ];

    protected $casts = [
        'tanggal_pembelian' => 'date',
        'harga_satuan' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    public function persediaan(): BelongsTo
    {
        return $this->belongsTo(Persediaan::class);
    }

    public function kolam(): BelongsTo
    {
        return $this->belongsTo(Kolam::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
