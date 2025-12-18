<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'birthday')) {
                $table->date('birthday')->nullable()->after('gender');
            }

            if (!Schema::hasColumn('users', 'orders_count')) {
                $table->unsignedInteger('orders_count')->default(0);
            }

            if (!Schema::hasColumn('users', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }

            if (!Schema::hasColumn('users', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'birthday',
                'orders_count',
                'created_at',
                'updated_at',
            ]);
        });
    }
};

