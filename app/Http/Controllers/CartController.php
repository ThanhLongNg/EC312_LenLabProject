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

    // Thêm sản phẩm vào giỏ (Updated to handle both with and without variants)
    public function add(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }

        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'variant_name' => 'nullable|string',
            'quantity'   => 'required|integer|min:1',
        ]);

        $userId = Auth::id();
        $productId = $request->product_id;
        $variantId = $request->variant_id;
        $variantName = $request->variant_name;
        $quantity = (int)$request->quantity;

        // Nếu có variant_id, lấy thông tin variant
        $variant = null;
        $price = null;
        $variantInfo = null;

        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            
            // Đảm bảo variant thuộc product_id
            if ((int)$variant->product_id !== (int)$productId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variant không thuộc sản phẩm này'
                ], 422);
            }
            
            $price = $variant->price;
            $variantInfo = [
                'color' => $variant->color ?? null,
                'size'  => $variant->size ?? null,
                'image' => $variant->image ?? null,
            ];
        } else {
            // Không có variant, lấy giá từ sản phẩm chính
            $product = \App\Models\Product::findOrFail($productId);
            $price = $product->price;
            
            if ($variantName) {
                $variantInfo = ['variant_name' => $variantName];
            }
        }

        // Tìm item trong giỏ hàng
        $cartQuery = Cart::where('user_id', $userId)
            ->where('product_id', $productId);
            
        if ($variantId) {
            $cartQuery->where('variant_id', $variantId);
        } else {
            $cartQuery->whereNull('variant_id');
            if ($variantName) {
                $cartQuery->where('variant_info->variant_name', $variantName);
            }
        }
        
        $item = $cartQuery->first();

        if ($item) {
            // Cập nhật số lượng
            $item->quantity += $quantity;
            $item->price_at_time = $price;
            if ($variantInfo) {
                $item->variant_info = $variantInfo;
            }
            $item->save();
        } else {
            // Tạo mới
            Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'price_at_time' => $price,
                'variant_info' => $variantInfo,
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

        // Kiểm tra ngày bắt đầu (nếu có)
        if ($voucher->start_date && now()->lt($voucher->start_date)) {
            return response()->json(["success" => false, "message" => "Voucher chưa bắt đầu"], 422);
        }

        // Kiểm tra ngày kết thúc (nếu có)
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

        // Kiểm tra giá trị đơn hàng tối thiểu
        if ($voucher->min_order_value && $subtotal < $voucher->min_order_value) {
            $minValue = number_format($voucher->min_order_value);
            return response()->json([
                "success" => false, 
                "message" => "Đơn hàng tối thiểu {$minValue}đ để sử dụng mã này"
            ], 422);
        }

        // Tính toán giảm giá
        if ($voucher->type === "fixed") {
            $discount = (int) $voucher->discount_value;
            $discountPercent = 0;
        } else {
            $discount = (int) ($subtotal * ($voucher->discount_value / 100));
            $discountPercent = (int) $voucher->discount_value;
        }

        // Lưu voucher vào session
        session([
            'voucher_code' => $voucher->code,
            'voucher_discount' => $discount,
            'voucher_type' => $voucher->type,
            'voucher_value' => $voucher->discount_value
        ]);

        return response()->json([
            "success" => true,
            "message" => "Áp dụng mã giảm giá thành công",
            "discount" => $discount,
            "discount_percent" => $discountPercent,
            "voucher_code" => $voucher->code
        ]);
    }

    // Hiển thị trang giỏ hàng
    public function show()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)
            ->with(['product', 'variant'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $subtotal = $cart->sum(function ($item) {
            $price = $item->price_at_time
                ?? optional($item->variant)->price
                ?? optional($item->product)->price
                ?? 0;
            return $price * $item->quantity;
        });

        // Lấy thông tin voucher từ session
        $voucherCode = session('voucher_code');
        $voucherDiscount = session('voucher_discount', 0);

        return view('cart', compact('cart', 'subtotal', 'voucherCode', 'voucherDiscount'));
    }
}
