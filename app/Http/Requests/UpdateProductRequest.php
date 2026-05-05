<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // authorization handled in controller via policy
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id ?? $this->route('product');

        return [
            'category_id'   => 'nullable|exists:categories,id',
            'name'          => 'sometimes|string|max:255',
            'sku'           => 'sometimes|string|max:100|unique:products,sku,' . $productId,
            'barcode'       => 'nullable|string|max:100|unique:products,barcode,' . $productId,
            'description'   => 'nullable|string|max:2000',
            'unit'          => 'sometimes|string|max:50',
            'track_stock'   => 'boolean',
            'has_variant'   => 'boolean',
            'is_composite'  => 'boolean',
            'is_active'     => 'boolean',
            'is_pos_visible'=> 'boolean',
            'is_favorite'   => 'boolean',
            'buy_price'     => 'nullable|numeric|min:0',
            'sell_price'    => 'nullable|numeric|min:0',
            'photo'         => 'nullable|image|max:2048',
        ];
    }
}
