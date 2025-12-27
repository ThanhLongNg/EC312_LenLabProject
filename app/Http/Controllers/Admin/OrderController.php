<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends BaseAdminController
{
    /**
     * Display orders list for admin
     */
    public function index()
    {
        $orders = Order::with(['orderItems.product', 'user'])
                      ->orderBy('order_id', 'desc')
                      ->paginate(20);

        return $this->view('admin.orders.index_simple', compact('orders'));
    }

    /**
     * Get orders list as JSON for DataTables
     */
    public function list(Request $request)
    {
        $query = Order::with(['orderItems.product', 'user']);

        // Apply filters
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status') && $request->payment_status !== '') {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('order_id', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders->items(),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total()
            ]
        ]);
    }

    /**
     * Show order details
     */
    public function show(Order $order)
    {
        $order->load(['orderItems.product', 'user']);
        
        return $this->view('admin.orders.detail_simple', compact('order'));
    }

    /**
     * Show create order form
     */
    public function create()
    {
        $products = Product::where('status', 'active')->get();
        
        return $this->view('admin.orders.create_simple', compact('products'));
    }

    /**
     * Store new order
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'province' => 'required|string|max:255',
            'specific_address' => 'required|string|max:500',
            'payment_method' => 'required|string|in:cod,bank_transfer',
            'status' => 'required|string|in:processing,shipping,delivered,cancelled',
            'total_amount' => 'required|numeric|min:0',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
        ]);

        $orderId = time() . rand(1000, 9999);

        DB::transaction(function () use ($request, $orderId) {
            $order = Order::create([
                'order_id' => $orderId,
                'order_code' => $orderId,  // Thêm order_code
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'province' => $request->province,
                'district' => $request->district, // nếu DB không có district thì bỏ dòng này
                'specific_address' => $request->specific_address,
                'shipping_fee' => $request->shipping_fee ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'payment_method' => $request->payment_method ?? 'cod',
                'payment_status' => $request->payment_status ?? 'pending',
                'order_note' => $request->note,
                'status' => $request->status,
                'total_amount' => $request->total_amount,
                'shipping_address' => json_encode([
                    'specific_address' => $request->specific_address ?? '',
                    'district' => $request->district ?? '',
                    'province' => $request->province ?? '',
                ], JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
            ]);

            foreach ($request->products as $productData) {
                if (!empty($productData['id']) && !empty($productData['quantity'])) {
                    OrderItem::create([
                        'order_id' => $orderId,
                        'product_id' => $productData['id'],
                        'product_name' => Product::find($productData['id'])->name ?? '',
                        'product_image' => Product::find($productData['id'])->image ?? '',
                        'quantity' => $productData['quantity'],
                        'price' => Product::find($productData['id'])->price ?? 0,
                        'total' => (Product::find($productData['id'])->price ?? 0) * $productData['quantity']
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Đơn hàng đã được tạo thành công',
            'order_id' => $orderId
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:processing,shipping,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Trạng thái đơn hàng đã được cập nhật'
        ]);
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, Order $order)
    {
        if ($order->status === 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy đơn hàng đã giao'
            ], 400);
        }

        $order->update([
            'status' => 'cancelled',
            'cancelled_reason' => $request->reason ?? 'Admin cancelled'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đơn hàng đã được hủy'
        ]);
    }

    /**
     * Delete order
     */
    public function destroy(Order $order)
    {
        try {
            DB::transaction(function () use ($order) {
                // Delete order items first
                $order->orderItems()->delete();
                
                // Delete order
                $order->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Đơn hàng đã được xóa thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa đơn hàng'
            ], 500);
        }
    }

    /**
     * Bulk delete orders
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,order_id'
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Delete order items first
                OrderItem::whereIn('order_id', $request->order_ids)->delete();
                
                // Delete orders
                Order::whereIn('order_id', $request->order_ids)->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa ' . count($request->order_ids) . ' đơn hàng'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa đơn hàng'
            ], 500);
        }
    }

    /**
     * Get product price for order creation
     */
    public function productPrice($id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'price' => $product->price,
            'name' => $product->name
        ]);
    }

    /**
     * Process refund
     */
    public function refund(Request $request, Order $order)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0|max:' . $order->total_amount,
            'refund_reason' => 'required|string|max:500'
        ]);

        $order->update([
            'payment_status' => 'refunded',
            'refund_amount' => $request->refund_amount,
            'refund_reason' => $request->refund_reason,
            'refunded_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã xử lý hoàn tiền thành công'
        ]);
    }
}