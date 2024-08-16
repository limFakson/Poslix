<?php

namespace App\Http\Resources\Api\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollectionV extends ResourceCollection {
    /**
    * Transform the resource collection into an array.
    *
    * @return array<int|string, mixed>
    */
    public function toArray(Request $request): array {
        return [
            'data' => $this->collection->map(function($item) use ($request) {
                return (new ProductResourceV($item))->toArray($request);
            }),
        ];
    }
}