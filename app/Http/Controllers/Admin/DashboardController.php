<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseAdminController
{
    public function index()
    {
        // Kiểm tra bảng tồn tại trước khi query
        $customerCount = \Schema::hasTable('users') ? DB::table('users')->count() : 0;
        $productCount = \Schema::hasTable('products') ? DB::table('products')->count() : 0;
        $orderCount = \Schema::hasTable('orders') ? DB::table('orders')->count() : 0;
        $pendingOrderCount = \Schema::hasTable('orders') ? DB::table('orders')->where('status', 'pending')->count() : 0;
        
        $recentOrders = [];
        if (\Schema::hasTable('orders') && \Schema::hasTable('users')) {
            try {
                $recentOrders = DB::table('orders')
                    ->join('users', 'orders.user_id', '=', 'users.id')
                    ->select('orders.*', 'users.name as full_name')
                    ->orderBy('orders.created_at', 'desc')
                    ->limit(5)
                    ->get();
            } catch (\Exception $e) {
                $recentOrders = [];
            }
        }

        // Pass required variables for admin layout
        return $this->view('admin.dashboard_modern', [
            'customerCount' => $customerCount,
            'productCount' => $productCount,
            'orderCount' => $orderCount,
            'pendingOrderCount' => $pendingOrderCount,
            'recentOrders' => $recentOrders,
            'revenueByMonth' => [],
            'incomeExpenseByMonth' => [],
        ]);
    }
}
