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
            'companyName'=>$this->company_name ?? null,
            'email'=>$this->email,
            'phoneNumber'=>$this->phone_number ?? null,
            'taxNo'=>$this->tax_no ?? null,
            'address'=>$this->address ?? null,
            'city'=>$this->city ?? null,
            'state'=>$this->state ?? null,
            'country'=>$this->country ?? null,
            'postalCode'=>$this->postal_code ?? null,
            'points'=>$this->points ?? null,
            'isActive'=>$this->is_active,
            'deposit'=>$this->deposit ?? null,
            'expense'=>$this->expense ?? null,
            'wishlist'=>$this->wishlist ?? null,
        ];
    }
}
