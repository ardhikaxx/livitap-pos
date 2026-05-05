<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tables to drop business_id from
        $tables = [
            'users', 'categories', 'outlets', 'products', 'customers', 
            'suppliers', 'purchase_orders', 'discounts', 'activity_logs'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // Drop foreign key if exists
                try {
                    $table->dropForeign([$tableName . '_business_id_foreign']);
                } catch (\Exception $e) {}
                
                $table->dropColumn('business_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not implemented for full removal
    }
};
