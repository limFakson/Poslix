<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource {
    /**
    * Transform the resource into an array.
    *
    * @return array<string, mixed>
    */

    public function toArray( Request $request ): array {
        return [
            'id'=>$this->id,
            'productId'=>$this->product_id,
            'variantId'=>$this->variant_id,
            'itemCode'=>$this->item_code,
            'additionalCost'=>$this->additional_cost,
            'additionalPrice'=>$this->additional_price,
            'quantity'=>$this->qty,
        ];
    }
}
