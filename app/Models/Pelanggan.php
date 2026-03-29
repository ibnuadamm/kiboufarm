<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'nomor_telepon',
        'alamat',
        'tipe'
    ];

    public function penjualans()
    {
        return $this->hasMany(Penjualan::class);
    }
}
