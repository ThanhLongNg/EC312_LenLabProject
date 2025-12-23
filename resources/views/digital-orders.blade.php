<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đơn hàng số của tôi - LENLAB</title>
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
        <button class="flex items-center justify-center size-10 rounded-full hover:bg-white/10 transition-colors" onclick="history.back()">
            <span class="material-symbols-outlined text-white">arrow_back</span>
        </button>
        <h1 class="text-lg font-medium text-white">Đơn hàng số của tôi</h1>
    </div>
</header>

<!-- Main Content -->
<main class="flex-grow px-4 py-6">
    @if($purchases->isEmpty())
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-24 h-24 bg-gray-700 rounded-full flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-gray-400 text-4xl">cloud_download</span>
            </div>
            <h2 class="text-white text-xl font-semibold mb-2">Chưa có đơn hàng số nào</h2>
            <p class="text-gray-400 mb-6 max-w-sm">
                Bạn chưa mua sản phẩm số nào. Khám phá các sản phẩm số của chúng tôi ngay!
            </p>
            <button onclick="window.location.href='/san-pham-so'" 
                    class="bg-primary hover:bg-primary/90 text-background-dark px-6 py-3 rounded-full font-bold transition-colors">
                Xem sản phẩm số
            </button>
        </div>
    @else
        <!-- Orders List -->
        <div class="space-y-4">
            @foreach($purchases as $purchase)
                <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 cursor-pointer hover:border-primary/20 transition-colors" 
                     onclick="window.location.href='/digital-orders/{{ $purchase->id }}'">>
                    
                    <!-- Order Header -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-400 text-sm">{{ $purchase->order_code }}</span>
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($purchase->status == 'active') bg-green-500/20 text-green-400
                                @elseif($purchase->status == 'pending') bg-yellow-500/20 text-yellow-400
                                @elseif($purchase->status == 'expired') bg-red-500/20 text-red-400
                                @else bg-gray-500/20 text-gray-400
                                @endif
                            ">
                                @switch($purchase->status)
                                    @case('active') Đã kích hoạt @break
                                    @case('pending') Đang xử lý @break
                                    @case('expired') Đã hết hạn @break
                                    @case('cancelled') Đã hủy @break
                                    @default {{ $purchase->status }}
                                @endswitch
                            </span>
                        </div>
                        <span class="text-gray-400 text-sm">{{ $purchase->purchased_at->format('d/m/Y') }}</span>
                    </div>
                    
                    <!-- Product Info -->
                    <div class="flex gap-3 mb-3">
                        <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 bg-gray-700">
                            @if($purchase->digitalProduct->thumbnail_url)
                                <img src="{{ $purchase->digitalProduct->thumbnail_url }}" alt="{{ $purchase->digitalProduct->name }}" class="w-full h-full object-cover"/>
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-xl">description</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="text-white font-medium mb-1 line-clamp-2">{{ $purchase->digitalProduct->name }}</h3>
                            <p class="text-gray-400 text-sm mb-2">{{ Str::limit($purchase->digitalProduct->description, 60) }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-primary font-bold">{{ number_format($purchase->amount_paid ?? $purchase->purchase_price ?? 0) }}đ</span>
                                <div class="flex items-center gap-4 text-xs text-gray-400">
                                    <span>{{ $purchase->downloads_count ?? $purchase->download_count ?? 0 }}/{{ $purchase->digitalProduct->download_limit ?? 'Không giới hạn' }} lần tải</span>
                                    @if($purchase->expires_at)
                                        <span>Hết hạn: {{ $purchase->expires_at->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-2 pt-3 border-t border-white/10">
                        @if($purchase->status == 'active' && !$purchase->isExpired())
                            <button class="flex-1 bg-primary/20 text-primary py-2 px-4 rounded-lg text-sm font-medium hover:bg-primary/30 transition-colors">
                                Tải xuống
                            </button>
                        @endif
                        <button class="flex-1 bg-white/10 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-white/20 transition-colors">
                            Xem chi tiết
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($purchases->hasPages())
            <div class="mt-8">
                {{ $purchases->links() }}
            </div>
        @endif
    @endif
</main>

</body>
</html>