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
    Schema::table('orders', function (Blueprint $table) {
        $table->string('refund_status')->default('none')->after('payment_status');
        $table->decimal('refund_amount', 12, 2)->nullable()->after('refund_status');
        $table->text('refund_note')->nullable()->after('refund_amount');
        $table->timestamp('refunded_at')->nullable()->after('refund_note');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
