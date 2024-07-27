<?php

namespace App\Observers;

use App\Models\Sale;
use App\Events\Sale as SaleEvent;

class SaleObserver
{
    /**
     * Handle the Sale "created" event.
     */
    public function created(Sale $sale): void
    {
        event(new SaleEvent($sale, 'created'));
    }

    /**
     * Handle the Sale "updated" event.
     */
    public function updated(Sale $sale): void
    {
        event(new SaleEvent($sale, 'updated'));
    }

    /**
     * Handle the Sale "deleted" event.
     */
    public function deleted(Sale $sale): void
    {
        event(new SaleEvent($sale, 'deleted'));
    }

    /**
     * Handle the Sale "restored" event.
     */
    public function restored(Sale $sale): void
    {
        event(new SaleEvent($sale, 'restored'));
    }

    /**
     * Handle the Sale "force deleted" event.
     */
    public function forceDeleted(Sale $sale): void
    {
        event(new SaleEvent($sale, 'forceDeleted'));
    }
}