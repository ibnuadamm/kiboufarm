<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetoranModal extends Model
{
    protected $fillable = [
        'jumlah',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];
}
