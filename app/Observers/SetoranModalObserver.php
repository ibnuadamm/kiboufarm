<?php

namespace App\Observers;

use App\Models\SetoranModal;

class SetoranModalObserver
{
    /**
     * Handle the SetoranModal "created" event.
     */
    public function created(SetoranModal $setoranModal): void
    {
        // Logic moved to ModalController::store to avoid double execution
        // and ensure robustness.
    }

    /**
     * Handle the SetoranModal "updated" event.
     */
    public function updated(SetoranModal $setoranModal): void
    {
        //
    }

    /**
     * Handle the SetoranModal "deleted" event.
     */
    public function deleted(SetoranModal $setoranModal): void
    {
        //
    }

    /**
     * Handle the SetoranModal "restored" event.
     */
    public function restored(SetoranModal $setoranModal): void
    {
        //
    }

    /**
     * Handle the SetoranModal "force deleted" event.
     */
    public function forceDeleted(SetoranModal $setoranModal): void
    {
        //
    }
}
