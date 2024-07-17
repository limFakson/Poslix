<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
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
            'name'=>$this ->name,
            'numberOfPerson'=>$this ->number_of_person,
            'description'=>$this ->description,
            'isActive'=>$this ->is_active,
            'createdAt'=>$this ->created_at
        ];
    }
}