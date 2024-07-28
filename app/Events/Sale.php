<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Http\Resources\Api\SaleCollection;

class Sale implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $saleData;
    public $method;
    public $tenantId;

    public function __construct($saleData, $method, $tenantId)
    {
        // dd($saleData['id']);
        $this->saleData = $saleData;
        $this->method = $method;
        $this->tenantId = $tenantId;
    }

    public function broadcastOn()
    {
        return new Channel('sales');
    }

    public function broadcastWith()
    {
        return [
            'method' => $this->method,
            'tenantId' => $this->tenantId,
            'sale' => $this->saleData
        ];
    }
}
