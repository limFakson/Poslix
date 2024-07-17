<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSalesResource extends JsonResource
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
            'saleId'=>$this->sale_id,
            'productId'=>$this->product_id,
            'productName'=>$this->product_name,
            'productBatchId'=>$this->product_batch_id,
            'variantId'=>$this->variant_id,
            'imeiNumber'=>$this->imei_number,
            'quatity'=>$this->qty,
            'returnQty'=>$this->return_qty,
            'saleUnitId'=>$this->sale_unit_id,
            'netUnitPrice'=>$this->net_unit_price,
            'discount'=>$this->discount,
            'taxRate'=>$this->tax_rate,
            'extras'=>$this->extras,
            'extraNames'=>$this->extra_names,
            'extra'=>$this->extra,
            'tax'=>$this->tax,
            'total'=>$this->total,
            'createdAt'=>$this->created_at,
            'updatedAt'=>$this->updated_at
        ];
    }
}