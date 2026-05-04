<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('update-customer');
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')->id ?? $this->route('customer');

        return [
            'business_id' => 'sometimes|exists:businesses,id',
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20|unique:customers,phone,' . $customerId,
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customerId,
            'address' => 'nullable|string|max:500',
            'gender' => 'nullable|in:male,female,other',
            'birthdate' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
            'tier' => 'nullable|in:regular,silver,gold,platinum',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];
    }
}
