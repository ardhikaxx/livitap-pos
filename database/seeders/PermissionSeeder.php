<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::create(['name' => 'create-sale']);
        Permission::create(['name' => 'manage-products']);

        $owner = Role::create(['name' => 'Owner']);
        $manager = Role::create(['name' => 'Manager']);
        $cashier = Role::create(['name' => 'Kasir']);

        $owner->givePermissionTo(Permission::all());
        $manager->givePermissionTo(['create-sale', 'manage-products']);
        $cashier->givePermissionTo(['create-sale']);
    }
}
