<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this ->id,
            'code'=>$this ->code,
            'type'=>$this ->type,
            'amount'=>$this ->amount,
            'minimumAmount'=>$this ->minimum_amount,
            'quantity'=>$this ->quantity,
            'userId'=>$this ->user_id,
            'userName'=>$this ->user_name,
            'used'=>$this ->used,
            'expiredDate'=>$this ->expired_date,
            'isActive'=>$this ->is_active,
        ];
    }
}