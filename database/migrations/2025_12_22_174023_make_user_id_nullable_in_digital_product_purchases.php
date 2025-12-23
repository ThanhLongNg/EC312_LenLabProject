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
        Schema::table('digital_product_purchases', function (Blueprint $table) {
            // Add user_id column if it doesn't exist
            if (!Schema::hasColumn('digital_product_purchases', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('digital_product_purchases', function (Blueprint $table) {
            if (Schema::hasColumn('digital_product_purchases', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};