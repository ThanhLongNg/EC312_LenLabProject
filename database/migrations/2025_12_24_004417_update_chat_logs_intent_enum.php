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
        Schema::table('chat_logs', function (Blueprint $table) {
            // Drop the old enum column
            $table->dropColumn('intent');
        });

        Schema::table('chat_logs', function (Blueprint $table) {
            // Add the new enum column with additional values
            $table->enum('intent', [
                'FAQ', 
                'CUSTOM_REQUEST', 
                'MATERIAL_ESTIMATE', 
                'ADMIN_NOTIFICATION',
                'ADMIN_MESSAGE',
                'UNKNOWN'
            ])->default('UNKNOWN')->after('bot_reply');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_logs', function (Blueprint $table) {
            // Drop the new enum column
            $table->dropColumn('intent');
        });

        Schema::table('chat_logs', function (Blueprint $table) {
            // Restore the old enum column
            $table->enum('intent', ['FAQ', 'CUSTOM_REQUEST', 'MATERIAL_ESTIMATE', 'UNKNOWN'])
                  ->default('UNKNOWN')
                  ->after('bot_reply');
        });
    }
};
