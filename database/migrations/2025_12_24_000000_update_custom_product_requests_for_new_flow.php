<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('custom_product_requests', function (Blueprint $table) {
            // Xóa các cột không cần thiết cho flow mới (không dùng đặt cọc)
            $table->dropColumn([
                'deposit_amount',
                'deposit_percentage', 
                'deposit_paid',
                'deposit_paid_at',
                'remaining_amount',
                'final_payment_paid',
                'final_payment_paid_at'
            ]);
            
            // Thêm các cột mới cho flow thanh toán 1 lần
            $table->decimal('final_price', 10, 2)->nullable()->after('estimated_price');
            $table->integer('estimated_completion_days')->nullable()->after('final_price');
            $table->json('payment_info')->nullable()->after('estimated_completion_days');
            $table->string('payment_bill_image')->nullable()->after('payment_info');
            $table->timestamp('payment_submitted_at')->nullable()->after('payment_bill_image');
            $table->timestamp('payment_confirmed_at')->nullable()->after('payment_submitted_at');
            $table->text('cancelled_reason')->nullable()->after('payment_confirmed_at');
        });
        
        // Cập nhật enum status cho flow mới
        Schema::table('custom_product_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('custom_product_requests', function (Blueprint $table) {
            $table->enum('status', [
                'pending_admin_response',  // Chờ admin phản hồi
                'in_discussion',          // Đang trao đổi với admin
                'awaiting_payment',       // Chờ thanh toán (1 lần)
                'payment_submitted',      // Đã gửi bill - Chờ xác nhận
                'paid',                   // Đã thanh toán - Đang sản xuất
                'completed',              // Hoàn thành
                'cancelled'               // Đã hủy
            ])->default('pending_admin_response')->after('contact_info');
        });
    }

    public function down()
    {
        Schema::table('custom_product_requests', function (Blueprint $table) {
            // Khôi phục các cột cũ
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->decimal('deposit_percentage', 5, 2)->default(30.00);
            $table->boolean('deposit_paid')->default(false);
            $table->timestamp('deposit_paid_at')->nullable();
            $table->decimal('remaining_amount', 10, 2)->nullable();
            $table->boolean('final_payment_paid')->default(false);
            $table->timestamp('final_payment_paid_at')->nullable();
            
            // Xóa các cột mới
            $table->dropColumn([
                'final_price',
                'estimated_completion_days',
                'payment_info',
                'payment_bill_image',
                'payment_submitted_at',
                'payment_confirmed_at',
                'cancelled_reason',
                'status'
            ]);
            
            // Khôi phục status cũ
            $table->enum('status', [
                'pending_info',
                'pending_images',
                'pending_admin_response',
                'admin_responded',
                'confirmed',
                'deposit_required',
                'deposit_paid',
                'in_production',
                'production_completed',
                'final_payment_required',
                'final_payment_paid',
                'shipping',
                'completed',
                'cancelled'
            ])->default('pending_info');
        });
    }
};