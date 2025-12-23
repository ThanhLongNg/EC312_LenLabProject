<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if posts table exists and fix AUTO_INCREMENT
        if (Schema::hasTable('posts')) {
            // Check if id column exists and fix AUTO_INCREMENT
            $columns = Schema::getColumnListing('posts');
            if (in_array('id', $columns)) {
                // Fix AUTO_INCREMENT for id column
                DB::statement('ALTER TABLE posts MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
                
                // Reset AUTO_INCREMENT to start from 1 if table is empty
                $count = DB::table('posts')->count();
                if ($count == 0) {
                    DB::statement('ALTER TABLE posts AUTO_INCREMENT = 1');
                }
                
                echo "Fixed posts table id AUTO_INCREMENT\n";
            }
        }
    }

    public function down(): void
    {
        // Cannot easily reverse AUTO_INCREMENT changes
    }
};