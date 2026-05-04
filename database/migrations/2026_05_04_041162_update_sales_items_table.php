<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('sale_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('table_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('order_type', ['dine_in', 'takeaway', 'delivery'])->default('dine_in');
            $table->foreignId('shift_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['sale', 'refund'])->default('sale');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->string('sku_snapshot')->nullable();
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('buy_price', 15, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['sku_snapshot', 'discount_amount', 'tax_amount', 'buy_price']);
        });
        
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['customer_id', 'sale_date', 'notes', 'table_id', 'order_type', 'shift_id', 'type']);
        });
    }
};