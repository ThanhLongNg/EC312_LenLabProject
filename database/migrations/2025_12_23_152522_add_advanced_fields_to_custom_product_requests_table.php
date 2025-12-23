<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('custom_product_requests', function (Blueprint $table) {
            // Thêm các trường mới cho nghiệp vụ nâng cấp
            $table->json('reference_images')->nullable()->after('description'); // Đường dẫn ảnh tham khảo
            $table->text('admin_response')->nullable()->after('admin_notes'); // Phản hồi của admin
            $table->timestamp('admin_responded_at')->nullable()->after('admin_response'); // Thời gian admin phản hồi
            $table->decimal('deposit_amount', 10, 2)->nullable()->after('estimated_price'); // Số tiền đặt cọc
            $table->decimal('deposit_percentage', 5, 2)->default(30.00)->after('deposit_amount'); // % đặt cọc
            $table->boolean('deposit_paid')->default(false)->after('deposit_percentage'); // Đã đặt cọc chưa
            $table->timestamp('deposit_paid_at')->nullable()->after('deposit_paid'); // Thời gian đặt cọc
            $table->decimal('remaining_amount', 10, 2)->nullable()->after('deposit_paid_at'); // Số tiền còn lại
            $table->boolean('final_payment_paid')->default(false)->after('remaining_amount'); // Đã thanh toán cuối chưa
            $table->timestamp('final_payment_paid_at')->nullable()->after('final_payment_paid'); // Thời gian thanh toán cuối
            $table->json('shipping_address')->nullable()->after('final_payment_paid_at'); // Địa chỉ giao hàng
            $table->string('order_code')->nullable()->after('shipping_address'); // Mã đơn hàng
            
            // Cập nhật enum status
            $table->dropColumn('status');
        });
        
        // Thêm lại column status với các giá trị mới
        Schema::table('custom_product_requests', function (Blueprint $table) {
            $table->enum('status', [
                'pending_info',           // Đang thu thập thông tin
                'pending_images',         // Chờ upload ảnh
                'pending_admin_response', // Chờ admin phản hồi
                'admin_responded',        // Admin đã phản hồi
                'confirmed',              // Đã xác nhận yêu cầu
                'deposit_required',       // Yêu cầu đặt cọc
                'deposit_paid',           // Đã đặt cọc
                'in_production',          // Đang sản xuất
                'production_completed',   // Hoàn thành sản xuất
                'final_payment_required', // Yêu cầu thanh toán cuối
                'final_payment_paid',     // Đã thanh toán cuối
                'shipping',               // Đang giao hàng
                'completed',              // Hoàn thành
                'cancelled'               // Đã hủy
            ])->default('pending_info')->after('contact_info');
        });
    }

    public function down()
    {
        Schema::table('custom_product_requests', function (Blueprint $table) {
            $table->dropColumn([
                'reference_images',
                'admin_response',
                'admin_responded_at',
                'deposit_amount',
                'deposit_percentage',
                'deposit_paid',
                'deposit_paid_at',
                'remaining_amount',
                'final_payment_paid',
                'final_payment_paid_at',
                'shipping_address',
                'order_code',
                'status'
            ]);
            
            // Khôi phục status cũ
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
        });
    }
};