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
        Schema::table('comments', function (Blueprint $table) {
            // Add digital_product_id column (nullable because not all comments are for digital products)
            $table->unsignedBigInteger('digital_product_id')->nullable()->after('product_id');
            
            // Add digital_purchase_id to link to digital_product_purchases
            $table->unsignedBigInteger('digital_purchase_id')->nullable()->after('order_id');
            
            // Make product_id and order_id nullable since digital products don't use them
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->string('order_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['digital_product_id', 'digital_purchase_id']);
        });
    }
};