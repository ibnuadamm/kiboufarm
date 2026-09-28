<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    use HasFactory;

    protected $table = 'coas';

    protected $fillable = ['kode', 'nama', 'tipe'];

    public function jurnalDetails()
    {
        return $this->hasMany(JurnalDetail::class, 'coa_id');
    }
}
