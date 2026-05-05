<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Add Cafe Products
        $cafeCategories = [
            'Coffee' => ['Espresso', 'Americano', 'Latte', 'Cappuccino'],
            'Non-Coffee' => ['Matcha Latte', 'Chocolate', 'Red Velvet'],
            'Snacks' => ['Croissant', 'Brownies', 'French Fries'],
        ];

        foreach ($cafeCategories as $catName => $products) {
            $category = Category::firstOrCreate([
                'name' => $catName,
                'slug' => strtolower(str_replace(' ', '-', $catName)),
            ]);

            foreach ($products as $prodName) {
                $product = Product::updateOrCreate(
                    ['name' => $prodName],
                    [
                        'category_id' => $category->id,
                        'slug' => strtolower(str_replace(' ', '-', $prodName)),
                        'sku' => strtoupper(Str::random(6)),
                        'is_active' => true,
                        'is_pos_visible' => true,
                        'track_stock' => true
                    ]
                );

                ProductPrice::updateOrCreate(
                    ['product_id' => $product->id],
                    ['sell_price' => rand(15000, 45000), 'buy_price' => rand(5000, 20000)]
                );

                \App\Models\ProductStock::updateOrCreate(
                    ['product_id' => $product->id],
                    ['qty' => 100, 'min_qty' => 10]
                );
            }
        }
    }
}
