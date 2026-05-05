<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // authorization handled in controller via policy
    }

    public function rules(): array
    {
        return [
            'category_id'   => 'nullable|exists:categories,id',
            'name'          => 'required|string|max:255',
            'sku'           => 'required|string|max:100|unique:products,sku',
            'barcode'       => 'nullable|string|max:100|unique:products,barcode',
            'description'   => 'nullable|string|max:2000',
            'unit'          => 'required|string|max:50',
            'track_stock'   => 'boolean',
            'has_variant'   => 'boolean',
            'is_composite'  => 'boolean',
            'is_active'     => 'boolean',
            'is_pos_visible'=> 'boolean',
            'is_favorite'   => 'boolean',
            'buy_price'     => 'required|numeric|min:0',
            'sell_price'    => 'required|numeric|min:0',
            'min_stock'     => 'nullable|numeric|min:0',
            'photo'         => 'nullable|image|max:2048',
        ];
    }
}
