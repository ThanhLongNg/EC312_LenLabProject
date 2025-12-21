<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\ProductVariant;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // Lấy giỏ hàng (API)
    public function index()
    {
        if (!Auth::check()) {
            return response()->json([
                'cart' => [],
                'total_items' => 0,
                'subtotal' => 0,
                'success' => true,
            ]);
        }

        $userId = Auth::id();

        $cart = Cart::where('user_id', $userId)
            ->with(['product', 'variant'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $totalItems = $cart->sum('quantity');

        $subtotal = $cart->sum(function ($item) {
            $price = $item->price_at_time
                ?? optional($item->variant)->price
                ?? optional($item->product)->price
                ?? 0;

            return $price * $item->quantity;
        });

        $formattedCart = $cart->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'quantity' => $item->quantity,
                'price_at_time' => $item->price_at_time,
                'variant_info' => $item->variant_info,
                'product' => $item->product ? [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'image' => $item->product->image,
                    'category' => $item->product->category ?? 'Chưa phân loại',
                ] : null,
                'variant' => $item->variant ? [
                    'id' => $item->variant->id,
                    'color' => $item->variant->color ?? null,
                    'size' => $item->variant->size ?? null,
                    'price' => $item->variant->price ?? null,
                    'image' => $item->variant->image ?? null,
                ] : null,
            ];
        });

        return response()->json([
            'cart' => $formattedCart,
            'total_items' => $totalItems,
            'subtotal' => $subtotal,
            'success' => true,
        ]);
    }

    // Thêm sản phẩm vào giỏ (Hướng B)
    public function add(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }

        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'variant_id' => 'required|integer|exists:product_variants,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $userId = Auth::id();
        $variant = ProductVariant::findOrFail($request->variant_id);

        // Optional: đảm bảo variant thuộc product_id
        if ((int)$variant->product_id !== (int)$request->product_id) {
            return response()->json([
                'success' => false,
                'message' => 'Variant không thuộc sản phẩm này'
            ], 422);
        }

        // Update/Insert theo (user_id, product_id, variant_id)
        $item = Cart::where('user_id', $userId)
            ->where('product_id', $request->product_id)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($item) {
            $item->quantity += (int)$request->quantity;
            $item->price_at_time = $variant->price; // cập nhật lại giá tại thời điểm thêm (tuỳ bạn)
            $item->variant_info = [
                'color' => $variant->color ?? null,
                'size'  => $variant->size ?? null,
                'image' => $variant->image ?? null,
            ];
            $item->save();
        } else {
            Cart::create([
                'user_id' => $userId,
                'product_id' => $request->product_id,
                'variant_id' => $request->variant_id,
                'quantity' => (int)$request->quantity,
                'price_at_time' => $variant->price,
                'variant_info' => [
                    'color' => $variant->color ?? null,
                    'size'  => $variant->size ?? null,
                    'image' => $variant->image ?? null,
                ],
            ]);
        }

        $cartCount = Cart::where('user_id', $userId)->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm sản phẩm vào giỏ hàng',
            'cart_count' => $cartCount
        ]);
    }

    // Tăng giảm số lượng
    public function updateQuantity(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }

        $request->validate([
            'id' => 'required|integer|exists:cart,id',
            'action' => 'required|in:increase,decrease',
        ]);

        $item = Cart::where('id', $request->id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'], 404);
        }

        if ($request->action === 'increase') {
            $item->quantity++;
            $item->save();
            return response()->json(['success' => true, 'new_quantity' => $item->quantity]);
        }

        // decrease
        if ($item->quantity > 1) {
            $item->quantity--;
            $item->save();
            return response()->json(['success' => true, 'new_quantity' => $item->quantity]);
        }

        $item->delete();
        return response()->json(['success' => true, 'removed' => true]);
    }

    // Xóa sản phẩm
    public function delete(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }

        $request->validate([
            'id' => 'required|integer|exists:cart,id',
        ]);

        $deleted = Cart::where('id', $request->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json([
            'success' => (bool)$deleted,
            'message' => $deleted ? 'Đã xóa sản phẩm khỏi giỏ hàng' : 'Không tìm thấy sản phẩm trong giỏ hàng'
        ]);
    }

    // Áp dụng voucher
    public function applyVoucher(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(["success" => false, "message" => "Vui lòng đăng nhập"], 401);
        }

        $request->validate([
            'code' => 'required|string'
        ]);

        $voucher = Voucher::where('code', $request->code)
            ->where('active', 1)
            ->first();

        if (!$voucher) {
            return response()->json(["success" => false, "message" => "Mã không hợp lệ"], 422);
        }

        if ($voucher->start_date && now()->lt($voucher->start_date)) {
            return response()->json(["success" => false, "message" => "Voucher chưa bắt đầu"], 422);
        }

        if ($voucher->end_date && now()->gt($voucher->end_date)) {
            return response()->json(["success" => false, "message" => "Voucher đã hết hạn"], 422);
        }

        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)->with(['product', 'variant'])->get();

        $subtotal = $cart->sum(function ($item) {
            $price = $item->price_at_time
                ?? optional($item->variant)->price
                ?? optional($item->product)->price
                ?? 0;
            return $price * $item->quantity;
        });

        if ($voucher->min_order_value && $subtotal < $voucher->min_order_value) {
            return response()->json(["success" => false, "message" => "Chưa đủ giá trị đơn tối thiểu"], 422);
        }

        if ($voucher->type === "fixed") {
            $discount = (int) $voucher->discount_value;
            $discountPercent = 0;
        } else {
            $discount = (int) ($subtotal * ($voucher->discount_value / 100));
            $discountPercent = (int) $voucher->discount_value;
        }

        return response()->json([
            "success" => true,
            "discount" => $discount,
            "discount_percent" => $discountPercent,
        ]);
    }
}
