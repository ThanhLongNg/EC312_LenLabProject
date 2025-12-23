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
        // 1) Add digital_product_id (nullable)
        if (!Schema::hasColumn('comments', 'digital_product_id')) {
            $table->unsignedBigInteger('digital_product_id')->nullable()->after('product_id');
        }

        // 2) Add digital_purchase_id (nullable)
        if (!Schema::hasColumn('comments', 'digital_purchase_id')) {
            $table->unsignedBigInteger('digital_purchase_id')->nullable()->after('order_id');
        }

        // 3) Make product_id + order_id nullable (digital products don't use them)
        // product_id in current DB is int(11) => use unsignedInteger, NOT unsignedBigInteger
        $table->unsignedInteger('product_id')->nullable()->change();
        $table->string('order_id')->nullable()->change();
    });
}


    /**
     * Reverse the migrations.
     */
   public function down(): void
{
    Schema::table('comments', function (Blueprint $table) {
        if (Schema::hasColumn('comments', 'digital_product_id')) {
            $table->dropColumn('digital_product_id');
        }
        if (Schema::hasColumn('comments', 'digital_purchase_id')) {
            $table->dropColumn('digital_purchase_id');
        }

        // Optional: revert nullable if you want (không bắt buộc)
        // $table->unsignedInteger('product_id')->nullable(false)->change();
        // $table->string('order_id')->nullable(false)->change();
    });
}
};
