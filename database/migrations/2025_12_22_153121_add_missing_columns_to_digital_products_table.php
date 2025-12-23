<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('digital_products', function (Blueprint $table) {

            // Thông tin sản phẩm
            if (!Schema::hasColumn('digital_products', 'name')) {
                $table->string('name', 255)->after('id');
            }

            if (!Schema::hasColumn('digital_products', 'description')) {
                $table->text('description')->nullable()->after('name');
            }

            if (!Schema::hasColumn('digital_products', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('description');
            }

            if (!Schema::hasColumn('digital_products', 'type')) {
                $table->string('type', 255)->default('file')->after('price');
            }

            // Hiển thị
            if (!Schema::hasColumn('digital_products', 'thumbnail')) {
                $table->string('thumbnail', 255)->nullable()->after('email_template');
            }

            if (!Schema::hasColumn('digital_products', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('thumbnail');
            }
        });
    }

    public function down(): void
    {
        Schema::table('digital_products', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'description',
                'price',
                'type',
                'thumbnail',
                'is_active'
            ]);
        });
    }
};
