<?php

namespace App\Http\Resources\Api\V2;

use Illuminate\Http\Request;
use App\Http\Resources\Api\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\ExtraCategoryCollection;
use App\Http\Resources\Api\ProductVariantCollection;

class ProductResourceV extends JsonResource {
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
            'cost' =>$this ->cost,
            'price' =>$this ->price,
            'price1' =>$this ->price1,
            'price2' =>$this ->price2,
            'price3' =>$this ->price3,
            'quantity' =>$this->qty,
            'image' => config( 'app.url' ).'/images/product/'.$this-> image ?? null,
            'taxMethod' =>$this ->tax_method??null,
            'taxName' =>$this ->tax_name??null,
            'taxRate' =>$this ->tax_rate??null,
            'warehouseQty'=>$this->warehouse_qty??null,
            'category' => new CategoryResource( $this->whenLoaded( 'category' ) ),
            'productVariant' => new ProductVariantCollection( $this->whenLoaded( 'productVariants' ) ),
            'extraCategory'=>new ExtraCategoryCollection( $this->whenLoaded( 'extraCategories' ) )
        ];
    }
}
