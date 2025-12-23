<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chi tiết đơn hàng số - LENLAB</title>
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
        <h1 class="text-lg font-medium text-white">Chi tiết đơn hàng số</h1>
    </div>
</header>

<!-- Main Content -->
<main class="flex-grow px-4 py-6">
    <!-- Order Status -->
    <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <div class="text-gray-400 text-sm">MÃ ĐƠN HÀNG</div>
                <div class="text-white font-bold text-lg">{{ $purchase->order_code }}</div>
            </div>
            <div class="px-3 py-1 rounded-full text-sm font-medium
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
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <div class="text-gray-400">Ngày mua</div>
                <div class="text-white font-medium">{{ $purchase->purchased_at->format('d/m/Y H:i') }}</div>
            </div>
            @if($purchase->expires_at)
            <div>
                <div class="text-gray-400">Hết hạn</div>
                <div class="text-white font-medium">{{ $purchase->expires_at->format('d/m/Y') }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Product Info -->
    <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
        <h2 class="text-white font-semibold mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">inventory_2</span>
            Thông tin sản phẩm
        </h2>
        
        <div class="flex gap-3 mb-4">
            <div class="w-20 h-20 rounded-xl overflow-hidden flex-shrink-0 bg-gray-700">
                @if($purchase->digitalProduct->thumbnail_url)
                    <img src="{{ $purchase->digitalProduct->thumbnail_url }}" alt="{{ $purchase->digitalProduct->name }}" class="w-full h-full object-cover"/>
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-xl">description</span>
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <h3 class="text-white font-medium mb-2">{{ $purchase->digitalProduct->name }}</h3>
                <p class="text-gray-400 text-sm mb-3 leading-relaxed">{{ $purchase->digitalProduct->description }}</p>
                <div class="text-primary font-bold text-lg">{{ number_format($purchase->amount_paid) }}đ</div>
            </div>
        </div>
        
        <!-- Product Stats -->
        <div class="grid grid-cols-3 gap-4 p-3 bg-black/20 rounded-xl">
            <div class="text-center">
                <div class="text-white font-bold">{{ $purchase->downloads_count }}</div>
                <div class="text-gray-400 text-xs">Đã tải</div>
            </div>
            <div class="text-center">
                <div class="text-white font-bold">{{ $purchase->digitalProduct->download_limit }}</div>
                <div class="text-gray-400 text-xs">Giới hạn</div>
            </div>
            <div class="text-center">
                <div class="text-white font-bold">{{ $purchase->digitalProduct->access_days }}</div>
                <div class="text-gray-400 text-xs">Ngày truy cập</div>
            </div>
        </div>
    </div>

    <!-- Customer Info -->
    <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
        <h2 class="text-white font-semibold mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">person</span>
            Thông tin khách hàng
        </h2>
        
        <div class="space-y-3">
            <div class="flex items-center gap-3 p-3 bg-black/20 rounded-xl">
                <span class="material-symbols-outlined text-gray-400">person</span>
                <div>
                    <div class="text-gray-400 text-sm">Tên khách hàng</div>
                    <div class="text-white font-medium">{{ $purchase->customer_name }}</div>
                </div>
            </div>
            
            <div class="flex items-center gap-3 p-3 bg-black/20 rounded-xl">
                <span class="material-symbols-outlined text-gray-400">email</span>
                <div>
                    <div class="text-gray-400 text-sm">Email nhận file</div>
                    <div class="text-white font-medium">{{ $purchase->customer_email }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Download Section -->
    @if($purchase->status == 'active' && !$purchase->isExpired())
        <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
            <h2 class="text-white font-semibold mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">cloud_download</span>
                Tải xuống sản phẩm
            </h2>
            
            @if($purchase->digitalProduct->files && count($purchase->digitalProduct->files) > 0)
                <div class="space-y-3">
                    @foreach($purchase->digitalProduct->files as $file)
                        <div class="flex items-center gap-3 p-3 bg-black/20 rounded-xl">
                            <div class="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary text-lg">description</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-white font-medium text-sm">{{ $file['name'] }}</div>
                                <div class="text-gray-400 text-xs">{{ number_format($file['size'] / 1024 / 1024, 2) }} MB</div>
                            </div>
                            <button onclick="downloadFile('{{ $file['path'] }}')" 
                                    class="bg-primary text-background-dark px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                                Tải xuống
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
            
            @if($purchase->digitalProduct->links && count($purchase->digitalProduct->links) > 0)
                <div class="space-y-3 mt-4">
                    @foreach($purchase->digitalProduct->links as $link)
                        <div class="flex items-center gap-3 p-3 bg-black/20 rounded-xl">
                            <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-blue-400 text-lg">link</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-white font-medium text-sm">{{ $link['name'] }}</div>
                                <div class="text-gray-400 text-xs">Liên kết trực tuyến</div>
                            </div>
                            <button onclick="window.open('{{ $link['url'] }}', '_blank')" 
                                    class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-600 transition-colors">
                                Truy cập
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
            
            <div class="mt-4 p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-xl">
                <p class="text-yellow-400 text-sm">
                    <span class="material-symbols-outlined text-lg mr-1">info</span>
                    Bạn còn {{ $purchase->digitalProduct->download_limit - $purchase->downloads_count }} lần tải. 
                    Quyền truy cập sẽ hết hạn vào {{ $purchase->expires_at->format('d/m/Y') }}.
                </p>
            </div>
        </div>
    @elseif($purchase->status == 'pending')
        <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
            <div class="text-center py-8">
                <span class="material-symbols-outlined text-yellow-400 text-4xl mb-4">pending</span>
                <h3 class="text-white font-semibold mb-2">Đơn hàng đang được xử lý</h3>
                <p class="text-gray-400 text-sm">
                    Chúng tôi đang xác minh thanh toán của bạn. 
                    Bạn sẽ nhận được email khi đơn hàng được kích hoạt.
                </p>
            </div>
        </div>
    @else
        <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
            <div class="text-center py-8">
                <span class="material-symbols-outlined text-red-400 text-4xl mb-4">error</span>
                <h3 class="text-white font-semibold mb-2">Lưu ý</h3>
                <p class="text-gray-400 text-sm">
                    @if($purchase->isExpired())
                        Quyền truy cập đã hết hạn vào {{ $purchase->expires_at->format('d/m/Y') }}.
                    @else
                        Đơn hàng đã được gửi thông tin qua mail của bạn.
                    @endif
                </p>
            </div>
        </div>
    @endif

    <!-- Review Section - Available for all purchases -->
    <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
        <h2 class="text-white font-semibold mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">rate_review</span>
            Đánh giá sản phẩm
        </h2>
        
        @php
            $hasReviewed = \App\Models\Comment::where('user_id', auth()->id())
                                             ->where('digital_product_id', $purchase->digital_product_id)
                                             ->where('digital_purchase_id', $purchase->id)
                                             ->exists();
        @endphp
        
        @if($hasReviewed)
            <div class="text-center py-6">
                <span class="material-symbols-outlined text-green-400 text-3xl mb-2">check_circle</span>
                <p class="text-white font-medium mb-2">Bạn đã đánh giá sản phẩm này</p>
                <button onclick="window.location.href='/san-pham-so/{{ $purchase->digital_product_id }}/danh-gia'" 
                        class="bg-white/10 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-white/20 transition-colors">
                    Xem đánh giá
                </button>
            </div>
        @else
            <div class="text-center py-6">
                <span class="material-symbols-outlined text-primary text-3xl mb-2">star</span>
                <p class="text-white font-medium mb-2">Chia sẻ trải nghiệm của bạn</p>
                <p class="text-gray-400 text-sm mb-4">Đánh giá sản phẩm để giúp khách hàng khác</p>
                <button onclick="window.location.href='/san-pham-so/{{ $purchase->digital_product_id }}/danh-gia/tao/{{ $purchase->id }}'" 
                        class="bg-primary text-background-dark px-6 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                    Đánh giá sản phẩm
                </button>
            </div>
        @endif
    </div>

    <!-- Download History -->
    @if($purchase->download_history && count($purchase->download_history) > 0)
        <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
            <h2 class="text-white font-semibold mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">history</span>
                Lịch sử tải xuống
            </h2>
            
            <div class="space-y-2">
                @foreach($purchase->download_history as $download)
                    <div class="flex items-center justify-between p-3 bg-black/20 rounded-xl">
                        <div class="text-white text-sm">
                            Tải xuống lần {{ $loop->iteration }}
                        </div>
                        <div class="text-gray-400 text-sm">
                            {{ \Carbon\Carbon::parse($download['downloaded_at'])->format('d/m/Y H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</main>

<!-- Scripts -->
<script>
function downloadFile(filePath) {
    // TODO: Implement secure file download with tracking
    alert('Chức năng tải xuống sẽ được triển khai sau khi tích hợp hệ thống bảo mật file.');
}
</script>

</body>
</html>