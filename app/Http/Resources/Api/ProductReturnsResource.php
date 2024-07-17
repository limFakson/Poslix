<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReturnsResource extends JsonResource
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
            'returnId'=>$this->return_id,
            'productId'=>$this->product_id,
            'productBatchId'=>$this->product_batch_id,
            'variantId'=>$this->variant_id,
            'imeiNumber'=>$this->imei_number,
            'quantity'=>$this->qty,
            'saleUnitId'=>$this->sale_unit_id,
            'netUnitPrice'=>$this->net_unit_price,
            'discount'=>$this->discount,
            'taxRate'=>$this->tax_rate,
            'tax'=>$this->tax,
            'total'=>$this->total,
            'createdAt'=>$this->created_at,
            'updatedAt'=>$this->updated_at
        ];
    }
}