<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PosSettingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this ->id,
            'customerId'=>$this ->customer_id,
            'warehouseId'=>$this ->warehouse_id,
            'billerId'=>$this ->biller_id,
            'productNumber'=>$this ->product_number,
            'keyboardActive'=>$this ->keybord_active,
            'isTable'=>$this ->is_table,
            'stripePublicKey'=>$this ->stripe_public_key,
            'stripeSecretKey'=>$this ->stripe_secret_key,
            'paypalLiveApiUsername'=>$this ->paypal_live_api_username,
            'paypalLiveApiPassword'=>$this ->paypal_live_api_password,
            'paypalLiveApiSecret'=>$this ->paypal_live_api_secret,
            'paymentOptions'=>$this ->payment_options,
            'invoiceOption'=>$this ->invoice_option,
        ];
    }
}
