<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;

class OrderController extends BaseAdminController
{
    // Trang danh sách đơn hàng
    public function index()
    {
        $orders = Order::with('user', 'items')->get();
        return $this->view('admin.orders.index_simple', compact('orders'));
    }

    // API để DataTable load danh sách đơn hàng
    public function list()
    {
        $orders = Order::with('user')
            ->select('orders.*')
            ->orderBy('id', 'DESC')
            ->get();

        $mapped = $orders->map(function ($o) {
            return [
                'id' => $o->id,
                'order_code' => $o->order_code,
                'customer_name' => $o->user->name ?? 'Không có',
                'order_date' => $o->created_at->format('d/m/Y H:i'),
                'total' => $o->total_amount,
                'status' => $o->status
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $mapped
        ]);
    }

    // Chi tiết đơn hàng
    public function show($id)
    {
        $order = Order::with('items.product', 'user')->where('order_id', $id)->firstOrFail();

        return $this->view('admin.orders.detail_simple', [
            'order' => $order
        ]);
    }

    // Xóa đơn hàng
    public function destroy($id)
    {
        Order::where('order_id', $id)->firstOrFail()->delete();

        return back()->with('success', 'Đơn hàng đã được xóa');
    }

    // Xóa nhiều đơn hàng
    public function bulkDelete(Request $request)
    {
        Order::whereIn('id', $request->ids)->delete();

        return response()->json(['success' => true]);
    }

    // Form thêm đơn hàng
    public function create()
    {
        $products = Product::all();
        return $this->view('admin.orders.create_simple', compact('products'));
    }

    // Lưu đơn hàng từ AddOrder
    public function store(Request $request)
    {
        $request->validate([
            'full_name'  => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'status' => 'required',
            'total_amount' => 'required|numeric|min:0',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
        ]);

        $orderId = time() . rand(1000, 9999);

        $order = Order::create([
            'order_id' => $orderId,
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'province' => $request->province,
            'district' => $request->district,
            'specific_address' => $request->specific_address,
            'shipping_fee' => $request->shipping_fee ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'payment_method' => $request->payment_method ?? 'cod',
            'order_note' => $request->note,
            'status' => $request->status,
            'total_amount' => $request->total_amount,
            'created_at' => now(),
        ]);

        // Lưu order items
        foreach ($request->products as $productData) {
            if (!empty($productData['id']) && !empty($productData['quantity'])) {
                OrderItem::create([
                    'order_id' => $orderId, // Sử dụng order_id thay vì id
                    'product_id' => $productData['id'],
                    'quantity' => $productData['quantity'],
                    'price' => $productData['price'] ?? 0,
                ]);
            }
        }

        return redirect()->route('admin.orders.index')->with('success', 'Tạo đơn hàng thành công!');
    }

    // Lấy giá sản phẩm
    public function productPrice($id)
    {
        $product = Product::findOrFail($id);
        return response()->json(['price' => $product->price]);
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,confirmed,shipping,delivered,cancelled'
            ]);

            // Kiểm tra order tồn tại
            $orderExists = \DB::table('orders')->where('order_id', $id)->exists();
            if (!$orderExists) {
                return redirect()->route('admin.orders.index')->with('error', "Không tìm thấy đơn hàng với ID: {$id}");
            }

            // Lấy trạng thái cũ
            $oldStatus = \DB::table('orders')->where('order_id', $id)->value('status');
            
            // Update trạng thái
            $updated = \DB::table('orders')
                ->where('order_id', $id)
                ->update(['status' => $request->status]);

            if ($updated) {
                return redirect()->route('admin.orders.index')->with('success', "Cập nhật trạng thái đơn hàng ORD-{$id} từ '{$oldStatus}' thành '{$request->status}' thành công!");
            } else {
                return redirect()->route('admin.orders.index')->with('error', 'Không thể cập nhật trạng thái đơn hàng!');
            }
            
        } catch (\Exception $e) {
            return redirect()->route('admin.orders.index')->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
