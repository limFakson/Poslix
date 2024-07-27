<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use App\Observers\SaleObserver;
use App\Models\Sale;
use App\Events\Sale as Sales;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Sale::observe(SaleObserver::class);
    }

}