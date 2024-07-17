<?php

namespace App\Http\Resources\Api;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenSettingsResource extends JsonResource
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
            'siteTitle'=>$this ->site_title,
            'siteLogo'=> config('app.url').'/logo/'.$this ->site_logo,
            'isRtl'=>$this ->is_rtl,
            'currency'=>$this ->currency,
            'packageId'=>$this ->package_id,
            'subscriptionType'=>$this ->subscription_type,
            'staffAccess'=>$this ->staff_access,
            'withoutStock'=>$this ->without_stock,
            'dateFormat'=>$this ->date_format,
            'developedBy'=>$this ->developed_by,
            'invoiceFormat'=>$this ->invoice_format,
            'decimal'=>$this ->decimal,
            'state'=>$this ->state,
            'theme'=>$this ->theme,
            'modules'=>$this ->modules,
            'currencyPosition'=>$this ->currency_position,
            'expiryDate'=>$this ->expiry_date,
            'isZatca'=>$this ->is_zatca,
            'companyName'=>$this ->company_name,
            'vatRegistrationNumber'=>$this ->vat_registration_number,    
        ];
    }
}