<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductPrice;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Find or create category
        $category = null;
        if (isset($row['category']) && $row['category']) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($row['category'])],
                ['name' => $row['category'], 'is_active' => true]
            );
        }

        // Create product
        $product = Product::create([
            'business_id' => 1, // TODO: get from session/auth
            'category_id' => $category?->id,
            'name' => $row['name'],
            'slug' => Str::slug($row['name']),
            'sku' => $row['sku'] ?? Str::upper(Str::random(8)),
            'barcode' => $row['barcode'] ?? null,
            'description' => $row['description'] ?? null,
            'unit' => $row['unit'] ?? 'pcs',
            'track_stock' => $row['track_stock'] ?? true,
            'is_active' => $row['is_active'] ?? true,
            'is_pos_visible' => true,
        ]);

        // Create price
        if (isset($row['sell_price'])) {
            $product->prices()->create([
                'outlet_id' => session('outlet_id', 1),
                'buy_price' => $row['buy_price'] ?? 0,
                'sell_price' => $row['sell_price'],
            ]);
        }

        return $product;
    }
}
