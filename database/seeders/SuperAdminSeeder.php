<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Business;
use App\Models\Outlet;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin user
        $user = User::firstOrCreate(
            ['email' => 'admin@livitap.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'last_login_at' => now(),
            ]
        );

        // Assign super-admin role
        $user->assignRole('super-admin');

        // Create demo business if none exists
        $business = Business::firstOrCreate(
            ['slug' => 'demo-business'],
            [
                'name' => 'Demo Business',
                'type' => 'retail',
                'address' => 'Jl. Demo No. 123',
                'phone' => '081234567890',
                'email' => 'demo@livitap.com',
                'is_active' => true,
            ]
        );

        // Create demo outlet
        $outlet = Outlet::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Main Outlet'],
            [
                'address' => 'Jl. Demo No. 123',
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        // Attach user to outlet
        $user->outlets()->syncWithoutDetaching([$outlet->id => ['is_primary' => true]]);
    }
}
