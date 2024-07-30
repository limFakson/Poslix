<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillerResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'companyName'=>$this->company_name,
            'email'=>$this->email,
            'phoneNum'=>$this->phone_number,
            'address'=>$this->address,
            'city'=>$this->city,
            'state'=>$this->state,
            'country'=>$this->country,
            'postalCode'=>$this->postal_code,
            'image'=>config('app.url').'/images/biller/'.$this->image,
            'vatNumber'=>$this->vat_number,
            'isActive'=>$this->is_active,
            'createdAt'=>$this->created_at
        ];
    }
}
