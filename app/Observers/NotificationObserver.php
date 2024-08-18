<?php

namespace App\Observers;

use App\Http\Resources\Api\NotificationResource;
use App\Events\Notifications as NotifyEvent;
use Illuminate\Support\Facades\Config;
use App\Models\MenuNotification;

class NotificationObserver {

    /**
    * Handle the MenuNotification 'created' event.
    */

    public function created( MenuNotification $menuNotification ): void {
        $tenantId = Config::get( 'tenant_id' );
        event( new NotifyEvent( new NotificationResource( $menuNotification ), 'created', $tenantId ) );
    }

    /**
    * Handle the MenuNotification 'updated' event.
    */

    public function updated( MenuNotification $menuNotification ): void {
        $tenantId = Config::get( 'tenant_id' );
        event( new NotifyEvent( new NotificationResource( $menuNotification ), 'updated', $tenantId ) );
    }

    /**
    * Handle the MenuNotification 'deleted' event.
    */

    public function deleted( MenuNotification $menuNotification ): void {
        $tenantId = Config::get( 'tenant_id' );
        event( new NotifyEvent( new NotificationResource( $menuNotification ), 'deleted', $tenantId ) );
    }

    /**
    * Handle the MenuNotification 'restored' event.
    */

    public function restored( MenuNotification $menuNotification ): void {
        $tenantId = Config::get( 'tenant_id' );
        event( new NotifyEvent( new NotificationResource( $menuNotification ), 'restored', $tenantId ) );
    }

    /**
    * Handle the MenuNotification 'force deleted' event.
    */

    public function forceDeleted( MenuNotification $menuNotification ): void {
        $tenantId = Config::get( 'tenant_id' );
        event( new NotifyEvent( new NotificationResource( $menuNotification ), 'forceDelete', $tenantId ) );
    }
}