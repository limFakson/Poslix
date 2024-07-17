<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
            'customerGroupId' => 'sometimes|required',
            'userId' => 'sometimes|required',
            'name' => 'sometimes|required|string|max:255',
            'companyName' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|required|string|max:255',
            'phoneNumber' => 'sometimes|required|max:20',
            'taxNo' => 'sometimes|nullable|max:40',
            'address' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|required|string|max:100',
            'state' => 'sometimes|nullable|string|max:100',
            'postalCode' => 'sometimes|nullable|max:20',
            'country' => 'sometimes|required|string|max:100',
            'points' => 'sometimes|nullable|integer',
            'deposit' => 'sometimes|nullable|integer',
            'expense' => 'sometimes|nullable|integer',
            'wishlist' => 'sometimes|nullable|integer',
            'isActive' => 'sometimes|nullable|boolean',
        ];
    }
}