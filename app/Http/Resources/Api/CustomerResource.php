<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'customerGroupId'=>$this->customer_group_id,
            'userId'=>$this->user_id,
            'name'=>$this->name,
            'companyName'=>$this->company_name,
            'email'=>$this->email,
            'phoneNumber'=>$this->phone_number,
            'taxNo'=>$this->tax_no,
            'address'=>$this->address,
            'city'=>$this->city,
            'state'=>$this->state,
            'country'=>$this->country,
            'postalCode'=>$this->postal_code,
            'points'=>$this->points,
            'isActive'=>$this->is_active,
            'deposit'=>$this->deposit,
            'expense'=>$this->expense,
            'wishlist'=>$this->wishlist,
        ];
    }
}