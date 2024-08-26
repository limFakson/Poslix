<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductWarehouseResource extends JsonResource {
    /**
    * Transform the resource into an array.
    *
    * @return array<string, mixed>
    */

    public function toArray( Request $request ): array {
        return [
            'id'=>$this->id,
            'productId'=>( int )$this->product_id,
            'warehouseId'=>$this->warehouse_id,
            'variantId'=>$this->variant_id,
            'productBatchId'=>$this->product_batch_id,
            'warehouseQty'=>$this->qty,
        ];
    }
}