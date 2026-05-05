<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductPrice;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ProductsImport implements ToModel, WithHeadingRow
{
    protected $outletId;

    public function __construct($outletId = null)
    {
        $this->outletId = $outletId ?? 1;
    }

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

        // Always create price (with default 0 if not provided)
        $product->prices()->create([
            'outlet_id' => $this->outletId,
            'buy_price' => $row['buy_price'] ?? 0,
            'sell_price' => $row['sell_price'] ?? 0,
        ]);

        return $product;
    }
}
