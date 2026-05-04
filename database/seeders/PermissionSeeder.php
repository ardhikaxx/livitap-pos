<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::firstOrCreate(['name' => 'create-sale']);
        Permission::firstOrCreate(['name' => 'manage-products']);
        Permission::firstOrCreate(['name' => 'view-reports']);
        Permission::firstOrCreate(['name' => 'manage-users']);
        Permission::firstOrCreate(['name' => 'manage-outlets']);
        Permission::firstOrCreate(['name' => 'manage-stock']);

        $owner = Role::firstOrCreate(['name' => 'Owner']);
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $cashier = Role::firstOrCreate(['name' => 'Kasir']);

        $owner->givePermissionTo(Permission::all());
        $manager->givePermissionTo(['create-sale', 'manage-products', 'view-reports', 'manage-stock']);
        $cashier->givePermissionTo(['create-sale']);
    }
}
