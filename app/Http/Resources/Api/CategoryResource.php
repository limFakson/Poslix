<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'name'=>$this->name,
            'image'=> config('app.url').'/images/category/'.$this->image,
            'parentId'=>$this->parent_id,
            'shortDescription'=>$this->short_description,
            'slug'=>$this->slug,
            'icon'=> config('app.url').'/images/category/icons/'.$this->icon,
            'featured'=>$this->featured,
            'isActive'=>$this->is_active,
            'woocommerceCategoryId'=>$this->woocommerce_category_id,
            'isSyncDisable'=>$this->is_sync_disable,
        ];
    }
}