<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource {
    /**
    * Transform the resource into an array.
    *
    * @return array<string, mixed>
    */

    public function toArray( Request $request ): array {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'shortDescription'=>$this->short_description,
            'image'=> config( 'app.url' ).'/images/category/'.$this->image,
            'icon'=> config( 'app.url' ).'/images/category/icons/'.$this->icon,
            'featured'=>$this->featured,
            'isActive'=>$this->is_active
        ];
    }
}