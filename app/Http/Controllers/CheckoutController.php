<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Helpers\ShippingHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    /**
     * Calculate shipping fee with consistent logic
     */
    private function calculateShippingFeeFromAddress($address)
    {
        $provinceName = '';
        
        // Try to get province name from address
        if (isset($address['province_name']) && !empty($address['province_name'])) {
            $provinceName = $address['province_name'];
        } elseif (isset($address['province_id'])) {
            try {
                $province = \App\Models\Province::find($address['province_id']);
                if ($province) {
                    $provinceName = $province->name;
                }
            } catch (\Exception $e) {
                \Log::error('Error loading province for shipping calculation:', ['error' => $e->getMessage()]);
            }
        }
        
        return ShippingHelper::calculateShippingFee($provinceName);
    }
    // Lưu các sản phẩm được chọn vào session
    public function setSelectedItems(Request $request)
    {
        try {
            $validated = $request->validate([
                'selected_items' => 'required|array|min:1',
                'selected_items.*' => 'integer|min:1'
            ]);

            // Verify that all selected items belong to the current user
            $userCartItems = Cart::where('user_id', Auth::id())
                                ->whereIn('id', $validated['selected_items'])
                                ->pluck('id')
                                ->toArray();

            if (count($userCartItems) !== count($validated['selected_items'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Một số sản phẩm không thuộc về giỏ hàng của bạn'
                ], 400);
            }

            // Store selected items in session
            Session::put('checkout_selected_items', $validated['selected_items']);

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu sản phẩm được chọn'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // Hiển thị trang checkout
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục');
        }

        // Kiểm tra có sản phẩm được chọn không
        $selectedItemIds = Session::get('checkout_selected_items');
        if (!$selectedItemIds || empty($selectedItemIds)) {
            return redirect()->route('cart')->with('error', 'Vui lòng chọn sản phẩm để thanh toán');
        }

        // Kiểm tra giỏ hàng có sản phẩm được chọn không
        $cartItems = Cart::where('user_id', Auth::id())
                        ->whereIn('id', $selectedItemIds)
                        ->with('product')
                        ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Không tìm thấy sản phẩm được chọn');
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

        // Kiểm tra có sản phẩm được chọn không
        $selectedItemIds = Session::get('checkout_selected_items');
        if (!$selectedItemIds || empty($selectedItemIds)) {
            return redirect()->route('cart')->with('error', 'Vui lòng chọn sản phẩm để thanh toán');
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

        // Lấy thông tin giỏ hàng (chỉ các sản phẩm được chọn)
        $cartItems = Cart::where('user_id', Auth::id())
                        ->whereIn('id', $selectedItemIds)
                        ->with('product')
                        ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Không tìm thấy sản phẩm được chọn');
        }

        $subtotal = $cartItems->sum(function($item) {
            return ($item->price_at_time ?? $item->product->price) * $item->quantity;
        });

        // Tính phí ship dựa trên tỉnh
        $shippingFee = $this->calculateShippingFeeFromAddress($address);

        return view('checkout-payment', compact('address', 'cartItems', 'subtotal', 'shippingFee'));
    }

    // Lưu phương thức thanh toán và chuyển đến trang xác nhận
    public function setPaymentMethod(Request $request)
    {
        try {
            $validated = $request->validate([
                'payment_method' => 'required|string|in:cod,bank_transfer,momo'
            ]);

            // Lưu phương thức thanh toán vào session
            Session::put('checkout_payment_method', $validated['payment_method']);

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu phương thức thanh toán'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // Hiển thị trang xác nhận đơn hàng
    public function confirm()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Kiểm tra có địa chỉ trong session không
        $address = Session::get('checkout_address');
        
        if (!$address) {
            return redirect()->route('checkout')->with('error', 'Vui lòng chọn địa chỉ giao hàng');
        }

        // Kiểm tra có sản phẩm được chọn không
        $selectedItemIds = Session::get('checkout_selected_items');
        if (!$selectedItemIds || empty($selectedItemIds)) {
            return redirect()->route('cart')->with('error', 'Vui lòng chọn sản phẩm để thanh toán');
        }

        // Lấy thông tin tỉnh và xã/phường
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

        // Lấy thông tin giỏ hàng (chỉ các sản phẩm được chọn)
        $cartItems = Cart::where('user_id', Auth::id())
                        ->whereIn('id', $selectedItemIds)
                        ->with('product')
                        ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Không tìm thấy sản phẩm được chọn');
        }

        $subtotal = $cartItems->sum(function($item) {
            return ($item->price_at_time ?? $item->product->price) * $item->quantity;
        });

        // Tính phí ship dựa trên tỉnh
        $shippingFee = $this->calculateShippingFeeFromAddress($address);

        return view('checkout-confirm', compact('address', 'cartItems', 'subtotal', 'shippingFee'));
    }

    // Lưu ghi chú đơn hàng
    public function setNote(Request $request)
    {
        try {
            $validated = $request->validate([
                'note' => 'nullable|string|max:500'
            ]);

            // Lưu ghi chú vào session
            Session::put('checkout_note', $validated['note'] ?? '');

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu ghi chú đơn hàng'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // Tạo đơn hàng
    public function createOrder(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
            }

            $address = Session::get('checkout_address');
            $note = Session::get('checkout_note');
            $selectedItemIds = Session::get('checkout_selected_items');
            
            if (!$address) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy địa chỉ giao hàng'], 400);
            }

            if (!$selectedItemIds || empty($selectedItemIds)) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm được chọn'], 400);
            }

            $validated = $request->validate([
                'payment_method' => 'required|string|in:cod,bank_transfer,momo'
            ]);

            // Lấy giỏ hàng (chỉ các sản phẩm được chọn)
            $cartItems = Cart::where('user_id', Auth::id())
                            ->whereIn('id', $selectedItemIds)
                            ->with('product')
                            ->get();
            
            if ($cartItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm được chọn'], 400);
            }

            // Tính tổng tiền
            $subtotal = $cartItems->sum(function($item) {
                return ($item->price_at_time ?? $item->product->price) * $item->quantity;
            });
            
            // Tính phí ship dựa trên tỉnh
            $shippingFee = $this->calculateShippingFeeFromAddress($address);
            
            // Lấy discount từ session nếu có
            $appliedVoucher = Session::get('applied_voucher');
            $discountAmount = 0;
            
            if ($appliedVoucher) {
                if ($appliedVoucher['type'] === 'percent') {
                    $discountAmount = ($subtotal * $appliedVoucher['discount_value']) / 100;
                } else {
                    $discountAmount = $appliedVoucher['discount_value'];
                }
            }
            
            $total = $subtotal + $shippingFee - $discountAmount;

            // Lấy thông tin user
            $user = Auth::user();
            
            // Lấy thông tin từ shipping_address
            $provinceName = '';
            $wardName = '';
            if (isset($address['province_id'])) {
                try {
                    $province = \App\Models\Province::find($address['province_id']);
                    if ($province) {
                        $provinceName = $province->name;
                    }
                } catch (\Exception $e) {
                    \Log::error('Error loading province name:', ['error' => $e->getMessage()]);
                }
            }
            
            if (isset($address['ward_id'])) {
                try {
                    $ward = \App\Models\Ward::find($address['ward_id']);
                    if ($ward) {
                        $wardName = $ward->name;
                    }
                } catch (\Exception $e) {
                    \Log::error('Error loading ward name:', ['error' => $e->getMessage()]);
                }
            }

            // Tạo đơn hàng trong database sử dụng các cột riêng biệt (không dùng shipping_address)
            $order = \App\Models\Order::create([
                'user_id' => Auth::id(),
                'status' => 'processing',
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_method'] === 'cod' ? 'pending' : 'pending',
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount_amount' => $discountAmount,
                'total_amount' => $total,
                'order_note' => $note ?? '',
                // Address columns (thay thế shipping_address JSON)
                'email' => $user->email,
                'full_name' => $address['full_name'] ?? '',
                'phone' => $address['phone'] ?? '',
                'province' => $provinceName,
                'ward' => $wardName,
                'specific_address' => $address['specific_address'] ?? ''
            ]);

            // Tạo order items
            foreach ($cartItems as $cartItem) {
                // Lấy variant_id từ variant_info nếu có
                $variantId = null;
                if ($cartItem->variant_info) {
                    $variantInfo = is_string($cartItem->variant_info) ? json_decode($cartItem->variant_info, true) : $cartItem->variant_info;
                    if (isset($variantInfo['variant_id'])) {
                        $variantId = $variantInfo['variant_id'];
                    }
                }
                
                \App\Models\OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_image' => $cartItem->product->image,
                    'variant_id' => $variantId, // Tận dụng cột có sẵn
                    'variant_info' => $cartItem->variant_info,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price_at_time ?? $cartItem->product->price,
                    'total' => ($cartItem->price_at_time ?? $cartItem->product->price) * $cartItem->quantity
                ]);
            }

            // Xóa chỉ các sản phẩm được chọn khỏi giỏ hàng
            Cart::where('user_id', Auth::id())
                ->whereIn('id', $selectedItemIds)
                ->delete();

            // Xóa session data
            Session::forget(['checkout_address', 'checkout_payment_method', 'checkout_note', 'checkout_selected_items', 'applied_voucher']);

            return response()->json([
                'success' => true,
                'order_code' => $order->order_id, // Sử dụng order_id làm mã đơn hàng
                'redirect_url' => '/order-success?order_code=' . $order->order_id . '&total=' . $total . '&payment_method=' . $validated['payment_method'] . '&province_name=' . urlencode($provinceName),
                'message' => 'Đặt hàng thành công'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error creating order:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // Chuẩn bị chuyển khoản ngân hàng
    public function prepareBankTransfer(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
            }

            $address = Session::get('checkout_address');
            $selectedItemIds = Session::get('checkout_selected_items');
            
            if (!$address || !$selectedItemIds) {
                return response()->json(['success' => false, 'message' => 'Thông tin đơn hàng không hợp lệ'], 400);
            }

            $validated = $request->validate([
                'payment_method' => 'required|string|in:bank_transfer'
            ]);

            // Lấy giỏ hàng
            $cartItems = Cart::where('user_id', Auth::id())
                            ->whereIn('id', $selectedItemIds)
                            ->with('product')
                            ->get();
            
            if ($cartItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm được chọn'], 400);
            }

            // Tính tổng tiền
            $subtotal = $cartItems->sum(function($item) {
                return ($item->price_at_time ?? $item->product->price) * $item->quantity;
            });
            
            // Tính phí ship dựa trên tỉnh
            $shippingFee = $this->calculateShippingFeeFromAddress($address);
            
            // Lấy discount từ session nếu có
            $appliedVoucher = Session::get('applied_voucher');
            $discountAmount = 0;
            
            if ($appliedVoucher) {
                if ($appliedVoucher['type'] === 'percent') {
                    $discountAmount = ($subtotal * $appliedVoucher['discount_value']) / 100;
                } else {
                    $discountAmount = $appliedVoucher['discount_value'];
                }
            }
            
            $total = $subtotal + $shippingFee - $discountAmount;

            // Tạo order_id unique cho bank transfer
            do {
                $orderId = 'LL' . date('Ymd') . rand(1000, 9999);
            } while (\App\Models\Order::where('order_id', $orderId)->exists());

            // Lưu thông tin đơn hàng vào session để xử lý sau
            Session::put('pending_order', [
                'order_id' => $orderId, // Sử dụng order_id thay vì order_code
                'total' => $total,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount_amount' => $discountAmount,
                'payment_method' => 'bank_transfer',
                'cart_items' => $cartItems->toArray(),
                'address' => $address,
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'order_code' => $orderId, // Trả về order_id làm order_code
                'total' => $total,
                'message' => 'Đã chuẩn bị thông tin chuyển khoản'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error preparing bank transfer:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // Hiển thị trang chuyển khoản ngân hàng
    public function bankTransfer(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $orderCode = $request->get('order_code');
        $urlTotal = $request->get('total');

        if (!$orderCode || !$urlTotal) {
            return redirect()->route('cart')->with('error', 'Thông tin đơn hàng không hợp lệ');
        }

        // Kiểm tra pending order trong session
        $pendingOrder = Session::get('pending_order');
        if (!$pendingOrder || $pendingOrder['order_id'] !== $orderCode) {
            return redirect()->route('cart')->with('error', 'Đơn hàng không tồn tại hoặc đã hết hạn');
        }

        // Use total from pending order to ensure consistency
        $total = $pendingOrder['total'];
        $subtotal = $pendingOrder['subtotal'];
        $shippingFee = $pendingOrder['shipping_fee'];
        $discountAmount = $pendingOrder['discount_amount'] ?? 0;

        return view('checkout-bank-transfer', compact('orderCode', 'total', 'subtotal', 'shippingFee', 'discountAmount'));
    }

    // Hoàn tất chuyển khoản ngân hàng
    public function completeBankTransfer(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
            }

            $validated = $request->validate([
                'transfer_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
                'payment_method' => 'required|string|in:bank_transfer',
                'order_code' => 'required|string'
            ]);

            // Kiểm tra pending order
            $pendingOrder = Session::get('pending_order');
            if (!$pendingOrder || $pendingOrder['order_id'] !== $validated['order_code']) {
                return response()->json(['success' => false, 'message' => 'Đơn hàng không tồn tại hoặc đã hết hạn'], 400);
            }

            // Lưu ảnh chuyển khoản
            $transferImage = $request->file('transfer_image');
            $imageName = 'transfer_' . $validated['order_code'] . '_' . time() . '.' . $transferImage->getClientOriginalExtension();
            $transferImage->move(public_path('transfer-images'), $imageName);

            // Lấy thông tin user
            $user = Auth::user();
            $address = $pendingOrder['address'];
            
            // Lấy thông tin tỉnh và phường/xã từ province_id và ward_id
            $provinceName = '';
            $wardName = '';
            if (isset($address['province_id'])) {
                try {
                    $province = \App\Models\Province::find($address['province_id']);
                    if ($province) {
                        $provinceName = $province->name;
                    }
                } catch (\Exception $e) {
                    \Log::error('Error loading province name in bank transfer:', ['error' => $e->getMessage()]);
                }
            }
            
            if (isset($address['ward_id'])) {
                try {
                    $ward = \App\Models\Ward::find($address['ward_id']);
                    if ($ward) {
                        $wardName = $ward->name;
                    }
                } catch (\Exception $e) {
                    \Log::error('Error loading ward name in bank transfer:', ['error' => $e->getMessage()]);
                }
            }

            // Tạo đơn hàng trong database sử dụng các cột riêng biệt
            $order = \App\Models\Order::create([
                'order_id' => $pendingOrder['order_id'], // Sử dụng order_id từ pending_order
                'user_id' => Auth::id(),
                'status' => 'processing',
                'payment_method' => 'bank_transfer',
                'payment_status' => 'pending',
                'subtotal' => $pendingOrder['subtotal'],
                'shipping_fee' => $pendingOrder['shipping_fee'],
                'discount_amount' => $pendingOrder['discount_amount'] ?? 0,
                'total_amount' => $pendingOrder['total'],
                'order_note' => Session::get('checkout_note', ''),
                'transfer_image' => $imageName,
                // Address columns (thay thế shipping_address JSON)
                'email' => $user->email,
                'full_name' => $address['full_name'] ?? '',
                'phone' => $address['phone'] ?? '',
                'province' => $provinceName,
                'ward' => $wardName,
                'specific_address' => $address['specific_address'] ?? ''
            ]);

            // Tạo order items từ cart items đã lưu
            foreach ($pendingOrder['cart_items'] as $cartItem) {
                // Lấy variant_id từ variant_info nếu có
                $variantId = null;
                if (isset($cartItem['variant_info'])) {
                    $variantInfo = is_string($cartItem['variant_info']) ? json_decode($cartItem['variant_info'], true) : $cartItem['variant_info'];
                    if (isset($variantInfo['variant_id'])) {
                        $variantId = $variantInfo['variant_id'];
                    }
                }
                
                \App\Models\OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $cartItem['product_id'],
                    'product_name' => $cartItem['product']['name'] ?? 'Sản phẩm',
                    'product_image' => $cartItem['product']['image'] ?? '',
                    'variant_id' => $variantId, // Tận dụng cột có sẵn
                    'variant_info' => $cartItem['variant_info'],
                    'quantity' => $cartItem['quantity'],
                    'price' => $cartItem['price_at_time'] ?? $cartItem['product']['price'],
                    'total' => ($cartItem['price_at_time'] ?? $cartItem['product']['price']) * $cartItem['quantity']
                ]);
            }

            // Xóa các sản phẩm được chọn khỏi giỏ hàng
            $selectedItemIds = Session::get('checkout_selected_items');
            if ($selectedItemIds) {
                Cart::where('user_id', Auth::id())
                    ->whereIn('id', $selectedItemIds)
                    ->delete();
            }

            // Xóa session data
            Session::forget(['checkout_address', 'checkout_payment_method', 'checkout_note', 'checkout_selected_items', 'pending_order']);

            return response()->json([
                'success' => true,
                'order_code' => $validated['order_code'],
                'redirect_url' => '/order-success?order_code=' . $validated['order_code'] . '&total=' . $pendingOrder['total'] . '&payment_method=bank_transfer&province_name=' . urlencode($pendingOrder['address']['province_name'] ?? ''),
                'message' => 'Đặt hàng thành công! Chúng tôi sẽ xác nhận thanh toán trong thời gian sớm nhất.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error completing bank transfer:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // Hiển thị trang thành công
    public function orderSuccess(Request $request)
    {
        $orderCode = $request->get('order_code');
        $total = $request->get('total');
        $paymentMethod = $request->get('payment_method', 'cod');
        $provinceName = $request->get('province_name', '');

        if (!$orderCode || !$total) {
            return redirect()->route('cart')->with('error', 'Thông tin đơn hàng không hợp lệ');
        }

        // Calculate estimated delivery based on zone
        $estimatedDelivery = ShippingHelper::calculateDeliveryTime($provinceName);

        return view('order-success', compact('orderCode', 'total', 'paymentMethod', 'estimatedDelivery'));
    }
}