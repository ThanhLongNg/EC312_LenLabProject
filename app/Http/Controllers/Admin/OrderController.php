<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;

class OrderController extends Controller
{
    // Trang danh sách đơn hàng + lọc
    public function index(Request $request)
    {
        $q = trim($request->q ?? '');
        $status = $request->status;
        $paymentMethod = $request->payment_method;

        $orders = Order::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('order_id', 'like', "%{$q}%")
                        ->orWhere('full_name', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($status, fn($query) => $query->where('status', $status))
            ->when($paymentMethod, fn($query) => $query->where('payment_method', $paymentMethod))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString(); // giữ filter khi bấm pagination

        return view('admin.orders.index_simple', compact('orders'));
    }

    // API để DataTable load danh sách đơn hàng
    public function list()
    {
        $orders = Order::with('user')
            ->select('orders.*')
            ->orderBy('created_at', 'DESC')
            ->get();

        $mapped = $orders->map(function ($o) {
            return [
                'id' => $o->order_id,
                'order_code' => $o->order_id,
                'customer_name' => $o->user->name ?? ($o->full_name ?? 'Không có'),
                'order_date' => optional($o->created_at)->format('d/m/Y H:i'),
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
        $order = Order::with(['orderItems', 'user'])
            ->where('order_id', $id)
            ->firstOrFail();

        return view('admin.orders.detail_simple', compact('order'));
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
        $ids = $request->input('ids', []);
        Order::whereIn('order_id', $ids)->delete();

        return response()->json(['success' => true]);
    }

    // Form thêm đơn hàng
    public function create()
    {
        $products = Product::all();
        return view('admin.orders.create_simple', compact('products'));
    }

    // Lưu đơn hàng từ AddOrder
    public function store(Request $request)
    {
        $request->validate([
            'full_name'  => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'total_amount' => 'required|numeric|min:0',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
        ]);

        $orderId = time() . rand(1000, 9999);

        DB::transaction(function () use ($request, $orderId) {
            $order = Order::create([
                'order_id' => $orderId,
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
                'created_at' => now(),
            ]);

            foreach ($request->products as $productData) {
                if (!empty($productData['id']) && !empty($productData['quantity'])) {
                    OrderItem::create([
                        'order_id' => $orderId,
                        'product_id' => $productData['id'],
                        'quantity' => $productData['quantity'],
                        'price' => $productData['price'] ?? 0,
                    ]);
                }
            }
        });

        return redirect()->route('admin.orders.index')->with('success', 'Tạo đơn hàng thành công!');
    }

    // Lấy giá sản phẩm
    public function productPrice($id)
    {
        $product = Product::findOrFail($id);
        return response()->json(['price' => $product->price]);
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus(Request $request, \App\Models\Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        // Không cho đổi trạng thái nếu đã hủy
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Đơn đã hủy không thể cập nhật trạng thái.');
        }

        // Không cho đổi trạng thái nếu đã giao
        if ($order->status === 'delivered') {
            return back()->with('error', 'Đơn đã giao không thể cập nhật.');
        }

        $order->status = $request->status;
        $order->save();

        return back()->with('success', 'Cập nhật trạng thái thành công.');
    }

    public function cancel(Request $request, \App\Models\Order $order)
    {
        if ($order->status === 'delivered') {
            return back()->with('error', 'Đơn đã giao không thể hủy.');
        }

        $order->status = 'cancelled';

        // Nếu chuyển khoản và đã thanh toán thì chuyển sang requested (chờ hoàn)
        if ($order->payment_method === 'bank_transfer' && $order->payment_status === 'paid') {
            $order->refund_status = 'requested';
            $order->refund_amount = $order->refund_amount ?? $order->total_amount;
        }

        $order->save();

        return back()->with('success', 'Đã hủy đơn hàng.');
    }

    // ✅ Hoàn tiền (fix binding + logic payment_status)
    public function refund(Request $request, $orderId)
    {
        $order = Order::where('order_id', $orderId)->firstOrFail();

        $request->validate([
            'refund_status' => 'required|in:requested,refunded,rejected,none',
            'refund_amount' => 'nullable|numeric|min:0',
            'refund_note'   => 'nullable|string|max:2000',
        ]);

        if ($order->payment_method !== 'bank_transfer') {
            return back()->with('error', 'Đơn COD không cần hoàn tiền.');
        }

        DB::transaction(function () use ($request, $order) {
            $order->refund_status = $request->refund_status;

            $order->refund_amount = $request->refund_amount
                ?? $order->refund_amount
                ?? $order->total_amount;

            $order->refund_note = $request->refund_note;

            if ($request->refund_status === 'refunded') {
                $order->refunded_at = now();
                $order->payment_status = 'refunded';
            } elseif ($request->refund_status === 'requested') {
                $order->refunded_at = null;
                $order->payment_status = 'refunding';
            } else {
                // none / rejected
                $order->refunded_at = null;
                if ($order->payment_status === 'refunding') {
                    $order->payment_status = 'paid';
                }
            }

            $order->save();
        });

        return back()->with('success', 'Cập nhật hoàn tiền thành công.');
    }
}
