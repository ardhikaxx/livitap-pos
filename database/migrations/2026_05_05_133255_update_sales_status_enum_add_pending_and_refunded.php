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
        // For MySQL, we use a raw statement to ensure the enum is updated correctly
        DB::statement("ALTER TABLE sales MODIFY COLUMN status ENUM('paid', 'partial', 'unpaid', 'void', 'pending', 'refunded') DEFAULT 'unpaid'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE sales MODIFY COLUMN status ENUM('paid', 'partial', 'unpaid', 'void') DEFAULT 'paid'");
    }
};
