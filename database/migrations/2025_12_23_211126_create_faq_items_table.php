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
        Schema::create('faq_items', function (Blueprint $table) {
            $table->id();
            $table->string('category')->default('general'); // Danh mục: giao_hang, doi_tra, san_pham, etc.
            $table->text('keywords'); // Từ khóa để tìm kiếm (JSON array)
            $table->text('question'); // Câu hỏi mẫu
            $table->text('answer'); // Câu trả lời
            $table->integer('priority')->default(0); // Độ ưu tiên (số càng cao càng ưu tiên)
            $table->boolean('is_active')->default(true); // Có hoạt động không
            $table->integer('usage_count')->default(0); // Số lần được sử dụng
            $table->timestamps();
            
            // Index để tìm kiếm nhanh
            $table->index(['category', 'is_active']);
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faq_items');
    }
};
