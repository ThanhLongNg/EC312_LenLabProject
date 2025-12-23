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
        Schema::create('digital_product_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('digital_product_id')->constrained()->onDelete('cascade');
            $table->string('order_code')->nullable();
            $table->decimal('purchase_price', 10, 2);
            $table->integer('download_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->json('download_links')->nullable();
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])->default('pending');
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'digital_product_id']);
            $table->index('order_code');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_product_purchases');
    }
};