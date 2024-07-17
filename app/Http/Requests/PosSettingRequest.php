<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PosSettingRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $method =$this->method();

        if($method=='PUT'){
            return [
                //
                'customerId' =>'required|integer',
                'warehouseId'=>'required|integer',
                'billerId'=>'nullable|integer',
                'productNumber'=>'nullable|integer',
                'keyboardActive'=>'nullable|boolean',
                'isTable'=>'nullable|boolean',
                'stripePublicKey'=>'nullable|string',
                'stripeSecretKey'=>'nullable|string',
                'paypalLiveApiUsername'=>'nullable|string',
                'paypalLiveApiPassword'=>'nullable|string',
                'paypalLiveApiAecret'=>'nullable|string',
                'paymentOptions'=>'nullable|string',
                'invoiceOption'=>'nullable|string',
            ];
        } else{
            return [
                //
                'customerId' =>'required|integer',
                'warehouseId'=>'required|integer',
                'billerId'=>'nullable|integer',
                'productNumber'=>'nullable|integer',
                'keyboardActive'=>'nullable|boolean',
                'isTable'=>'nullable|boolean',
                'stripePublicKey'=>'nullable|string',
                'stripeSecretKey'=>'nullable|string',
                'paypalLiveApiUsername'=>'nullable|string',
                'paypalLiveApiPassword'=>'nullable|string',
                'paypalLiveApiAecret'=>'nullable|string',
                'paymentOptions'=>'nullable|string',
                'invoiceOption'=>'nullable|string',
            ];
        }
    }
}