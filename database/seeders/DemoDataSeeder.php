<?php

namespace Database\Seeders;

use App\Models\{
    Business, Outlet, Category, Product, ProductPrice, ProductStock, User,
    Customer, Supplier, Discount, Voucher, Table, Shift
};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::create([
            'name' => 'Kopi Kenangan Livitap',
            'slug' => 'kopi-kenangan-livitap',
            'type' => 'fnb',
            'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
            'phone' => '021-5555-1234',
            'email' => 'info@kopikenangan.test',
            'is_active' => true,
        ]);

        $outlet = Outlet::create([
            'business_id' => $business->id,
            'name' => 'Cabang Sudirman',
            'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
            'phone' => '021-5555-1234',
            'is_active' => true,
        ]);

        $catFood = Category::create([
            'business_id' => $business->id,
            'name' => 'Makanan',
            'slug' => 'makanan',
            'icon' => '🍽️',
            'sort_order' => 1,
        ]);

        $catDrink = Category::create([
            'business_id' => $business->id,
            'name' => 'Minuman',
            'slug' => 'minuman',
            'icon' => '☕',
            'sort_order' => 2,
        ]);

        $catSnack = Category::create([
            'business_id' => $business->id,
            'name' => 'Snack',
            'slug' => 'snack',
            'icon' => '🥨',
            'sort_order' => 3,
        ]);

        $foods = [
            ['name' => 'Nasi Goreng Spesial', 'sku' => 'FOOD001', 'desc' => 'Nasi goreng dengan telur, ayam, dan sayur', 'buy' => 15000, 'sell' => 25000],
            ['name' => 'Mie Goreng Jawa', 'sku' => 'FOOD002', 'desc' => 'Mie goreng dengan rasa khas Jawa', 'buy' => 12000, 'sell' => 20000],
            ['name' => 'Ayam Bakar Taliwang', 'sku' => 'FOOD003', 'desc' => 'Ayam bakar dengan bumbu Taliwang pedas', 'buy' => 18000, 'sell' => 30000],
            ['name' => 'Sate Ayam Madura', 'sku' => 'FOOD004', 'desc' => 'Sate ayam dengan bumbu kacang Madura', 'buy' => 10000, 'sell' => 18000],
            ['name' => 'Gado-Gado Jakarta', 'sku' => 'FOOD005', 'desc' => 'Gado-gado dengan bumbu kacang spesial', 'buy' => 8000, 'sell' => 15000],
            ['name' => 'Bakso Urat Spesial', 'sku' => 'FOOD006', 'desc' => 'Bakso urat dengan mie dan tahu', 'buy' => 12000, 'sell' => 20000],
            ['name' => 'Iga Bakar Rica-Rica', 'sku' => 'FOOD007', 'desc' => 'Iga bakar dengan rica-rica pedas', 'buy' => 25000, 'sell' => 40000],
            ['name' => 'Pecel Lele Minggat', 'sku' => 'FOOD008', 'desc' => 'Pecel lele dengan sambal terasi', 'buy' => 14000, 'sell' => 22000],
        ];

        $drinks = [
            ['name' => 'Kopi Susu Gula Aren', 'sku' => 'DRINK001', 'desc' => 'Kopi susu dengan gula aren asli', 'buy' => 8000, 'sell' => 18000],
            ['name' => 'Es Teh Manis', 'sku' => 'DRINK002', 'desc' => 'Es teh manis segar', 'buy' => 2000, 'sell' => 5000],
            ['name' => 'Kopi Hitam Tubruk', 'sku' => 'DRINK003', 'desc' => 'Kopi hitam tanpa gula', 'buy' => 5000, 'sell' => 8000],
            ['name' => 'Cappuccino Cincau', 'sku' => 'DRINK004', 'desc' => 'Cappuccino dengan cincau hitam', 'buy' => 9000, 'sell' => 16000],
            ['name' => 'Milo Dingin', 'sku' => 'DRINK005', 'desc' => 'Milo dingin dengan topping marshmallow', 'buy' => 7000, 'sell' => 14000],
            ['name' => 'Es Jeruk Peras', 'sku' => 'DRINK006', 'desc' => 'Jeruk peras segar tanpa gula', 'buy' => 6000, 'sell' => 12000],
            ['name' => 'Smoothie Mangga', 'sku' => 'DRINK007', 'desc' => 'Smoothie mangga dengan susu', 'buy' => 10000, 'sell' => 18000],
            ['name' => 'Es Cokelat', 'sku' => 'DRINK008', 'desc' => 'Cokelat panas dengan whipped cream', 'buy' => 8000, 'sell' => 15000],
        ];

        $snacks = [
            ['name' => 'Roti Bakar Cokelat', 'sku' => 'SNACK001', 'desc' => 'Roti bakar dengan selai cokelat', 'buy' => 6000, 'sell' => 12000],
            ['name' => 'Kentang Goreng', 'sku' => 'SNACK002', 'desc' => 'Kentang goreng dengan saus tomat', 'buy' => 5000, 'sell' => 10000],
            ['name' => 'Onion Ring', 'sku' => 'SNACK003', 'desc' => 'Onion ring crispy dengan saus mayones', 'buy' => 7000, 'sell' => 13000],
            ['name' => 'Chicken Nugget', 'sku' => 'SNACK004', 'desc' => 'Nugget ayam dengan saus pilihan', 'buy' => 8000, 'sell' => 14000],
            ['name' => 'Bakwan Jagung', 'sku' => 'SNACK005', 'desc' => 'Bakwan jagung renyah', 'buy' => 4000, 'sell' => 8000],
        ];

        $allProducts = array_merge($foods, $drinks, $snacks);

        foreach ($allProducts as $index => $p) {
            $category = $index < 8 ? $catFood : ($index < 16 ? $catDrink : $catSnack);
            
            $product = Product::create([
                'business_id' => $business->id,
                'category_id' => $category->id,
                'name' => $p['name'],
                'slug' => Str::slug($p['name']),
                'sku' => $p['sku'],
                'description' => $p['desc'],
                'unit' => 'pcs',
                'track_stock' => true,
                'is_active' => true,
                'is_pos_visible' => true,
            ]);

            ProductPrice::create([
                'product_id' => $product->id,
                'outlet_id' => $outlet->id,
                'buy_price' => $p['buy'],
                'sell_price' => $p['sell'],
            ]);

            ProductStock::create([
                'product_id' => $product->id,
                'outlet_id' => $outlet->id,
                'qty' => rand(20, 100),
                'min_qty' => rand(5, 15),
            ]);
        }

        for ($i = 1; $i <= 6; $i++) {
            Table::create([
                'outlet_id' => $outlet->id,
                'name' => "Meja $i",
                'capacity' => $i <= 4 ? 4 : 6,
                'area' => $i <= 3 ? 'Indoor' : 'Outdoor',
                'status' => 'empty',
                'sort_order' => $i,
            ]);
        }

        Supplier::create([
            'business_id' => $business->id,
            'name' => 'PT Suplier Bahan Baku',
            'code' => 'SUP001',
            'contact_person' => 'Bapak Suplier',
            'phone' => '021-5555-9999',
            'address' => 'Jl. Supplier No. 456, Jakarta',
            'is_active' => true,
        ]);

        $owner = User::create([
            'business_id' => $business->id,
            'name' => 'Pemilik Livitap',
            'email' => 'owner@livitap.test',
            'password' => Hash::make('password'),
            'phone' => '0812-3456-7890',
        ]);
        $owner->assignRole('Owner');

        $manager = User::create([
            'business_id' => $business->id,
            'name' => 'Manager Kenangan',
            'email' => 'manager@livitap.test',
            'password' => Hash::make('password'),
            'phone' => '0812-3456-7891',
        ]);
        $manager->assignRole('Manager');
        $manager->outlets()->attach($outlet->id);

        $kasir = User::create([
            'business_id' => $business->id,
            'name' => 'Kasir 1',
            'email' => 'kasir1@livitap.test',
            'password' => Hash::make('password'),
            'phone' => '0812-3456-7892',
        ]);
        $kasir->assignRole('Kasir');
        $kasir->outlets()->attach($outlet->id);

        $kasir2 = User::create([
            'business_id' => $business->id,
            'name' => 'Kasir 2',
            'email' => 'kasir2@livitap.test',
            'password' => Hash::make('password'),
            'phone' => '0812-3456-7893',
        ]);
        $kasir2->assignRole('Kasir');
        $kasir2->outlets()->attach($outlet->id);

        for ($i = 1; $i <= 5; $i++) {
            Customer::create([
                'business_id' => $business->id,
                'name' => "Pelanggan $i",
                'phone' => "0812-0000-000$i",
                'tier' => ['regular', 'silver', 'gold'][$i % 3],
                'points' => rand(0, 500),
            ]);
        }

        Discount::create([
            'business_id' => $business->id,
            'name' => 'Diskon Pagi 10%',
            'type' => 'percentage',
            'value' => 10,
            'min_purchase' => 50000,
            'applies_to' => 'all',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(30),
            'is_active' => true,
        ]);

        Discount::create([
            'business_id' => $business->id,
            'name' => 'Diskon Minuman 15%',
            'type' => 'percentage',
            'value' => 15,
            'min_purchase' => 25000,
            'applies_to' => 'category',
            'target_ids' => [$catDrink->id],
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(15),
            'is_active' => true,
        ]);

        $voucherDiscount = Discount::create([
            'business_id' => $business->id,
            'name' => 'Voucher Diskon 20.000',
            'type' => 'nominal',
            'value' => 20000,
            'min_purchase' => 100000,
            'applies_to' => 'all',
            'usage_limit' => 100,
            'is_active' => true,
        ]);

        for ($i = 1; $i <= 5; $i++) {
            Voucher::create([
                'discount_id' => $voucherDiscount->id,
                'code' => 'LIVE' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'expires_at' => now()->addDays(30),
            ]);
        }
    }
}