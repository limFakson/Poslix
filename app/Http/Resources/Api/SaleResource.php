<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'referenceNo'=>$this->reference_no,
            'userId'=>$this->user_id,
            'cashRegisterId'=>$this->cash_register_id,
            'tableId'=>$this->table_id,
            'queue'=>$this->queue,
            'customerId'=>$this->customer_id,
            'customerName'=>$this->customer_name,
            'customerPhoneNumber'=>$this->customer_phone_number,
            'warehouseId'=>$this->warehouse_id,
            'billerId'=>$this->biller_id,
            'item'=>$this->item,
            'totalQty'=>$this->total_qty,
            'totalDiscount'=>$this->total_discount,
            'totalTax'=>$this->total_tax,
            'totalPrice'=>$this->total_price,
            'grandTotal'=>$this->grand_total,
            'currencyId'=>$this->currency_id,
            'exchangeRate'=>$this->exchange_rate,
            'orderTaxRate'=>$this->order_tax_rate,
            'orderTax'=>$this->order_tax,
            'orderDiscountType'=>$this->order_discount_type,
            'orderDiscountValue'=>$this->order_discount_value,
            'orderDiscount'=>$this->order_discount,
            'couponId'=>$this->coupon_id,
            'couponDiscount'=>$this->coupon_discount,
            'shippingCost'=>$this->shipping_cost,
            'saleStatus'=>$this->sale_status,
            'paymentStatus'=>$this->payment_status,
            'document'=>$this->document,
            'paidAmount'=>$this->paid_amount,
            'billingName'=>$this->billing_name,
            'billingPhone'=>$this->billing_phone,
            'billingEmail'=>$this->billing_email,
            'billingAddress'=>$this->billing_address,
            'billingCity'=>$this->billing_city,
            'billingState'=>$this->billing_state,
            'billingCountry'=>$this->billing_country,
            'billingZip'=>$this->billing_zip,
            'shippingName'=>$this->shipping_name,
            'shippingPhone'=>$this->shipping_phone,
            'shippingEmail'=>$this->shipping_email,
            'shippingAddress'=>$this->shipping_address,
            'shippingCity'=>$this->shipping_city,
            'shippingState'=>$this->shipping_state,
            'shippingCountry'=>$this->shipping_country,
            'shippingZip'=>$this->shipping_zip,
            'saleType'=>$this->sale_type,
            'orderType'=>$this->order_type,
            'paymentMode'=>$this->payment_mode,
            'saleNote'=>$this->sale_note,
            'staffNote'=>$this->staff_note,
            'woocommerceOrderId'=>$this->woocommerce_order_id,
            'createdAt'=>$this->created_at,
            'updatedAt'=>$this->updated_at,
        ];
    }
}