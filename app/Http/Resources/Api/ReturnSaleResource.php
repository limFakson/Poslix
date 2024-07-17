<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnSaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            'referenceNo'=>$this->reference_no,
            'saleId'=>$this->sale_id,
            'userId'=>$this->user_id,
            'cashregisterId'=>$this->cash_register_id,
            'customerId'=>$this->customer_id,
            'warehouseId'=>$this->warehouse_id,
            'billerId'=>$this->biller_id,
            'accountId'=>$this->account_id,
            'currencyId'=>$this->currency_id,
            'exchangeRate'=>$this->exchange_rate,
            'item'=>$this->item,
            'totalQty'=>$this->total_qty,
            'totalDiscount'=>$this->total_discount,
            'totalTax'=>$this->total_tax,
            'totalPrice'=>$this->total_price,
            'orderTaxRate'=>$this->order_tax_rate,
            'orderTax'=>$this->order_tax,
            'grandTotal'=>$this->grand_total,
            'document'=>$this->document,
            'returnNote'=>$this->return_note,
            'staffNote'=>$this->staff_note,
            'createdAt'=>$this->created_at,
            'updatedAt'=>$this->updated_at
        ];
    }
}