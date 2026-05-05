<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductPrice;
use App\Models\ProductStock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CafeProductSeeder extends Seeder
{
    public function run(): void
    {
        $businessId = 2; // Sesuai dengan ID bisnis yang aktif
        $outletId = 1;

        $categories = [
            'Coffee' => ['Espresso', 'Americano', 'Latte', 'Cappuccino'],
            'Non-Coffee' => ['Matcha Latte', 'Chocolate', 'Red Velvet'],
            'Snacks' => ['Croissant', 'Brownies', 'French Fries'],
        ];

        foreach ($categories as $catName => $products) {
            $category = Category::firstOrCreate([
                'name' => $catName,
                'slug' => Str::slug($catName),
                'is_active' => true
            ]);

            foreach ($products as $prodName) {
                $product = Product::updateOrCreate(
                    ['name' => $prodName],
                    [
                        'category_id' => $category->id,
                        'slug' => Str::slug($prodName),
                        'sku' => Str::upper(Str::random(6)),
                        'is_active' => true,
                        'is_pos_visible' => true,
                        'track_stock' => true
                    ]
                );

                ProductPrice::updateOrCreate(
                    ['product_id' => $product->id, 'outlet_id' => $outletId],
                    ['sell_price' => rand(15000, 45000), 'buy_price' => rand(5000, 20000)]
                );

                ProductStock::updateOrCreate(
                    ['product_id' => $product->id, 'outlet_id' => $outletId],
                    ['qty' => 50, 'min_qty' => 5]
                );
            }
        }
    }
}
