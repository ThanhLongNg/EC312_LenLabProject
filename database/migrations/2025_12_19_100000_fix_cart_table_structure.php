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
        // Check if cart table exists and fix its structure
        if (Schema::hasTable('cart')) {
            // Check if id column exists and is auto increment
            $columns = Schema::getColumnListing('cart');
            
            if (!in_array('id', $columns)) {
                // Add id column as primary key with auto increment
                Schema::table('cart', function (Blueprint $table) {
                    $table->id()->first();
                });
            } else {
                // Check if id column is auto increment, if not, fix it
                Schema::table('cart', function (Blueprint $table) {
                    $table->id()->change();
                });
            }
        } else {
            // Create cart table from scratch if it doesn't exist
            Schema::create('cart', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->integer('quantity')->default(1);
                $table->decimal('price_at_time', 10, 2)->nullable()->comment('Giá tại thời điểm thêm vào giỏ');
                $table->json('variant_info')->nullable()->comment('Thông tin variant (màu sắc, size, etc.)');
                $table->string('session_id')->nullable()->comment('Session ID cho guest users');
                $table->timestamps();
                
                // Add indexes for better performance
                $table->index(['user_id', 'product_id']);
                $table->index('session_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is designed to fix structure, so we don't want to reverse it
        // But if needed, you can drop and recreate the table
    }
};