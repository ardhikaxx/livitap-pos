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
        Schema::table('product_prices', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
            $table->dropColumn('outlet_id');
        });

        Schema::table('product_stocks', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
            $table->dropColumn('outlet_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('outlet_id');
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('outlet_id');
        });

        Schema::table('cash_flows', function (Blueprint $table) {
            $table->dropColumn('outlet_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->foreignId('outlet_id')->constrained()->onDelete('cascade');
        });

        Schema::table('product_stocks', function (Blueprint $table) {
            $table->foreignId('outlet_id')->constrained()->onDelete('cascade');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('outlet_id')->after('id');
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->unsignedBigInteger('outlet_id')->after('id');
        });

        Schema::table('cash_flows', function (Blueprint $table) {
            $table->unsignedBigInteger('outlet_id')->after('id');
        });
    }
};
