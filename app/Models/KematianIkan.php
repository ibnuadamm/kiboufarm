<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KematianIkan extends Model
{
    use HasFactory;

    protected $table = 'kematian_ikans'; // Ensure plural

    protected $fillable = [
        'kolam_id',
        'jumlah_ekor',
        'tanggal',
        'catatan'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function kolam()
    {
        return $this->belongsTo(Kolam::class);
    }
}
