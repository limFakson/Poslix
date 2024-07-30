<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashRegisterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'cashInHand'=>$this->cash_in_hand,
            'userId'=>$this->user_id,
            'userName'=>$this->user_name,
            'warehouseId'=>$this->warehouse_id,
            'warehouseName'=>$this->warehouse_name,
            'status'=>$this->status,
            'createdAt'=>$this->created_at,
            'updatedAt'=>$this->updated_at,
        ];
    }
}
