<?php

namespace Database\Seeders;

use App\Models\{Business, Outlet, Category, Product, ProductPrice, ProductStock, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([PermissionSeeder::class]);

        $business = Business::create([
            'name' => 'Cafe Livitap',
            'slug' => 'cafe-livitap',
            'type' => 'fnb',
        ]);

        $outlet = Outlet::create([
            'business_id' => $business->id,
            'name' => 'Cabang Pusat',
        ]);

        $catFood = Category::create(['business_id' => $business->id, 'name' => 'Makanan', 'slug' => 'makanan']);
        $catDrink = Category::create(['business_id' => $business->id, 'name' => 'Minuman', 'slug' => 'minuman']);

        $products = [
            ['name' => 'Nasi Goreng Spesial', 'cat' => $catFood, 'sku' => 'M001', 'buy' => 15000, 'sell' => 25000],
            ['name' => 'Mie Ayam Bakso', 'cat' => $catFood, 'sku' => 'M002', 'buy' => 12000, 'sell' => 20000],
            ['name' => 'Kopi Susu Gula Aren', 'cat' => $catDrink, 'sku' => 'D001', 'buy' => 8000, 'sell' => 18000],
            ['name' => 'Es Teh Manis', 'cat' => $catDrink, 'sku' => 'D002', 'buy' => 2000, 'sell' => 5000],
        ];

        foreach ($products as $p) {
            $prod = Product::create([
                'business_id' => $business->id,
                'category_id' => $p['cat']->id,
                'name' => $p['name'],
                'slug' => Str::slug($p['name']),
                'sku' => $p['sku'],
            ]);

            ProductPrice::create([
                'product_id' => $prod->id,
                'outlet_id' => $outlet->id,
                'buy_price' => $p['buy'],
                'sell_price' => $p['sell'],
            ]);

            ProductStock::create([
                'product_id' => $prod->id,
                'outlet_id' => $outlet->id,
                'qty' => 50,
            ]);
        }

        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@livitap.test',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('Owner');

        $kasir = User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir@livitap.test',
            'password' => Hash::make('password'),
        ]);
        $kasir->assignRole('Kasir');
    }
}
