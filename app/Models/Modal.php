<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modal extends Model
{
    protected $fillable = ['saldo_modal'];

    protected $casts = [
        'saldo_modal' => 'decimal:2',
    ];

    /**
     * Get singleton instance (hanya 1 record)
     */
    public static function getSingleton()
    {
        // Always try to get the first record, create if empty.
        // Don't enforce ID=1 in query to avoid duplication if ID is different.
        $modal = self::first();
        if (!$modal) {
            $modal = self::create(['id' => 1, 'saldo_modal' => 0]);
        }
        return $modal;
    }

    /**
     * Tambah modal
     */
    public function tambahModal($jumlah)
    {
        $this->increment('saldo_modal', $jumlah);
        return $this->fresh();
    }

    /**
     * Kurangi modal
     */
    public function kurangiModal($jumlah)
    {
        $this->decrement('saldo_modal', $jumlah);
        return $this->fresh();
    }

    /**
     * Relasi ke setoran modal
     */
    public function setoranModals()
    {
        return $this->hasMany(SetoranModal::class);
    }
}
