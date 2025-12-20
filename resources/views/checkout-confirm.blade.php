<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tổng quan đơn hàng - LENLAB</title>
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&family=Noto+Sans:wght@400;500;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#FAC638",
                        "background-dark": "#0f0f0f",
                        "surface-dark": "#1a1a1a",
                        "card-dark": "#2a2a2a"
                    },
                    fontFamily: {
                        "display": ["Spline Sans", "sans-serif"],
                        "body": ["Noto Sans", "sans-serif"]
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Spline Sans', sans-serif;
            background: #0f0f0f;
            min-height: 100vh;
            padding-bottom: 100px;
        }
        
        .confirm-container {
            background: #0f0f0f;
            max-width: 400px;
            margin: 0 auto;
            min-height: 100vh;
        }
        
        .progress-bar {
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: #FAC638;
            border-radius: 2px;
            transition: width 0.3s ease;
            width: 66%; /* 66% for step 2 of 3 */
        }
        
        .section-card {
            background: #1a1a1a;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }
        
        .product-item {
            background: rgba(45, 45, 45, 0.3);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
        }
        
        .product-item:last-child {
            margin-bottom: 0;
        }
        
        .confirm-btn {
            background: #FAC638;
            transition: all 0.3s ease;
            border-radius: 25px;
        }
        
        .confirm-btn:hover {
            background: #e6b332;
            transform: translateY(-1px);
        }
        
        .confirm-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        
        .total-amount {
            color: #FAC638;
            font-size: 24px;
            font-weight: bold;
        }
        
        .discount-text {
            color: #ef4444;
        }
    </style>
</head>

<body class="bg-background-dark">
    <div class="confirm-container">
        <!-- Header -->
        <div class="flex items-center justify-between p-4">
            <button onclick="window.location.href='/checkout'" class="text-white hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </button>
            <h1 class="text-white font-semibold text-lg">Tổng quan đơn hàng</h1>
            <div class="w-8"></div>
        </div>

        <!-- Progress Bar -->
        <div class="px-4 mb-6">
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
        </div>

        <!-- Content -->
        <div class="px-4">
            <!-- Địa chỉ nhận hàng -->
            <div class="section-card">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-white font-semibold">Địa chỉ nhận hàng</h3>
                    <button onclick="editAddress()" class="text-primary text-sm">Sửa</button>
                </div>
                
                <div class="text-gray-300">
                    <p class="font-medium mb-1">{{ $address['full_name'] }}</p>
                    <p class="text-sm mb-1">{{ $address['phone'] }}</p>
                    <p class="text-sm">
                        {{ $address['specific_address'] }}
                        @if(isset($address['ward_name']))
                            , {{ $address['ward_name'] }}
                        @endif
                        @if(isset($address['province_name']))
                            , {{ $address['province_name'] }}
                        @endif
                    </p>
                </div>
            </div>

            <!-- Sản phẩm -->
            <div class="section-card">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-white font-semibold">Sản phẩm</h3>
                </div>
                
                <div class="space-y-3">
                    @foreach($cartItems as $item)
                        <div class="product-item">
                            <div class="flex items-center gap-3">
                                <img src="/PRODUCT-IMG/{{ $item->product->image ?? 'default.jpg' }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="w-16 h-16 object-cover rounded-lg"
                                     onerror="this.src='https://via.placeholder.com/64x64/FAC638/FFFFFF?text=SP'">
                                
                                <div class="flex-1">
                                    <h4 class="text-white font-medium text-sm mb-1">{{ $item->product->name }}</h4>
                                    @if($item->variant_info)
                                        @php
                                            $variantInfo = is_string($item->variant_info) ? json_decode($item->variant_info, true) : $item->variant_info;
                                        @endphp
                                        @if(isset($variantInfo['variant_name']) && $variantInfo['variant_name'])
                                            <p class="text-gray-400 text-xs mb-1">{{ $variantInfo['variant_name'] }}</p>
                                        @endif
                                    @endif
                                    <p class="text-primary font-semibold text-sm">{{ number_format(($item->price_at_time ?? $item->product->price)) }}đ</p>
                                </div>
                                
                                <div class="text-right">
                                    <span class="text-white text-sm">x{{ $item->quantity }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Ghi chú đơn hàng -->
            <div class="section-card">
                <h3 class="text-white font-semibold mb-4">Ghi chú đơn hàng</h3>
                <textarea id="orderNote" 
                          class="w-full bg-gray-700 border border-gray-600 rounded-lg p-3 text-white placeholder-gray-400 resize-none focus:outline-none focus:border-primary" 
                          rows="3" 
                          placeholder="Nếu bạn muốn gói quà để tặng hoặc không hiển thị giá trên đơn hàng, hãy để lại ghi chú với chúng tôi"></textarea>
            </div>

            <!-- Chi tiết thanh toán -->
            <div class="section-card">
                <h3 class="text-white font-semibold mb-4">Chi tiết thanh toán</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-300">Tạm tính</span>
                        <span class="text-white">{{ number_format($subtotal) }}đ</span>
                    </div>
                    
                    @php
                        // Get voucher discount from session
                        $appliedVoucher = Session::get('applied_voucher');
                        $discountAmount = 0;
                        
                        if ($appliedVoucher) {
                            if ($appliedVoucher['type'] === 'percent') {
                                $discountAmount = ($subtotal * $appliedVoucher['discount_value']) / 100;
                            } else {
                                $discountAmount = $appliedVoucher['discount_value'];
                            }
                        }
                        
                        $actualShippingFee = $shippingFee ?? 30000;
                        $finalTotal = $subtotal + $actualShippingFee - $discountAmount;
                    @endphp
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-300">Phí vận chuyển</span>
                        <span class="text-white">{{ number_format($actualShippingFee) }}đ</span>
                    </div>
                    
                    @if($discountAmount > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Giảm giá</span>
                            <span class="discount-text">-{{ number_format($discountAmount) }}đ</span>
                        </div>
                    @endif
                    
                    <hr class="border-gray-600">
                    
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-white font-bold text-lg">Tổng cộng</span>
                        <div class="text-right">
                            <div class="total-amount">{{ number_format($finalTotal) }}đ</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Spacer for bottom button -->
            <div class="mb-20"></div>
        </div>

        <!-- Fixed Bottom Button -->
        <div class="fixed bottom-0 left-1/2 transform -translate-x-1/2 w-full max-w-[400px] bg-background-dark p-4">
            <button onclick="proceedToPayment()" class="confirm-btn w-full py-4 text-black font-bold text-lg">
                Tiếp tục thanh toán
            </button>
        </div>
    </div>

    <script>
        function editAddress() {
            window.location.href = '/checkout';
        }

        function proceedToPayment() {
            // Get order note
            const orderNote = $('#orderNote').val().trim();
            
            // Save note to session
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Show loading state
            $('.confirm-btn').prop('disabled', true).html(`
                <span class="animate-spin material-symbols-outlined mr-2">refresh</span>
                Đang xử lý...
            `);

            $.post('/api/checkout/set-note', { note: orderNote }, function(response) {
                if (response.success) {
                    window.location.href = '/checkout/payment';
                } else {
                    alert('Có lỗi xảy ra: ' + response.message);
                    $('.confirm-btn').prop('disabled', false).text('Tiếp tục thanh toán');
                }
            }).fail(function(xhr) {
                let errorMessage = 'Có lỗi xảy ra, vui lòng thử lại!';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                alert(errorMessage);
                $('.confirm-btn').prop('disabled', false).text('Tiếp tục thanh toán');
            });
        }
    </script>
</body>
</html>