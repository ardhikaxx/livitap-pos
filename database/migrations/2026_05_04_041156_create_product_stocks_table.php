<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('outlet_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->decimal('qty', 15, 2)->default(0);
            $table->decimal('min_qty', 15, 2)->default(0);
            $table->decimal('max_qty', 15, 2)->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'outlet_id', 'variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stocks');
    }
};
