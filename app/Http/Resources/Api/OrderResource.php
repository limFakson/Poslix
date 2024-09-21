<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource {
    /**
    * Transform the resource into an array.
    *
    * @return array<string, mixed>
    */

    public function toArray( Request $request ): array {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'userId'=>$this->user_id,
            'enableOrder'=>$this->enable_order,
            'enablePayment'=>$this->enable_payments,
            'paymentRequired'=>$this->payment_required
        ];
    }
}