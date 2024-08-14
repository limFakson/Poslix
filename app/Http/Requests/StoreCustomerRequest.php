<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends BaseFormRequest
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
        return [
            'customerGroupId' => 'required',
            'userId' => 'nullable',
            'name' => 'required|string|max:255',
            'companyName' => 'nullable|string|max:255',
            'email' => 'required|string|max:255',
            'phoneNumber' => 'nullable|max:20',
            'taxNo' => 'nullable|max:40',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postalCode' => 'nullable|max:20',
            'country' => 'nullable|string|max:100',
            'points' => 'nullable|integer',
            'deposit' => 'nullable|integer',
            'expense' => 'nullable|integer',
            'wishlist' => 'nullable|integer',
            'isActive' => 'nullable|boolean',
        ];
    }

    // protected function prepareForValidation() {
    //     $this->merge([
    //         'customer_group_id'=>$this->customerGroupId,
    //         'user_id'=>$this->userId,
    //         'company_name'=>$this->companyName,
    //         'phone_number'=>$this->phoneNumber,
    //         'tax_no'=>$this->taxNo,
    //         'postal_code'=>$this->postalCode,
    //         'is_active'=>$this->isActive,
    //     ]);
    // }
}
