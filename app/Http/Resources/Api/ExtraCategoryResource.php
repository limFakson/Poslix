<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExtraCategoryResource extends JsonResource {
    /**
    * Transform the resource into an array.
    *
    * @return array<string, mixed>
    */

    public function toArray( Request $request ): array {
        return [
            'id'=>$this->id,
            'categoryName'=>$this->category_name,
            'isMulti'=>$this->is_multi,
            'laravelThroughKey'=>$this->laravel_through_key
        ];
    }
}