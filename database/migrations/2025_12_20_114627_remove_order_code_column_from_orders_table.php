<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tên index mà Laravel hay tạo khi unique('order_code'):
        $indexName = 'orders_order_code_unique';

        // 1) Check index tồn tại rồi mới drop (tránh lỗi 1091)
        $dbName = DB::getDatabaseName();

        $indexExists = DB::table('information_schema.statistics')
            ->where('table_schema', $dbName)
            ->where('table_name', 'orders')
            ->where('index_name', $indexName)
            ->exists();

        if ($indexExists) {
            DB::statement("ALTER TABLE `orders` DROP INDEX `$indexName`");
        }

        // 2) Drop column order_code nếu có
        if (Schema::hasColumn('orders', 'order_code')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('order_code');
            });
        }
    }

    public function down(): void
    {
        // rollback: tạo lại column (nếu bạn cần)
        if (!Schema::hasColumn('orders', 'order_code')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('order_code')->nullable();
                // nếu bạn muốn unique lại:
                // $table->unique('order_code');
            });
        }
    }
};
