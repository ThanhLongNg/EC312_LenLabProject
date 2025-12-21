<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    // Hiển thị trang checkout
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục');
        }

        // Kiểm tra giỏ hàng có sản phẩm không
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Giỏ hàng trống');
        }

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
    ->with('product')
    ->get();

$subtotal = $cartItems->sum(function ($item) {
    $price = $item->price_at_time ?? optional($item->product)->price ?? 0;
    return $price * $item->quantity;
});


foreach ($cartItems as $item) {
    $price = $item->price_at_time ?? optional($item->product)->price ?? 0;

    OrderItem::create([
        'order_id'   => $order->order_id,   // hoặc $order->id tùy schema bạn đang dùng
        'product_id' => $item->product_id,
        'quantity'   => $item->quantity,
        'price'      => $price,

        // nếu order_items có cột variant_info thì lưu:
        // 'variant_info' => $item->variant_info,
    ]);
}


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
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in setAddress:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // Hiển thị trang thanh toán
    public function payment()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Kiểm tra có địa chỉ trong session không
        $address = Session::get('checkout_address');
        if (!$address) {
            return redirect()->route('checkout')->with('error', 'Vui lòng chọn địa chỉ giao hàng');
        }

        // Lấy thông tin tỉnh và xã/phường nếu có province_id và ward_id
        if (isset($address['province_id']) && isset($address['ward_id'])) {
            try {
                $province = \App\Models\Province::find($address['province_id']);
                $ward = \App\Models\Ward::find($address['ward_id']);
                
                if ($province) {
                    $address['province_name'] = $province->name;
                }
                if ($ward) {
                    $address['ward_name'] = $ward->name;
                }
            } catch (\Exception $e) {
                \Log::error('Error loading province/ward names:', ['error' => $e->getMessage()]);
            }
        }

        // Lấy thông tin giỏ hàng
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Giỏ hàng trống');
        }

        $subtotal = $cartItems->sum(function($item) {
            return ($item->price_at_time ?? $item->product->price) * $item->quantity;
        });

        return view('checkout-payment', compact('address', 'cartItems', 'subtotal'));
    }

    // Hoàn tất đơn hàng (chọn phương thức thanh toán ở Order Detail)
    public function completeOrder(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
            }

            $address = Session::get('checkout_address');
            if (!$address) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy địa chỉ giao hàng'], 400);
            }

            $validated = $request->validate([
                'payment_method' => 'required|string|in:cod,bank_transfer,momo',
                'note' => 'nullable|string|max:500'
            ]);

            // Lấy giỏ hàng
            $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
            if ($cartItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Giỏ hàng trống'], 400);
            }

            // Tính tổng tiền
            $subtotal = $cartItems->sum(function($item) {
                return ($item->price_at_time ?? $item->product->price) * $item->quantity;
            });
            
            $shippingFee = 30000; // Phí ship cố định
            $total = $subtotal + $shippingFee;

            // Tạo mã đơn hàng
            $orderCode = 'LL' . date('Ymd') . rand(1000, 9999);

            // Tạo đơn hàng (giả sử có model Order)
            // Order::create([...]);

            // Xóa giỏ hàng
            Cart::where('user_id', Auth::id())->delete();

            // Xóa địa chỉ khỏi session
            Session::forget('checkout_address');

            return response()->json([
                'success' => true,
                'order_code' => $orderCode,
                'message' => 'Đặt hàng thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}