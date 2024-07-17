<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email'=>$this->email,
            'phone'=>$this->phone,
            'companyName'=>$this->company_name,
            'roleId'=>$this->role_id,
            'roleName'=>$this->role_name,
            'billerId'=>$this->biller_id,
            'billerName'=>$this->biller_name,
            'warehouseId'=>$this->warehouse_id,
            'warehouseName'=>$this->warehouse_name,
            'warehousePhone'=>$this->warehouse_phone,
            'warehouseAddress'=>$this->warehouse_address,
            'warehouseIsActive'=>$this->warehouse_is_active,
            'isActive'=>$this->is_active
        ];
    }
}