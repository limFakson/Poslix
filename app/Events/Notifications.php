<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Notifications implements ShouldBroadcastNow {
    use Dispatchable, SerializesModels;

    public $notiData;
    public $method;
    public $tenantId;

    /**
    * Create a new event instance.
    */

    public function __construct( $notiData, $method, $tenantId ) {
        $this->notiData = $notiData;
        $this->method = $method;
        $this->tenantId = $tenantId;
    }

    /**
    * Get the channels the event should broadcast on.
    *
    * @return array<int, \Illuminate\Broadcasting\Channel>
    */

    public function broadcastOn() {
        return new Channel( 'notify' );
    }

    public function broadcastWith() {
        return [
            'method' => $this->method,
            'tenantId' => $this->tenantId,
            'notification' => $this->notiData
        ];
    }
}