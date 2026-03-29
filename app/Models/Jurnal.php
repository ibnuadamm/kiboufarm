<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use HasFactory;

    protected $fillable = ['tanggal', 'keterangan', 'ref_type', 'ref_id'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(JurnalDetail::class);
    }

    public function ref()
    {
        return $this->morphTo();
    }
}
