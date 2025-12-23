<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_logs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id'); // Cho guest users
            $table->unsignedBigInteger('user_id')->nullable(); // Cho logged users
            $table->text('user_message');
            $table->text('bot_reply');
            $table->enum('intent', ['FAQ', 'CUSTOM_REQUEST', 'MATERIAL_ESTIMATE', 'UNKNOWN'])->default('UNKNOWN');
            $table->json('context')->nullable(); // Lưu context cho multi-step conversations
            $table->timestamps();
            
            $table->index(['session_id', 'created_at']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_logs');
    }
};