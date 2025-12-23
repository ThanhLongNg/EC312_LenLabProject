<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đơn hàng thành công - LENLAB</title>
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&family=Noto+Sans:wght@400;500;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#FAC638", 
                        "background-light": "#f8f8f5", 
                        "background-dark": "#231e0f", 
                        "surface-dark": "#1c2e24", 
                        "surface-light": "#ffffff"
                    },
                    fontFamily: {
                        "display": ["Spline Sans", "sans-serif"],
                        "body": ["Noto Sans", "sans-serif"]
                    }
                },
            },
        }
    </script>
</head>
<body class="bg-background-dark font-display min-h-screen flex flex-col antialiased">

<!-- Header -->
<header class="sticky top-0 z-50 w-full bg-background-dark/95 backdrop-blur-md border-b border-white/10">
    <div class="px-4 py-3 flex items-center gap-3">
        <h1 class="text-lg font-medium text-white">Đơn hàng thành công</h1>
    </div>
</header>

<!-- Main Content -->
<main class="flex-grow px-4 py-8">
    <div class="max-w-md w-full mx-auto">
        <!-- Success Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-500 rounded-full mb-4">
                <span class="material-symbols-outlined text-white text-2xl">check</span>
            </div>
            <h1 class="text-xl font-bold text-white mb-2">Thanh toán thành công!</h1>
            <p class="text-gray-300 text-sm leading-relaxed">
                Cảm ơn bạn đã ủng hộ Len Thủ Công. Đơn hàng sản phẩm số của bạn đã được xác nhận.
            </p>
        </div>
        
        <!-- Order Info Card -->
        <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="text-gray-400 text-sm">MÃ ĐƠN HÀNG</div>
                    <div class="text-white font-bold text-lg">{{ $purchase->order_code }}</div>
                </div>
                <div class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-sm font-medium">
                    Hoàn tất
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="flex gap-3 p-3 bg-black/20 rounded-xl">
                <div class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 bg-gray-700">
                    @if($purchase->digitalProduct->thumbnail_url)
                        <img src="{{ $purchase->digitalProduct->thumbnail_url }}" alt="{{ $purchase->digitalProduct->name }}" class="w-full h-full object-cover"/>
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-lg">description</span>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <h3 class="text-white font-medium text-sm mb-1">{{ $purchase->digitalProduct->name }}</h3>
                    <p class="text-gray-400 text-xs mb-2">Số lượng: 1</p>
                    <div class="text-primary font-bold">{{ number_format($purchase->amount_paid) }}đ</div>
                </div>
            </div>
        </div>

        <!-- Download Instructions -->
        <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
            <h2 class="text-white font-semibold mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">mail</span>
                Hướng dẫn nhận file
            </h2>
            <p class="text-gray-300 text-sm mb-3">
                Link tải sản phẩm đã được gửi tự động đến email của bạn. Vui lòng kiểm tra hộp thư đến.
            </p>
            <div class="flex items-center gap-2 p-3 bg-black/20 rounded-xl">
                <span class="material-symbols-outlined text-primary">email</span>
                <span class="text-white font-medium">{{ $purchase->customer_email }}</span>
            </div>
            <p class="text-gray-400 text-xs mt-3">
                Nếu không nhận được email, vui lòng kiểm tra thư mục spam hoặc liên hệ với chúng tôi để được hỗ trợ.
            </p>
        </div>

        <!-- Support Section -->
        <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-8">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-primary">help</span>
                <span class="text-white font-medium text-sm">Cần hỗ trợ kỹ thuật?</span>
            </div>
            <p class="text-gray-400 text-xs">
                Liên hệ với chúng tôi qua email hoặc hotline để được hỗ trợ nhanh chóng.
            </p>
        </div>
        
        <!-- Action Buttons -->
        <div class="space-y-3">
            <button onclick="window.location.href='/san-pham-so'" 
                    class="w-full bg-primary hover:bg-primary/90 text-background-dark py-4 rounded-full font-bold text-lg transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">shopping_bag</span>
                Tiếp tục mua sắm
            </button>
            <button onclick="window.location.href='/orders'" 
                    class="w-full bg-white/10 hover:bg-white/20 text-white py-3 rounded-full font-medium transition-colors">
                Xem sản phẩm đã mua
            </button>
        </div>
    </div>
</main>

</body>
</html>