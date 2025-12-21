<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('cart', function (Blueprint $table) {
        $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
        $table->index('variant_id');
    });
}

public function down(): void
{
    Schema::table('cart', function (Blueprint $table) {
        $table->dropIndex(['variant_id']);
        $table->dropColumn('variant_id');
    });
}

};
