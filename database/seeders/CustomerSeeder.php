<?php
namespace Database\Seeders;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder {
    public function run(): void {
        Customer::firstOrCreate(['name' => 'Pelanggan Umum', 'business_id' => 1]);
        Customer::firstOrCreate(['name' => 'Budi Santoso', 'business_id' => 1]);
    }
}