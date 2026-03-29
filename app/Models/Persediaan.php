<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Persediaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_barang',
        'kategori',
        'satuan',
        'stok_masuk',
        'stok_keluar',
        'stok_sisa',
    ];

    public function pembelians(): HasMany
    {
        return $this->hasMany(Pembelian::class);
    }
}
