<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    // Hiển thị view checkout
    public function showCheckout()
    {
        return view('checkout');
    }

    // Hiển thị view order detail
    public function showOrderDetail($code)
    {
        return view('order_detail', ['orderCode' => $code]);
    }

    // Tóm tắt giỏ hàng cho trang checkout
    public function summary()
    {
        $userId = Auth::id();

        $cartItems = Cart::where('user_id', $userId)
            ->with(['variant', 'product'])
            ->get();

        $subtotal = $cartItems->sum(function ($item) {
            return $item->variant->price * $item->quantity;
        });

        return response()->json([
            'cart_items' => $cartItems,
            'subtotal'   => $subtotal,
        ]);
    }

    // Hàm generate mã đơn hàng kiểu C: LL-XXXXXXXX
    protected function generateOrderCode(): string
    {
        return 'LL-' . strtoupper(Str::random(8));
    }

    // Tạo đơn hàng sau khi user điền form checkout
    public function createOrder(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'full_name'        => 'required|string|max:255',
            'phone'            => 'required|string|max:20',
            'email'            => 'required|email',
            'province'         => 'required|string',
            'district'         => 'required|string',
            'specific_address' => 'required|string',
            'shipping_method'  => 'required|in:store,delivery',
            'note'             => 'nullable|string',
            'voucher_code'     => 'nullable|string',
        ]);

        // Lấy giỏ hàng
        $cartItems = Cart::where('user_id', $userId)
            ->with('variant')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng trống.',
            ], 400);
        }

        $subtotal = $cartItems->sum(fn($item) => $item->variant->price * $item->quantity);

        // Phí ship: nhận tại cửa hàng = 0, giao hàng = 30000 (bạn tuỳ chỉnh)
        $shippingFee = $request->shipping_method === 'store' ? 0 : 30000;

        // Voucher (nếu có)
        $discount = 0;
        if ($request->voucher_code) {
            $voucher = Voucher::where('code', $request->voucher_code)
                ->where('active', 1)
                ->first();

            if ($voucher) {
                // kiểm tra hạn, đơn tối thiểu giống CartController
                $now = now();

                if ($voucher->start_date && $now->lt($voucher->start_date)) {
                    // chưa bắt đầu -> bỏ qua
                } elseif ($voucher->end_date && $now->gt($voucher->end_date)) {
                    // hết hạn -> bỏ qua
                } elseif ($subtotal >= $voucher->min_order_value) {
                    if ($voucher->type === 'fixed') {
                        $discount = $voucher->discount_value;
                    } else {
                        $discount = intval($subtotal * ($voucher->discount_value / 100));
                    }
                }
            }
        }

        $total = max(0, $subtotal + $shippingFee - $discount);

        // Tạo order
        $order = Order::create([
            'order_code'       => $this->generateOrderCode(),
            'user_id'          => $userId,
            'full_name'        => $request->full_name,
            'phone'            => $request->phone,
            'email'            => $request->email,
            'province'         => $request->province,
            'district'         => $request->district,
            'specific_address' => $request->specific_address,
            'shipping_method'  => $request->shipping_method,
            'shipping_fee'     => $shippingFee,
            'discount_amount'  => $discount,
            'total_amount'     => $total,
            'status'           => 'pending',
            'payment_method'   => null,              // sẽ cập nhật ở bước complete
            'note'             => $request->note,
        ]);

        // Lưu order items
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'quantity'   => $item->quantity,
                'price'      => $item->variant->price,
            ]);
        }

        // Xoá giỏ hàng (theo yêu cầu: 1. A)
        Cart::where('user_id', $userId)->delete();

        return response()->json([
            'success'    => true,
            'order_code' => $order->order_code,
        ]);
    }

    // Lấy thông tin 1 order cho trang order detail
    public function getOrder($code)
    {
        $userId = Auth::id();

        $order = Order::where('order_code', $code)
            ->where('user_id', $userId)
            ->with(['items.variant', 'items.product', 'user'])
            ->firstOrFail();

        $cartItems = $order->items->map(function ($item) {
            return [
                'product_name'  => $item->product->name ?? '',
                'category_name' => $item->product->category ?? '',
                'color'         => $item->variant->color ?? '',
                'size'          => $item->variant->size ?? '',
                'variant_image' => $item->variant->image ?? '',
                'quantity'      => $item->quantity,
                'price'         => $item->price,
            ];
        });

        return response()->json([
            'success' => true,
            'order'   => [
                'order_code'      => $order->order_code,
                'full_name'       => $order->full_name,
                'phone'           => $order->phone,
                'email'           => $order->email,
                'province'        => $order->province,
                'district'        => $order->district,
                'specific_address' => $order->specific_address,
                'shipping_method' => $order->shipping_method,
                'shipping_fee'    => $order->shipping_fee,
                'discount_amount' => $order->discount_amount,
                'total_amount'    => $order->total_amount,
                'note'            => $order->note,
                'status'          => $order->status,
                'cart_items'      => $cartItems,
            ],
            'user' => [
                'full_name' => $order->user->name ?? $order->full_name,
                'email'     => $order->user->email ?? $order->email,
            ],
        ]);
    }

    // Hoàn tất đơn hàng (chọn phương thức thanh toán ở Order Detail)
    public function completeOrder(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'order_code'     => 'required|string',
            'payment_method' => 'required|in:cod,bank_transfer',
            'note'           => 'nullable|string',
        ]);

        $order = Order::where('order_code', $request->order_code)
            ->where('user_id', $userId)
            ->firstOrFail();

        $order->payment_method = $request->payment_method;
        if ($request->note) {
            $order->note = $request->note;
        }
        $order->status = 'confirmed';
        $order->save();

        return response()->json([
            'success' => true,
        ]);
    }
}
