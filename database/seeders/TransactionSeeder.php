<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada data pendukung
        $user = User::first() ?? User::factory()->create();
        $customer = Customer::firstOrCreate(['name' => 'Budi Santoso']);
        $product = Product::first() ?? Product::create(['name' => 'Produk Demo', 'category_id' => 1, 'sku' => 'DEMO']);
        $shift = Shift::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'open'],
            ['outlet_id' => 1, 'opening_cash' => 100000]
        );

        $sale = Sale::create([
            'id' => Str::uuid(),
            'outlet_id' => 1,
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'shift_id' => $shift->id,
            'invoice_number' => 'INV-' . now()->format('YmdHis') . '-' . rand(100, 999),
            'sale_date' => now(),
            'subtotal' => 50000,
            'total' => 50000,
            'paid_amount' => 50000,
            'change_amount' => 0,
            'status' => 'paid',
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'name_snapshot' => $product->name,
            'qty' => 1,
            'unit_price' => 50000,
            'subtotal' => 50000,
        ]);

        SalePayment::create([
            'sale_id' => $sale->id,
            'method' => 'cash',
            'amount' => 50000,
        ]);

        $this->command->info('Data transaksi berhasil di-seed.');
    }
}
