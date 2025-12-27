<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $voucher ? 'Mã giảm giá đặc biệt' : 'Giỏ hàng của bạn đang chờ' }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .voucher-box {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            margin: 25px 0;
            box-shadow: 0 4px 15px rgba(238, 90, 36, 0.3);
        }
        .voucher-code {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 3px;
            margin: 15px 0;
            padding: 15px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            border: 2px dashed rgba(255,255,255,0.5);
        }
        .voucher-details {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 15px;
        }
        .cart-items {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }
        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
            border: 2px solid #e9ecef;
        }
        .item-details {
            flex: 1;
        }
        .item-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .item-info {
            color: #6c757d;
            font-size: 14px;
        }
        .item-price {
            font-weight: 600;
            color: #e74c3c;
            font-size: 16px;
        }
        .total-section {
            background: linear-gradient(135deg, #74b9ff, #0984e3);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin: 25px 0;
        }
        .total-amount {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #00b894, #00a085);
            color: white;
            padding: 18px 40px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 18px;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(0, 184, 148, 0.3);
            transition: transform 0.3s ease;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        .footer {
            background-color: #2c3e50;
            color: white;
            padding: 25px 20px;
            text-align: center;
            font-size: 14px;
        }
        .footer a {
            color: #74b9ff;
            text-decoration: none;
        }
        .social-links {
            margin: 15px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #74b9ff;
            font-size: 20px;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 8px;
            }
            .header, .content, .footer {
                padding: 20px 15px;
            }
            .voucher-code {
                font-size: 24px;
                letter-spacing: 2px;
            }
            .cart-item {
                flex-direction: column;
                text-align: center;
            }
            .item-image {
                margin: 0 0 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $voucher ? '🎁 Quà tặng đặc biệt!' : '🛒 Giỏ hàng đang chờ bạn' }}</h1>
            <p>{{ config('app.name', 'Lenlab Official') }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Xin chào <strong>{{ $user->name }}</strong>,
            </div>

            @if($voucher)
                <p>Chúng tôi nhận thấy bạn đã để lại một số sản phẩm tuyệt vời trong giỏ hàng. Để cảm ơn sự quan tâm của bạn, chúng tôi xin gửi tặng một mã giảm giá đặc biệt!</p>

                <div class="voucher-box">
                    <div style="font-size: 20px; margin-bottom: 10px;">🎉 MÃ GIẢM GIÁ ĐẶC BIỆT</div>
                    <div class="voucher-code">{{ $voucher->code }}</div>
                    <div class="voucher-details">
                        <strong>Giảm {{ $voucher->type === 'percent' ? $voucher->discount_value . '%' : number_format($voucher->discount_value) . 'đ' }}</strong><br>
                        Có hiệu lực đến: {{ $voucher->end_date->format('d/m/Y') }}<br>
                        @if($voucher->min_order_value > 0)
                            Đơn hàng tối thiểu: {{ number_format($voucher->min_order_value) }}đ
                        @endif
                    </div>
                </div>
            @else
                <p>Chúng tôi nhận thấy bạn đã để lại một số sản phẩm tuyệt vời trong giỏ hàng. Đừng để chúng chờ đợi quá lâu nhé!</p>
            @endif

            <!-- Cart Items -->
            <div class="cart-items">
                <h3 style="margin-top: 0; color: #2c3e50; text-align: center;">📦 Sản phẩm trong giỏ hàng của bạn</h3>
                
                @foreach($cartItems as $item)
                    <div class="cart-item">
                        @if($item->product && $item->product->image)
                            <img src="{{ asset('storage/products/' . $item->product->image) }}" 
                                 alt="{{ $item->product->name }}" 
                                 class="item-image"
                                 onerror="this.style.display='none'">
                        @else
                            <div class="item-image" style="background-color: #e9ecef; display: flex; align-items: center; justify-content: center;">
                                <span style="color: #6c757d;">📷</span>
                            </div>
                        @endif
                        
                        <div class="item-details">
                            <div class="item-name">{{ $item->product->name ?? 'Sản phẩm' }}</div>
                            <div class="item-info">
                                Số lượng: {{ $item->quantity }}
                                @if($item->variant_info && isset($item->variant_info['variant_name']))
                                    • {{ $item->variant_info['variant_name'] }}
                                @endif
                            </div>
                        </div>
                        
                        <div class="item-price">
                            {{ number_format($item->price_at_time * $item->quantity) }}đ
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Total -->
            <div class="total-section">
                <div style="font-size: 18px;">💰 Tổng giá trị giỏ hàng</div>
                <div class="total-amount">{{ number_format($total) }}đ</div>
                @if($voucher && $voucher->type === 'percent')
                    <div style="font-size: 16px; opacity: 0.9;">
                        Sau giảm giá: <strong>{{ number_format($total * (100 - $voucher->discount_value) / 100) }}đ</strong>
                    </div>
                @endif
            </div>

            <!-- CTA Button -->
            <div style="text-align: center;">
                <a href="{{ url('/cart') }}" class="cta-button">
                    {{ $voucher ? '🎁 Sử dụng mã ngay' : '🛒 Hoàn tất đơn hàng' }}
                </a>
            </div>

            @if($voucher)
                <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin: 20px 0; text-align: center;">
                    <strong>💡 Cách sử dụng:</strong><br>
                    <span style="color: #856404;">Thêm mã <strong>{{ $voucher->code }}</strong> vào giỏ hàng để nhận ưu đãi!</span>
                </div>
            @endif

            <p style="color: #6c757d; font-size: 14px; text-align: center; margin-top: 30px;">
                Nếu bạn có bất kỳ câu hỏi nào, đừng ngần ngại liên hệ với chúng tôi. 
                Chúng tôi luôn sẵn sàng hỗ trợ bạn! 💪
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div>
                <strong>{{ config('app.name', 'Lenlab Official') }}</strong><br>
                Cảm ơn bạn đã tin tưởng và lựa chọn chúng tôi! ❤️
            </div>
            
            <div class="social-links">
                <a href="#" title="Facebook">📘</a>
                <a href="#" title="Instagram">📷</a>
                <a href="#" title="Email">📧</a>
            </div>
            
            <div style="font-size: 12px; opacity: 0.8; margin-top: 15px;">
                Email này được gửi tự động. Vui lòng không trả lời email này.<br>
                Nếu bạn không muốn nhận email này, vui lòng <a href="#">bỏ đăng ký</a>.
            </div>
        </div>
    </div>
</body>
</html>