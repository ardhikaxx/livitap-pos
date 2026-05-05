<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop shifts table
        Schema::dropIfExists('shifts');

        // 2. Remove shift_id from sales if exists
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'shift_id')) {
                $table->dropColumn('shift_id');
            }
        });
    }

    public function down(): void
    {
        // Not implemented for full removal
    }
};
