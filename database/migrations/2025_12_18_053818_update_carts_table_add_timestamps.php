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
        Schema::table('cart', function (Blueprint $table) {
            // Add timestamps if they don't exist
            if (!Schema::hasColumn('cart', 'created_at')) {
                $table->timestamps();
            }
            
            // Add additional fields for cart functionality
            if (!Schema::hasColumn('cart', 'price_at_time')) {
                $table->decimal('price_at_time', 10, 2)->nullable()->comment('Giá tại thời điểm thêm vào giỏ');
            }
            
            if (!Schema::hasColumn('cart', 'variant_info')) {
                $table->json('variant_info')->nullable()->comment('Thông tin variant (màu sắc, size, etc.)');
            }
            
            if (!Schema::hasColumn('cart', 'session_id')) {
                $table->string('session_id')->nullable()->comment('Session ID cho guest users');
            }
            
            // Add index for better performance
            $table->index(['user_id', 'product_id']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->dropColumn(['price_at_time', 'variant_info', 'session_id']);
            $table->dropIndex(['user_id', 'product_id']);
            $table->dropIndex(['session_id']);
        });
    }
};