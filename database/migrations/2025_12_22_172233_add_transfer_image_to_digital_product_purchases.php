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
            // Add transfer image field if it doesn't exist
            if (!Schema::hasColumn('digital_product_purchases', 'transfer_image')) {
                $table->string('transfer_image')->nullable()->after('download_history');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('digital_product_purchases', function (Blueprint $table) {
            if (Schema::hasColumn('digital_product_purchases', 'transfer_image')) {
                $table->dropColumn('transfer_image');
            }
        });
    }
};