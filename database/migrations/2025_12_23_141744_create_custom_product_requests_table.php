<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('custom_product_requests', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('product_type'); // áo, khăn, thú len, etc.
            $table->string('size')->nullable(); // S, M, L hoặc kích thước cụ thể
            $table->text('description'); // Mô tả chi tiết yêu cầu
            $table->string('contact_info')->nullable(); // Email hoặc phone
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->text('admin_notes')->nullable(); // Ghi chú của admin
            $table->decimal('estimated_price', 10, 2)->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('custom_product_requests');
    }
};