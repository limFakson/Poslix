<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BillerCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'companyName'=>$item->company_name,
                'email'=>$item->email,
                'phoneNum'=>$item->phone_number,
                'address'=>$item->address,
                'city'=>$item->city,
                'state'=>$item->state,
                'country'=>$item->country,
                'postalCode'=>$item->postal_code,
                'image'=>config('app.url').'/images/biller/'.$item->image,
                'vatNumber'=>$item->vat_number,
                'isActive'=>$item->is_active,
                'createdAt'=>$item->created_at
            ];
        })->toArray();
    }
}