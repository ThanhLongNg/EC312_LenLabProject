<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();

            $table->string('category')->nullable(); // TIPS/TREND...
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();

            $table->string('thumbnail')->nullable(); // path ảnh

            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();

            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
