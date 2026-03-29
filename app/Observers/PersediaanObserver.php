<?php

namespace App\Observers;

use App\Models\Persediaan;

class PersediaanObserver
{
    /**
     * Handle the Persediaan "saving" event.
     * This runs before creating or updating.
     */
    public function saving(Persediaan $persediaan): void
    {
        // Enforce Logic: Sisa = Masuk - Keluar
        $persediaan->stok_sisa = $persediaan->stok_masuk - $persediaan->stok_keluar;
    }
}
