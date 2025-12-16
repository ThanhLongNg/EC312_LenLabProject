<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Lấy giỏ hàng (API)
    public function index()
    {
        if (!Auth::check()) {
            return response()->json(['cart' => []]);
        }

        $userId = Auth::id();

        $cart = Cart::where('user_id', $userId)
            ->with(['product'])
            ->get();

        return response()->json([
            'cart' => $cart
        ]);
    }

    // Trang giỏ hàng (View)
    public function show()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem giỏ hàng');
        }

        $userId = Auth::id();
        $cartItems = Cart::where('user_id', $userId)
            ->with(['product'])
            ->get();

        return view('cart', compact('cartItems'));
    }

    // Thêm sản phẩm vào giỏ
    public function add(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập']);
        }

        $userId = Auth::id();

        $item = Cart::where('user_id', $userId)
            ->where('product_id', $request->product_id)
            ->first();

        if ($item) {
            $item->quantity += $request->quantity;
            $item->save();
        } else {
            Cart::create([
                'user_id' => $userId,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);
        }

        return response()->json(['success' => true]);
    }

    // Tăng giảm số lượng
    public function updateQuantity(Request $request)
    {
        $item = Cart::find($request->id);

        if (!$item) return;

        if ($request->action === 'increase') {
            $item->quantity++;
        } elseif ($request->action === 'decrease') {
            if ($item->quantity > 1) $item->quantity--;
        }

        $item->save();
        return response()->json(['success' => true]);
    }

    // Xóa sản phẩm
    public function delete(Request $request)
    {
        Cart::where('id', $request->id)->delete();
        return response()->json(['success' => true]);
    }

    // Áp dụng voucher
    public function applyVoucher(Request $request)
    {
        $code = $request->code;
        $voucher = Voucher::where('code', $code)
            ->where('active', 1)
            ->first();

        if (!$voucher) {
            return response()->json(["success" => false, "message" => "Mã không hợp lệ"]);
        }

        // Kiểm tra ngày bắt đầu
        if ($voucher->start_date && now()->lt($voucher->start_date)) {
            return response()->json(["success" => false, "message" => "Voucher chưa bắt đầu"]);
        }

        // Kiểm tra ngày kết thúc
        if ($voucher->end_date && now()->gt($voucher->end_date)) {
            return response()->json(["success" => false, "message" => "Voucher đã hết hạn"]);
        }

        // Tính tổng giỏ hàng
        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)->with('product')->get();
        $subtotal = $cart->sum(fn($item) => $item->product->price * $item->quantity);

        // Kiểm tra giá tối thiểu
        if ($subtotal < $voucher->min_order_value) {
            return response()->json(["success" => false, "message" => "Chưa đủ giá trị đơn tối thiểu"]);
        }

        // Tính giảm giá
        if ($voucher->type === "fixed") {
            $discount = $voucher->discount_value;
        } else {
            $discount = intval($subtotal * ($voucher->discount_value / 100));
        }

        return response()->json([
            "success" => true,
            "discount" => $discount
        ]);
    }
}
