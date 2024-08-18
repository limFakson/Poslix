<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource {
    /**
    * Transform the resource into an array.
    *
    * @return array<string, mixed>
    */

    public function toArray( Request $request ): array {
        return [
            'id'=>$this->id,
            'message'=>$this->message,
            'icon'=>$this->icon,
            'table'=>$this->table,
            'isViewed'=>$this->is_viewed,
            'viewedBy'=>$this->viewed_by,
            'createdAt'=>$this->created_at
        ];
    }
}