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
        // Fix addresses table auto increment issue
        try {
            // First, check if the table has the correct structure
            $tableExists = Schema::hasTable('addresses');
            
            if ($tableExists) {
                // Drop and recreate the table with correct structure
                Schema::dropIfExists('addresses');
                
                Schema::create('addresses', function (Blueprint $table) {
                    $table->id(); // This creates auto-increment primary key
                    $table->foreignId('user_id')->constrained()->onDelete('cascade');
                    $table->string('full_name');
                    $table->string('phone');
                    $table->foreignId('province_id')->constrained()->onDelete('cascade');
                    $table->foreignId('ward_id')->constrained()->onDelete('cascade');
                    $table->string('specific_address');
                    $table->boolean('is_default')->default(false);
                    $table->timestamps();
                });
            }
        } catch (\Exception $e) {
            // If the above fails, try a simpler approach
            DB::statement('ALTER TABLE addresses AUTO_INCREMENT = 1');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this fix
    }
};
