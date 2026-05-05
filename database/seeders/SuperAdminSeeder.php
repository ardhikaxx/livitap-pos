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
    }
}
