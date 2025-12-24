<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_support_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('custom_request_id');
            $table->enum('sender_type', ['customer', 'admin']); // Người gửi
            $table->unsignedBigInteger('sender_id')->nullable(); // ID của admin hoặc user
            $table->text('message'); // Nội dung tin nhắn
            $table->json('attachments')->nullable(); // Ảnh đính kèm
            $table->boolean('is_read')->default(false); // Đã đọc chưa
            $table->timestamps();
            
            $table->foreign('custom_request_id')->references('id')->on('custom_product_requests')->onDelete('cascade');
            $table->index(['custom_request_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_support_logs');
    }
};