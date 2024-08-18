<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use DB;
use Illuminate\Support\Facades\URL;
use App\Models\Sale;
use App\Models\MenuNotification;
use App\Observers\SaleObserver;
use App\Observers\NotificationObserver;

class AppServiceProvider extends ServiceProvider {
    /**
    * Bootstrap any application services.
    *
    * @return void
    */

    /**
    * Register any application services.
    *
    * @return void
    */

    public function register() {
        //
    }

    public function boot() {
        // Schema::defaultStringLength( 191 );
        Sale::observe( SaleObserver::class );
        MenuNotification::observe( NotificationObserver::class );
    }

}