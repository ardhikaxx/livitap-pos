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
                    'business_id' => $business->id,
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

        // Create demo products
        $foodCategory = Category::where('slug', 'food')->first();
        $drinkCategory = Category::where('slug', 'drink')->first();
        $snackCategory = Category::where('slug', 'snack')->first();
        $dailyCategory = Category::where('slug', 'daily')->first();

        $products = [
            ['Nasi Goreng', 'NASG001', $foodCategory->id, 15000],
            ['Mie Ayam', 'MIEA001', $foodCategory->id, 12000],
            ['Es Teh', 'ESTE001', $drinkCategory->id, 5000],
            ['Kopi Susu', 'KOPI001', $drinkCategory->id, 18000],
            ['Keripik Kentang', 'KENT001', $snackCategory->id, 8000],
            ['Sabun Mandi', 'SABU001', $dailyCategory->id, 12000],
        ];

        foreach ($products as $productData) {
            $product = Product::firstOrCreate(
                ['sku' => $productData[1]],
                [
                    'business_id' => $business->id,
                    'category_id' => $productData[2],
                    'name' => $productData[0],
                    'slug' => strtolower(str_replace(' ', '-', $productData[0])),
                    'sku' => $productData[1],
                    'unit' => 'pcs',
                    'track_stock' => true,
                    'is_active' => true,
                    'is_pos_visible' => true,
                ]
            );

            // Create product price for outlet
            ProductPrice::firstOrCreate(
                ['product_id' => $product->id, 'outlet_id' => $outlet->id],
                [
                    'buy_price' => $productData[3] * 0.6, // HPP 60% dari harga jual
                    'sell_price' => $productData[3],
                ]
            );

            // Create stock
            $product->stocks()->firstOrCreate(
                ['outlet_id' => $outlet->id],
                ['qty' => 100, 'min_qty' => 10]
            );
        }

        // Assign outlet to all super-admin users
        $superAdmins = User::role('super-admin')->get();
        foreach ($superAdmins as $user) {
            $user->outlets()->syncWithoutDetaching([$outlet->id => ['is_primary' => true]]);
        }
    }
}
