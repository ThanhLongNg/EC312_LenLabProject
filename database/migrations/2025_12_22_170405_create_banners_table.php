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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();

            // Vị trí banner: home | products
            $table->string('position')->unique();

            // Tiêu đề banner (tuỳ chọn)
            $table->string('title')->nullable();

            // Link khi click banner
            $table->string('link')->nullable();

            // Đường dẫn ảnh banner (storage)
            $table->string('image')->nullable();

            // Trạng thái bật / tắt
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
