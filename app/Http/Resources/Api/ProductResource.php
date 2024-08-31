<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\ProductVariantCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource {
    /**
    * Transform the resource into an array.
    *
    * @return array<string, mixed>
    */

    public function toArray( Request $request ): array {
        return [
            'id' =>$this ->id,
            'name' =>$this ->name,
            'code' =>$this ->code,
            'type' =>$this ->type,
            'CategoryId' =>$this ->category_id,
            'unitId' =>$this ->unit_id,
            'price' =>$this ->price,
            'price1' =>$this ->price1,
            'price2' =>$this ->price2,
            'price3' =>$this ->price3,
            'quantity' =>$this->qty,
            'image' => config( 'app.url' ).'/images/product/'.$this-> image ?? null,
            'taxMethod' =>$this ->tax_method??null,
            'taxName' =>$this ->tax_name??null,
            'taxRate' =>$this ->tax_rate??null,
            'warehouseQty'=>$this->warehouse_qty??null
        ];
    }
}