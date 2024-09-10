<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use App\Traits\ColorConverter;
use Illuminate\Http\Resources\Json\JsonResource;

class AppearanceResource extends JsonResource {

    use ColorConverter;

    /**
    * Transform the resource into an array.
    *
    * @return array<string, mixed>
    */

    public function toArray( Request $request ): array {
        return [
            'id'=> $this->id,
            'userId'=>$this->user_id,
            'logo' => config( 'app.url' ).'/logo/'.$this-> logo ?? null,
            'color'=>$this->hex2rgba( $this->color ),
            'menuOption'=>$this->menu_option,
            'createdAt'=>$this->created_at
        ];
    }
}