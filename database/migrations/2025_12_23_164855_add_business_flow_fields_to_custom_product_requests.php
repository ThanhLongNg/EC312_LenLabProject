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
        Schema::table('custom_product_requests', function (Blueprint $table) {
            $table->integer('estimated_completion_days')->nullable()->after('deposit_percentage');
            $table->text('cancelled_reason')->nullable()->after('order_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_product_requests', function (Blueprint $table) {
            $table->dropColumn(['estimated_completion_days', 'cancelled_reason']);
        });
    }
};
