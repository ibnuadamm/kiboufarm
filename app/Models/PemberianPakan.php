<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemberianPakan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kolam_id',
        'persediaan_id',
        'tanggal',
        'jumlah',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function kolam()
    {
        return $this->belongsTo(Kolam::class, 'kolam_id', 'id'); // Explicit keys just in case
    }

    public function persediaan()
    {
        return $this->belongsTo(Persediaan::class);
    }
}
