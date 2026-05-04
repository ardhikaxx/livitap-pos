<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = config('livitap.permissions', []);

        foreach ($permissions as $key => $value) {
            Permission::firstOrCreate(['name' => $key, 'guard_name' => 'web']);
        }

        // Create Roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $kasir = Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);
        $gudang = Role::firstOrCreate(['name' => 'stock_keeper', 'guard_name' => 'web']);
        $waiter = Role::firstOrCreate(['name' => 'waiter', 'guard_name' => 'web']);

        // Super Admin - all permissions
        $superAdmin->syncPermissions(Permission::all());

        // Owner permissions
        $owner->syncPermissions([
            'create-sale', 'void-sale', 'hold-sale',
            'create-product', 'edit-product', 'delete-product',
            'adjust-stock', 'transfer-stock', 'stock-opname',
            'view-sales-report', 'view-stock-report', 'view-financial-report',
            'manage-users', 'assign-outlet',
            'manage-settings', 'manage-discounts',
            'create-customer', 'edit-customer', 'delete-customer',
        ]);

        // Manager permissions
        $manager->syncPermissions([
            'create-sale', 'void-sale', 'hold-sale',
            'edit-product',
            'adjust-stock', 'transfer-stock', 'stock-opname',
            'view-sales-report', 'view-stock-report', 'view-financial-report',
            'assign-outlet',
            'manage-discounts',
            'create-customer', 'edit-customer',
        ]);

        // Kasir permissions
        $kasir->syncPermissions([
            'create-sale', 'hold-sale',
            'create-customer',
            'view-sales-report',
        ]);

        // Gudang permissions
        $gudang->syncPermissions([
            'adjust-stock', 'transfer-stock', 'stock-opname',
            'view-stock-report',
        ]);

        // Waiter permissions (F&B)
        $waiter->syncPermissions([
            'create-sale',
        ]);
    }
}
