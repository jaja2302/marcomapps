<?php

namespace App\Observers;

use App\Models\Pengguna;

class InvoiceObserver
{
    /**
     * Handle the Pengguna "created" event.
     */
    public function created(Pengguna $pengguna): void
    {
        //
    }

    /**
     * Handle the Pengguna "updated" event.
     */
    public function updated(Pengguna $pengguna): void
    {
        //
    }

    /**
     * Handle the Pengguna "deleted" event.
     */
    public function deleted(Pengguna $pengguna): void
    {
        //
    }

    /**
     * Handle the Pengguna "restored" event.
     */
    public function restored(Pengguna $pengguna): void
    {
        //
    }

    /**
     * Handle the Pengguna "force deleted" event.
     */
    public function forceDeleted(Pengguna $pengguna): void
    {
        //
    }
}
