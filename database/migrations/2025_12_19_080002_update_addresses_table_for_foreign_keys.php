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
        Schema::table('addresses', function (Blueprint $table) {
            // Drop old columns if they exist
            if (Schema::hasColumn('addresses', 'province')) {
                $table->dropColumn('province');
            }
            if (Schema::hasColumn('addresses', 'ward')) {
                $table->dropColumn('ward');
            }
            
            // Add foreign key columns
            if (!Schema::hasColumn('addresses', 'province_id')) {
                $table->foreignId('province_id')->after('phone')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('addresses', 'ward_id')) {
                $table->foreignId('ward_id')->after('province_id')->constrained()->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
            $table->dropForeign(['ward_id']);
            $table->dropColumn(['province_id', 'ward_id']);
            
            $table->string('province')->after('phone');
            $table->string('ward')->after('province');
        });
    }
};