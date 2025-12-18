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
            return response()->json(['cart' => [], 'total_items' => 0, 'subtotal' => 0]);
        }

        $userId = Auth::id();

        $cart = Cart::where('user_id', $userId)
            ->with(['product'])
            ->orderBy('updated_at', 'desc') // Show recently updated items first
            ->get();

        // Calculate totals
        $totalItems = $cart->sum('quantity');
        $subtotal = $cart->sum(function($item) {
            return ($item->price_at_time ?? $item->product->price) * $item->quantity;
        });

        // Format cart items with additional info
        $formattedCart = $cart->map(function($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price_at_time' => $item->price_at_time,
                'variant_info' => $item->variant_info,
                'added_at' => $item->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $item->updated_at?->format('Y-m-d H:i:s'),
                'product' => [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'image' => $item->product->image,
                    'category' => $item->product->category ?? 'Chưa phân loại'
                ]
            ];
        });

        return response()->json([
            'cart' => $formattedCart,
            'total_items' => $totalItems,
            'subtotal' => $subtotal,
            'success' => true
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
        try {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
            }

            $userId = Auth::id();
            
            // Validate input
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'variant_name' => 'nullable|string|max:100'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }

        try {
            // Get product with variants
            $product = Product::with('variants')->find($validated['product_id']);
            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại'], 404);
            }

            // Tạo variant info
            $variantInfo = [
                'variant_name' => $validated['variant_name'] ?? null,
                'added_from' => $request->input('source', 'product_page')
            ];
            
            $productPrice = $product->price;
            
            // Nếu sản phẩm có variants, kiểm tra và lấy giá từ variant
            if ($product->variants->count() > 0) {
                $variant = null;
                
                if ($validated['variant_name']) {
                    $variant = $product->variants()
                        ->where('variant_name', $validated['variant_name'])
                        ->first();
                }
                
                if ($variant) {
                    $productPrice = $variant->price ?? $product->price;
                } else if ($product->hasMultipleVariants() && $validated['variant_name']) {
                    // Nếu sản phẩm có nhiều variants nhưng không tìm thấy variant phù hợp
                    return response()->json([
                        'success' => false, 
                        'message' => 'Biến thể sản phẩm không tồn tại'
                    ], 404);
                }
            }

            // Check if item with same variant already exists in cart
            $item = Cart::where('user_id', $userId)
                ->where('product_id', $validated['product_id'])
                ->where('variant_info', json_encode($variantInfo))
                ->first();

            if ($item) {
                // Update existing item
                $item->quantity += $validated['quantity'];
                $item->save(); // Laravel will automatically update updated_at
            } else {
                // Create new cart item with detailed info
                Cart::create([
                    'user_id' => $userId,
                    'product_id' => $validated['product_id'],
                    'quantity' => $validated['quantity'],
                    'price_at_time' => $productPrice,
                    'variant_info' => $variantInfo, // No need to json_encode, Laravel will handle it
                ]);
            }

            // Get updated cart count
            $cartCount = Cart::where('user_id', $userId)->sum('quantity');

            return response()->json([
                'success' => true, 
                'message' => 'Đã thêm sản phẩm vào giỏ hàng',
                'cart_count' => $cartCount
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    // Tăng giảm số lượng
    public function updateQuantity(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập']);
        }

        $item = Cart::where('id', $request->id)
                   ->where('user_id', Auth::id())
                   ->first();

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ hàng']);
        }

        if ($request->action === 'increase') {
            $item->quantity++;
        } elseif ($request->action === 'decrease') {
            if ($item->quantity > 1) {
                $item->quantity--;
            } else {
                // If quantity becomes 0, remove item
                $item->delete();
                return response()->json(['success' => true, 'removed' => true]);
            }
        }

        $item->save(); // Laravel will automatically update updated_at
        
        return response()->json([
            'success' => true, 
            'new_quantity' => $item->quantity
        ]);
    }

    // Xóa sản phẩm
    public function delete(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập']);
        }

        $deleted = Cart::where('id', $request->id)
                      ->where('user_id', Auth::id())
                      ->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Đã xóa sản phẩm khỏi giỏ hàng']);
        } else {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ hàng']);
        }
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
            "discount" => $discount,
            "discount_percent" => $voucher->type === "percent" ? $voucher->discount_value : 0
        ]);
    }

    // Clean up old cart items (can be called via cron job)
    public function cleanupOldCarts()
    {
        // Remove cart items older than 30 days
        $deleted = Cart::where('updated_at', '<', now()->subDays(30))->delete();
        
        return response()->json([
            'success' => true,
            'deleted_items' => $deleted,
            'message' => "Đã xóa {$deleted} sản phẩm cũ khỏi giỏ hàng"
        ]);
    }

    // Get cart statistics (for admin or analytics)
    public function getCartStats()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = Auth::id();
        
        $stats = [
            'total_items' => Cart::where('user_id', $userId)->sum('quantity'),
            'unique_products' => Cart::where('user_id', $userId)->count(),
            'oldest_item' => Cart::where('user_id', $userId)->min('created_at'),
            'newest_item' => Cart::where('user_id', $userId)->max('updated_at'),
            'total_value' => Cart::where('user_id', $userId)
                                ->with('product')
                                ->get()
                                ->sum(function($item) {
                                    return ($item->price_at_time ?? $item->product->price) * $item->quantity;
                                })
        ];

        return response()->json(['success' => true, 'stats' => $stats]);
    }
}
