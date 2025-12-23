<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to add foreign key constraint
        DB::statement('ALTER TABLE digital_product_purchases ADD CONSTRAINT digital_product_purchases_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key constraint using raw SQL
        DB::statement('ALTER TABLE digital_product_purchases DROP FOREIGN KEY digital_product_purchases_user_id_foreign');
    }
};