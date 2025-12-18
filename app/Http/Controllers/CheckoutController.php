<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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

    // Lưu địa chỉ giao hàng vào session
    public function setAddress(Request $request)
    {
        try {
            // Log request data for debugging
            \Log::info('Checkout setAddress request:', $request->all());

            // Validate without save_address first
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'province_id' => 'required|integer|min:1',
                'ward_id' => 'required|integer|min:1',
                'specific_address' => 'required|string|max:500',
                'selected_address_id' => 'nullable|integer|min:1'
            ]);

            // Handle save_address separately
            $saveAddressInput = $request->input('save_address');
            $validated['save_address'] = in_array($saveAddressInput, [true, 'true', 1, '1'], true);

            \Log::info('Save address processing:', [
                'input' => $saveAddressInput,
                'type' => gettype($saveAddressInput),
                'result' => $validated['save_address']
            ]);

            // Check if province and ward exist (optional check)
            try {
                $province = \App\Models\Province::find($validated['province_id']);
                $ward = \App\Models\Ward::find($validated['ward_id']);
                
                if (!$province) {
                    \Log::warning('Province not found:', ['province_id' => $validated['province_id']]);
                }
                if (!$ward) {
                    \Log::warning('Ward not found:', ['ward_id' => $validated['ward_id']]);
                }
                if ($ward && $ward->province_id != $validated['province_id']) {
                    \Log::warning('Ward does not belong to province:', [
                        'ward_id' => $validated['ward_id'],
                        'ward_province_id' => $ward->province_id,
                        'selected_province_id' => $validated['province_id']
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Error checking province/ward:', ['error' => $e->getMessage()]);
            }

            // Lưu địa chỉ vào session
            Session::put('checkout_address', $validated);

            // Nếu user chọn lưu địa chỉ và chưa có địa chỉ này
            if ($validated['save_address'] && empty($validated['selected_address_id'])) {
                try {
                    Address::create([
                        'user_id' => Auth::id(),
                        'full_name' => $validated['full_name'],
                        'phone' => $validated['phone'],
                        'province_id' => $validated['province_id'],
                        'ward_id' => $validated['ward_id'],
                        'specific_address' => $validated['specific_address'],
                        'is_default' => false
                    ]);
                } catch (\Exception $addressError) {
                    // Log address creation error but don't fail the whole request
                    \Log::error('Failed to save address:', ['error' => $addressError->getMessage()]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu địa chỉ giao hàng'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in setAddress:', $e->errors());
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

    // Tạo đơn hàng
    public function createOrder(Request $request)
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