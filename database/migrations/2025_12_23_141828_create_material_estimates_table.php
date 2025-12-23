<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('material_estimates', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('product_type'); // áo, khăn, thú len
            $table->string('size'); // S, M, L
            $table->string('yarn_type'); // cotton, wool, acrylic
            $table->json('estimated_materials'); // Danh sách nguyên liệu và số lượng
            $table->decimal('total_estimated_cost', 10, 2)->nullable();
            $table->boolean('added_to_cart')->default(false);
            $table->timestamps();
            
            $table->index(['session_id', 'created_at']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('material_estimates');
    }
};