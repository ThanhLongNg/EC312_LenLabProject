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
        Schema::table('comments', function (Blueprint $table) {
            // Add order_id column after product_id if it doesn't exist
            if (!Schema::hasColumn('comments', 'order_id')) {
                $table->string('order_id', 30)->after('product_id');
            }
            
            // Add is_verified column after comment (default 1) if it doesn't exist
            if (!Schema::hasColumn('comments', 'is_verified')) {
                $table->tinyInteger('is_verified')->default(1)->after('comment');
            }
            
            // Add is_hidden column after is_verified (default 0) if it doesn't exist
            if (!Schema::hasColumn('comments', 'is_hidden')) {
                $table->tinyInteger('is_hidden')->default(0)->after('is_verified');
            }
            
            // Add created_at column if it doesn't exist
            if (!Schema::hasColumn('comments', 'created_at')) {
                $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'is_verified', 'is_hidden']);
            
            // Only drop created_at if we added it
            if (Schema::hasColumn('comments', 'created_at')) {
                $table->dropColumn('created_at');
            }
        });
    }
};
