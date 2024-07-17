<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GiftCardResource extends JsonResource
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
            'cardNo'=>$this ->card_no,
            'amount'=>$this ->amount,
            'expense'=>$this ->expense,
            'customerId'=>$this ->customer_id,
            'userId'=>$this ->user_id,
            'createdBy'=>$this ->created_by,
            'expiredDate'=>$this ->expired_date,
            'isActive'=>$this ->is_active,
        ];
    }
}