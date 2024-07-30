<?php
// SaleObserver.php
namespace App\Observers;

use App\Models\Sale;
use App\Events\Sale as SaleEvent;
use Illuminate\Support\Facades\Config;
use App\Http\Resources\Api\SaleResource;
use App\Http\Resources\Api\ProductSalesCollection;

class SaleObserver
{
    /**
    * Handle the Sale "created" event.
    */
    public function created(Sale $sale): void
    {
        $tenantId = Config::get('tenant_id');
        event(new SaleEvent(new SaleResource($sale), 'created', $tenantId));
    }

    /**
    * Handle the Sale "updated" event.
    */
    public function updated(Sale $sale): void
    {
        $tenantId = Config::get('tenant_id');
        event(new SaleEvent(new SaleResource($sale), 'updated', $tenantId));
    }

    /**
    * Handle the Sale "deleted" event.
    */
    public function deleted(Sale $sale): void
    {
        $tenantId = Config::get('tenant_id');
        event(new SaleEvent(new SaleResource($sale), 'deleted', $tenantId));
    }

    /**
    * Handle the Sale "restored" event.
    */
    public function restored(Sale $sale): void
    {
        $tenantId = Config::get('tenant_id');
        event(new SaleEvent(new SaleResource($sale), 'restored', $tenantId));
    }

    /**
    * Handle the Sale "force deleted" event.
    */
    public function forceDeleted(Sale $sale): void
    {
        $tenantId = Config::get('tenant_id');
        event(new SaleEvent(new SaleResource($sale), 'forceDeleted', $tenantId));
    }
}