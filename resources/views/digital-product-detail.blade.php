<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product->name }} - LENLAB</title>
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
    <style>
        .hide-scrollbar::-webkit-scrollbar {display: none;}
        .hide-scrollbar {-ms-overflow-style: none; scrollbar-width: none;}
        
        /* Badge styles */
        .badge {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px 8px;
            border-radius: 6px;
        }
        
        .badge-pdf { background: #FF6B35; color: white; }
        .badge-video { background: #4ECDC4; color: white; }
        .badge-ebook { background: #45B7D1; color: white; }
    </style>
</head>
<body class="bg-background-dark font-display min-h-screen flex flex-col antialiased">

<!-- Header -->
<header class="sticky top-0 z-50 w-full bg-background-dark/95 backdrop-blur-md border-b border-white/10">
    <div class="px-4 py-3 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <button class="flex items-center justify-center size-10 rounded-full hover:bg-white/10 transition-colors" onclick="history.back()">
                <span class="material-symbols-outlined text-white">arrow_back</span>
            </button>
            <h1 class="text-lg font-medium text-white">Chi tiết sản phẩm số</h1>
        </div>
        
        <button class="flex items-center justify-center size-10 rounded-full hover:bg-white/10 transition-colors" onclick="shareProduct()">
            <span class="material-symbols-outlined text-white">share</span>
        </button>
    </div>
</header>

<!-- Main Content -->
<main class="flex-grow pb-24">
    <!-- Product Hero -->
    <section class="relative">
        <!-- Product Image -->
        <div class="relative w-full h-80 bg-gradient-to-br from-gray-700 to-gray-800">
            @if($product->thumbnail_url)
                <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover"/>
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-8xl opacity-50">description</span>
                </div>
            @endif
            
            <!-- Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            
            <!-- Badge -->
            <div class="absolute top-4 left-4">
                <span class="badge 
                    @switch($product->type)
                        @case('course') badge-video @break
                        @case('file') badge-pdf @break
                        @default badge-ebook
                    @endswitch
                ">
                    @switch($product->type)
                        @case('course') VIDEO @break
                        @case('file') PDF @break
                        @default E-BOOK
                    @endswitch
                </span>
            </div>
            
            <!-- Rating (for courses) -->
            @if($product->type == 'course')
                <div class="absolute top-4 right-4 flex items-center gap-1 bg-black/50 rounded-full px-2 py-1">
                    <span class="material-symbols-outlined text-yellow-400 text-sm">star</span>
                    <span class="text-white text-sm font-medium">4.9</span>
                </div>
            @endif
        </div>
        
        <!-- Product Info Card -->
        <div class="relative -mt-6 mx-4 bg-surface-dark rounded-t-3xl border border-white/10 p-6">
            <h1 class="text-2xl font-bold text-white mb-3 leading-tight">{{ $product->name }}</h1>
            
            <!-- Price and Stats -->
            <div class="flex items-center justify-between mb-4">
                <div class="text-3xl font-bold text-primary">{{ number_format($product->price) }}đ</div>
                @if($product->type == 'course')
                    <div class="flex items-center gap-2 text-sm">
                        <div class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-yellow-400 text-lg">star</span>
                            <span class="text-white font-semibold">4.9</span>
                        </div>
                        <span class="text-gray-400">•</span>
                        <span class="text-gray-400">128 đánh giá</span>
                    </div>
                @endif
            </div>
            
            <!-- Quick Stats -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="text-center">
                    <div class="text-white font-bold text-lg">{{ $product->download_limit }}</div>
                    <div class="text-gray-400 text-xs">Lần tải</div>
                </div>
                <div class="text-center">
                    <div class="text-white font-bold text-lg">{{ $product->access_days }}</div>
                    <div class="text-gray-400 text-xs">Ngày truy cập</div>
                </div>
                <div class="text-center">
                    <div class="text-white font-bold text-lg">{{ count($product->files ?? []) }}</div>
                    <div class="text-gray-400 text-xs">File</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Content Sections -->
    <div class="px-4 space-y-6">
        <!-- Description -->
        <section class="bg-surface-dark rounded-2xl p-6 border border-white/10">
            <h2 class="text-lg font-semibold text-white mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">description</span>
                Mô tả sản phẩm
            </h2>
            <p class="text-gray-300 leading-relaxed">{{ $product->description }}</p>
        </section>

        <!-- Instructions -->
        @if($product->instructions)
        <section class="bg-surface-dark rounded-2xl p-6 border border-white/10">
            <h2 class="text-lg font-semibold text-white mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">info</span>
                Hướng dẫn sử dụng
            </h2>
            <p class="text-gray-300 leading-relaxed text-sm">{{ $product->instructions }}</p>
        </section>
        @endif

        <!-- Files Content -->
        @if($product->files && count($product->files) > 0)
        <section class="bg-surface-dark rounded-2xl p-6 border border-white/10">
            <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">folder</span>
                Nội dung bao gồm
            </h2>
            <div class="space-y-3">
                @foreach($product->files as $file)
                <div class="flex items-center gap-3 p-3 bg-black/20 rounded-xl border border-white/5">
                    <div class="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-lg">description</span>
                    </div>
                    <div class="flex-1">
                        <div class="text-white font-medium text-sm">{{ $file['name'] }}</div>
                        <div class="text-gray-400 text-xs">{{ number_format($file['size'] / 1024 / 1024, 2) }} MB</div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Related Products -->
        @if($relatedProducts->isNotEmpty())
        <section class="bg-surface-dark rounded-2xl p-6 border border-white/10">
            <h2 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">recommend</span>
                Sản phẩm liên quan
            </h2>
            <div class="space-y-3">
                @foreach($relatedProducts as $related)
                <div class="flex gap-3 p-3 bg-black/20 rounded-xl border border-white/5 hover:border-primary/20 transition-colors cursor-pointer" onclick="window.location.href='/san-pham-so/{{ $related->id }}'">
                    <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 bg-gray-700">
                        @if($related->thumbnail_url)
                            <img src="{{ $related->thumbnail_url }}" alt="{{ $related->name }}" class="w-full h-full object-cover"/>
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-xl">description</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-white font-medium text-sm line-clamp-1 mb-1">{{ $related->name }}</h3>
                        <p class="text-gray-400 text-xs line-clamp-2 mb-2">{{ Str::limit($related->description, 60) }}</p>
                        <div class="text-primary font-bold text-sm">{{ number_format($related->price) }}đ</div>
                    </div>
                    <div class="flex items-center">
                        <span class="material-symbols-outlined text-gray-400">chevron_right</span>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Reviews Section -->
        <section class="bg-surface-dark rounded-2xl p-6 border border-white/10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">star</span>
                    Đánh giá sản phẩm
                </h2>
                <button onclick="window.location.href='{{ route('digital-products.reviews', $product->id) }}'" 
                        class="text-primary text-sm font-medium hover:text-primary/80 transition-colors">
                    Xem tất cả
                </button>
            </div>
            
            @if($product->review_count > 0)
                <div class="flex items-center gap-4 mb-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary">{{ number_format($product->average_rating, 1) }}</div>
                        <div class="flex items-center justify-center gap-1 mb-1">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="material-symbols-outlined text-sm {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-600' }}">star</span>
                            @endfor
                        </div>
                        <div class="text-gray-400 text-xs">{{ $product->review_count }} đánh giá</div>
                    </div>
                    
                    <div class="flex-1">
                        <p class="text-gray-300 text-sm">
                            Sản phẩm được đánh giá cao bởi những khách hàng đã sử dụng.
                        </p>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <span class="material-symbols-outlined text-gray-400 text-3xl mb-2">rate_review</span>
                    <p class="text-gray-400 text-sm">Chưa có đánh giá nào cho sản phẩm này</p>
                </div>
            @endif
        </section>
    </div>
</main>

<!-- Fixed Bottom Purchase Bar -->
<div class="fixed bottom-0 left-0 right-0 bg-background-dark/95 backdrop-blur-md border-t border-white/10 p-4 z-40">
    <div class="flex items-center gap-4">
        <!-- Price Info -->
        <div class="flex-1">
            <div class="text-gray-400 text-xs">Tổng thanh toán</div>
            <div class="text-xl font-bold text-primary">{{ number_format($product->price) }}đ</div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex gap-2">
            <button class="bg-primary hover:bg-primary/90 text-background-dark px-6 py-3 rounded-full font-bold transition-colors flex items-center gap-2" onclick="buyNow({{ $product->id }})">
                <span class="material-symbols-outlined">shopping_bag</span>
                Mua hàng
            </button>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function buyNow(productId) {
    // Redirect to digital order confirmation page
    window.location.href = `/xac-nhan-don-hang-so/${productId}`;
}

function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $product->name }}',
            text: '{{ $product->description }}',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link đã được sao chép vào clipboard!');
        });
    }
}
</script>

</body>
</html>