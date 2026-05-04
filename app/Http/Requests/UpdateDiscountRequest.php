<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('update-discount');
    }

    public function rules(): array
    {
        $discountId = $this->route('discount')->id ?? $this->route('discount');

        return [
            'business_id' => 'sometimes|exists:businesses,id',
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:percentage,nominal,bogo,bundle',
            'value' => [
                'sometimes',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($this->input('type') === 'percentage' && $value > 100) {
                        $fail('Persentase diskon maksimal 100%');
                    }
                },
            ],
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'applies_to' => 'sometimes|in:all,category,product',
            'target_ids' => 'nullable|array',
            'target_ids.*' => 'exists:products,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ];
    }
}
