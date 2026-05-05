<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business;
use App\Models\Outlet;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo business
        $business = Business::firstOrCreate(
            ['slug' => 'toko-abc'],
            [
                'name' => 'Toko ABC',
                'type' => 'retail',
                'address' => 'Jl. Merdeka No. 123, Jakarta',
                'phone' => '021-1234567',
                'email' => 'info@tokoabc.com',
                'settings' => [
                    'enable_fnb' => false,
                    'timezone' => 'Asia/Jakarta',
                ],
                'is_active' => true,
            ]
        );

        // Create categories
        $categories = [
            ['Makanan', 'food', '🍔'],
            ['Minuman', 'drink', '🥤'],
            ['Cemilan', 'snack', '🍿'],
            ['Keperluan Sehari-hari', 'daily', '🧻'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => $cat[1]],
                [
                    'name' => $cat[0],
                    'slug' => $cat[1],
                    'icon' => $cat[2],
                    'is_active' => true,
                ]
            );
        }

        // Create outlet
        $outlet = Outlet::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Cabang Utama'],
            [
                'address' => 'Jl. Merdeka No. 123',
                'phone' => '021-1234567',
                'tax_settings' => ['enabled' => false],
                'receipt_settings' => [
                    'paper_size' => '80mm',
                    'show_logo' => true,
                    'show_tax' => true,
                ],
                'is_active' => true,
            ]
        );

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
                        'sku' => strtoupper(str_random(6)),
                        'is_active' => true,
                        'is_pos_visible' => true,
                        'track_stock' => true
                    ]
                );

                ProductPrice::updateOrCreate(
                    ['product_id' => $product->id],
                    ['sell_price' => rand(15000, 45000), 'buy_price' => rand(5000, 20000)]
                );
            }
        }

        // Assign outlet to all super-admin users
        $superAdmins = User::role('super-admin')->get();
        foreach ($superAdmins as $user) {
            $user->outlets()->syncWithoutDetaching([$outlet->id => ['is_primary' => true]]);
        }
    }
}
