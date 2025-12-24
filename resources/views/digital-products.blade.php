<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sản phẩm số - LENLAB</title>
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
                        "surface-light": "#ffffff", 
                        "pastel-orange": "#FFCCBC", 
                        "pastel-orange-dark": "#E64A19"
                    },
                    fontFamily: {
                        "display": ["Spline Sans", "sans-serif"],
                        "body": ["Noto Sans", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "1rem", 
                        "lg": "1.5rem", 
                        "xl": "2rem", 
                        "2xl": "2.5rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        .hide-scrollbar::-webkit-scrollbar {display: none;}
        .hide-scrollbar {-ms-overflow-style: none; scrollbar-width: none;}
        body {min-height: max(884px, 100dvh);}
        
        /* Product card hover effects */
        .product-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        /* Filter button styles */
        .filter-btn {
            transition: all 0.2s ease;
        }
        
        .filter-btn.active {
            background: #FAC638;
            color: #231e0f;
        }
        
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
        .badge-free { background: #96CEB4; color: #2C3E50; }
    </style>
</head>
<body class="bg-background-dark font-display min-h-screen flex flex-col antialiased selection:bg-primary selection:text-background-dark">

<!-- Header -->
<header class="sticky top-0 z-50 w-full bg-background-dark/95 backdrop-blur-md border-b border-white/10">
    <div class="px-4 py-3 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <button class="flex items-center justify-center size-10 rounded-full hover:bg-white/10 transition-colors" id="mobileMenuBtn">
                <span class="material-symbols-outlined text-white">menu</span>
            </button>
            <h1 class="text-xl font-bold text-primary tracking-wide">LENLAB</h1>
        </div>
        
        <!-- Search Bar (Desktop) -->
        <div class="hidden md:flex flex-1 max-w-md mx-4">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <span class="material-symbols-outlined text-gray-400 text-[20px]">search</span>
                </span>
                <input class="w-full py-2 pl-10 pr-4 bg-surface-dark text-white rounded-full border border-white/10 text-sm focus:ring-2 focus:ring-primary/50 placeholder:text-gray-400 transition-all" placeholder="Tìm kiếm..." type="text" id="desktopSearchInput"/>
            </div>
        </div>
        
        <div class="flex items-center gap-1">
            <!-- Search Button (Mobile) -->
            <button class="md:hidden flex items-center justify-center size-10 rounded-full hover:bg-white/10 transition-colors" id="mobileSearchBtn">
                <span class="material-symbols-outlined text-white">search</span>
            </button>
            
            <!-- Cart Button -->
            <button class="relative flex items-center justify-center size-10 rounded-full hover:bg-white/10 transition-colors" onclick="window.location.href='/cart'">
                <span class="material-symbols-outlined text-white">shopping_bag</span>
                <span class="absolute -top-1 -right-1 w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                    <span class="text-white text-xs font-bold">0</span>
                </span>
            </button>
            
            <!-- User Button -->
            @auth
                <button class="flex items-center justify-center size-10 rounded-full hover:bg-white/10 transition-colors" onclick="window.location.href='/profile'">
                    <span class="material-symbols-outlined text-white">account_circle</span>
                </button>
            @else
                <button class="flex items-center justify-center size-10 rounded-full hover:bg-white/10 transition-colors" onclick="window.location.href='/login'">
                    <span class="material-symbols-outlined text-white">account_circle</span>
                </button>
            @endauth
        </div>
    </div>
    
    <!-- Mobile Search Bar (Hidden by default) -->
    <div class="md:hidden px-4 pb-3 hidden" id="mobileSearchBar">
        <div class="relative">
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                <span class="material-symbols-outlined text-gray-400 text-[20px]">search</span>
            </span>
            <input class="w-full py-2 pl-10 pr-4 bg-surface-dark text-white rounded-full border border-white/10 text-sm focus:ring-2 focus:ring-primary/50 placeholder:text-gray-400 transition-all" placeholder="Tìm kiếm..." type="text" id="mobileSearchInput"/>
        </div>
    </div>
</header>

<!-- Search and Filters -->
<section class="px-4 py-4 bg-background-dark">
    <!-- Mobile Search (only visible when toggled) -->
    <div class="md:hidden mb-4 hidden" id="mobileSearchSection">
        <div class="relative">
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                <span class="material-symbols-outlined text-gray-400 text-[20px]">search</span>
            </span>
            <input class="w-full py-3 pl-10 pr-4 bg-surface-dark text-white rounded-2xl border border-white/10 text-sm focus:ring-2 focus:ring-primary/50 placeholder:text-gray-400 transition-all" placeholder="Tìm kiếm sách, khóa học..." type="text" id="searchInput"/>
        </div>
    </div>
    
    <!-- Filter Buttons -->
    <div class="flex gap-2 overflow-x-auto pb-2 hide-scrollbar">
        <button class="filter-btn flex-shrink-0 px-4 py-2 bg-primary text-background-dark rounded-full text-sm font-semibold active" data-filter="all">
            Tất cả
        </button>
        <button class="filter-btn flex-shrink-0 px-4 py-2 bg-white/10 text-white rounded-full text-sm font-medium hover:bg-white/20" data-filter="file">
            <span class="material-symbols-outlined text-sm mr-1">description</span>
            Mẫu móc
        </button>
        <button class="filter-btn flex-shrink-0 px-4 py-2 bg-white/10 text-white rounded-full text-sm font-medium hover:bg-white/20" data-filter="course">
            <span class="material-symbols-outlined text-sm mr-1">play_circle</span>
            Khóa học
        </button>
    </div>
</section>

<!-- Main Content -->
<main class="flex-grow px-4 pb-8">

    <!-- Products Grid -->
    <section class="space-y-4" id="productsContainer">
        @forelse($products as $product)
        <div class="bg-surface-dark rounded-2xl p-4 product-card border border-white/5 hover:border-primary/20 transition-all duration-300" data-category="{{ $product->type }}">
            <div class="flex gap-4">
                <!-- Product Image -->
                <div class="w-20 h-20 rounded-xl overflow-hidden flex-shrink-0 relative">
                    @if($product->thumbnail_url)
                        <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover"/>
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-gray-600 to-gray-700 flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-2xl">description</span>
                        </div>
                    @endif
                    
                    <!-- Badge -->
                    <div class="absolute top-1 left-1">
                        @if($product->price == 0)
                            <span class="badge badge-free text-[8px] px-1.5 py-0.5">FREE</span>
                        @else
                            <span class="badge 
                                @switch($product->type)
                                    @case('course') badge-video @break
                                    @case('file') badge-pdf @break
                                    @default badge-ebook
                                @endswitch
                                text-[8px] px-1.5 py-0.5">
                                @switch($product->type)
                                    @case('course') VIDEO @break
                                    @case('file') PDF @break
                                    @default E-BOOK
                                @endswitch
                            </span>
                        @endif
                    </div>
                    
                    <!-- Rating (if course) -->
                    @if($product->type == 'course')
                        <div class="absolute bottom-1 left-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-yellow-400 text-xs">star</span>
                            <span class="text-white text-[10px] font-medium">4.9</span>
                        </div>
                    @endif
                </div>
                
                <!-- Product Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <h3 class="text-white font-semibold text-base leading-tight mb-1">{{ $product->name }}</h3>
                            <p class="text-gray-400 text-sm line-clamp-2 mb-2">{{ Str::limit($product->description, 60) }}</p>
                        </div>
                    </div>
                    
                    <!-- Price and Action -->
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-lg font-bold text-primary">{{ number_format($product->price) }}đ</div>
                        </div>
                        <button class="bg-primary/20 text-primary px-4 py-2 rounded-full text-sm font-medium hover:bg-primary/30 transition-colors" onclick="window.location.href='/san-pham-so/{{ $product->id }}'">
                            Xem thêm
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <span class="material-symbols-outlined text-6xl text-gray-600 mb-4">inventory_2</span>
            <h3 class="text-xl font-semibold text-white mb-2">Chưa có sản phẩm số nào</h3>
            <p class="text-gray-400">Hãy quay lại sau để khám phá những sản phẩm số mới nhất!</p>
        </div>
        @endforelse
    </section>

    <!-- Pagination -->
    @if($products->hasPages())
    <div class="mt-8">
        {{ $products->links('pagination::tailwind') }}
    </div>
    @endif
</main>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Mobile menu functionality
    $('#mobileMenuBtn').click(function() {
        showMobileMenu();
    });
    
    // Mobile search toggle
    $('#mobileSearchBtn').click(function() {
        $('#mobileSearchBar').toggleClass('hidden');
        if (!$('#mobileSearchBar').hasClass('hidden')) {
            $('#mobileSearchInput').focus();
        }
    });
    
    // Sync search inputs
    $('#desktopSearchInput, #mobileSearchInput').on('input', function() {
        const value = $(this).val();
        $('#desktopSearchInput, #mobileSearchInput').val(value);
        performSearch(value);
    });
    
    // Filter functionality
    $('.filter-btn').click(function() {
        $('.filter-btn').removeClass('active').addClass('bg-white/10 text-white').removeClass('bg-primary text-background-dark');
        $(this).addClass('active').removeClass('bg-white/10 text-white').addClass('bg-primary text-background-dark');
        
        const filter = $(this).data('filter');
        
        if (filter === 'all') {
            $('.product-card').show();
        } else {
            $('.product-card').hide();
            $(`.product-card[data-category="${filter}"]`).show();
        }
    });
    
    // Search functionality
    function performSearch(searchTerm) {
        searchTerm = searchTerm.toLowerCase();
        
        $('.product-card').each(function() {
            const productName = $(this).find('h3').text().toLowerCase();
            const productDesc = $(this).find('p').text().toLowerCase();
            
            if (productName.includes(searchTerm) || productDesc.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
    
    // Load more functionality
    $('#loadMoreBtn').click(function() {
        // Simulate loading more products
        $(this).text('Đang tải...').prop('disabled', true);
        
        setTimeout(() => {
            // Add more products here
            $(this).text('Xem thêm sản phẩm').prop('disabled', false);
        }, 1000);
    });
    
    // Favorite toggle
    $(document).on('click', '.material-symbols-outlined:contains("favorite_border")', function() {
        if ($(this).text() === 'favorite_border') {
            $(this).text('favorite').addClass('text-red-500');
        } else {
            $(this).text('favorite_border').removeClass('text-red-500');
        }
    });
    
    // Mobile menu function
    function showMobileMenu() {
        let menuHtml = `
            <div class="fixed inset-0 bg-black/60 z-50 backdrop-blur-sm" id="mobileMenuOverlay">
                <div class="fixed left-0 top-0 h-full w-80 bg-gradient-to-b from-[#2a2318] to-[#1a1610] shadow-2xl transform -translate-x-full transition-transform duration-300 ease-out" id="mobileMenuPanel">
                    <!-- Header -->
                    <div class="flex justify-between items-center p-6 border-b border-primary/20">
                        <h3 class="text-xl font-bold text-white">Menu</h3>
                        <button id="closeMobileMenu" class="p-2 hover:bg-white/10 rounded-full transition-colors">
                            <span class="material-symbols-outlined text-white">close</span>
                        </button>
                    </div>
                    
                    <!-- User Section -->
                    <div class="p-6 border-b border-primary/20">
                        @auth
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-primary to-yellow-600 rounded-full flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-xl">account_circle</span>
                                </div>
                                <div>
                                    <p class="text-white font-semibold">Xin chào, {{ Auth::user()->name }}!</p>
                                    <p class="text-yellow-300 text-sm">Chúc bạn mua sắm vui vẻ</p>
                                </div>
                            </div>
                        @else
                            <div class="text-center mb-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-primary to-yellow-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <span class="material-symbols-outlined text-white text-2xl">waving_hand</span>
                                </div>
                                <h4 class="text-white font-bold text-lg mb-2">Chào mừng bạn!</h4>
                                <p class="text-yellow-300 text-sm mb-4">Đăng nhập để trải nghiệm mua sắm tốt hơn</p>
                                <div class="flex gap-2">
                                    <a href="{{ route('login') }}" class="flex-1 bg-primary text-background-dark py-2 px-4 rounded-full text-sm font-semibold text-center hover:bg-primary/90 transition-colors">
                                        Đăng nhập
                                    </a>
                                    <a href="{{ route('register') }}" class="flex-1 border border-primary text-primary py-2 px-4 rounded-full text-sm font-semibold text-center hover:bg-primary/10 transition-colors">
                                        Đăng ký
                                    </a>
                                </div>
                            </div>
                        @endauth
                    </div>
                    
                    <!-- Navigation Menu -->
                    <div class="p-6">
                        <nav class="space-y-2">
                            <a href="/" class="flex items-center gap-4 p-3 rounded-xl hover:bg-white/10 transition-all duration-200 group">
                                <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">home</span>
                                <span class="text-white font-medium">Trang chủ</span>
                            </a>
                            
                            <a href="/san-pham" class="flex items-center gap-4 p-3 rounded-xl hover:bg-white/10 transition-all duration-200 group">
                                <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">inventory_2</span>
                                <span class="text-white font-medium">Sản phẩm</span>
                            </a>

                            <a href="/san-pham-so" class="flex items-center gap-4 p-3 rounded-xl bg-white/10 transition-all duration-200 group">
                                <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">description</span>
                                <span class="text-white font-medium">Sản phẩm số</span>
                            </a>
                            
                            <a href="/gioi-thieu" class="flex items-center gap-4 p-3 rounded-xl hover:bg-white/10 transition-all duration-200 group">
                                <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">info</span>
                                <span class="text-white font-medium">Về chúng tôi</span>
                            </a>
                                                            
                            <a href="#" class="flex items-center gap-4 p-3 rounded-xl hover:bg-white/10 transition-all duration-200 group">
                                <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">receipt_long</span>
                                <span class="text-white font-medium">Tin tức & Blog</span>
                            </a>

                            <a href="#" class="flex items-center gap-4 p-3 rounded-xl hover:bg-white/10 transition-all duration-200 group">
                                <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">support_agent</span>
                                <span class="text-white font-medium">Hỗ trợ</span>
                            </a>
                            
                        </nav>
                    </div>
                    
                    <!-- Footer -->
                    <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-primary/20">                        
                        <div class="flex justify-center gap-4 mt-4">
                            <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-colors">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-colors">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-colors">
                                <i class="fab fa-tiktok"></i>
                            </a>
                        </div>
                        
                        <p class="text-center text-yellow-300/60 text-xs mt-4">© 2025 LENLAB. All rights reserved.</p>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(menuHtml).addClass('overflow-hidden');
        
        // Animate menu in
        setTimeout(() => {
            $('#mobileMenuPanel').removeClass('-translate-x-full');
        }, 10);
    }
    
    // Close mobile menu
    $(document).on('click', '#closeMobileMenu', function(e) {
        e.preventDefault();
        $('#mobileMenuPanel').addClass('-translate-x-full');
        setTimeout(() => {
            $('#mobileMenuOverlay').remove();
            $('body').removeClass('overflow-hidden');
        }, 300);
    });

    $(document).on('click', '#mobileMenuOverlay', function(e) {
        if (e.target === this) {
            $('#mobileMenuPanel').addClass('-translate-x-full');
            setTimeout(() => {
                $('#mobileMenuOverlay').remove();
                $('body').removeClass('overflow-hidden');
            }, 300);
        }
    });
});
</script>

<!-- Chatbot Widget -->
@include('components.chatbot')

<!-- Service Worker Registration -->
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        // First, unregister any existing service workers that might be causing issues
        navigator.serviceWorker.getRegistrations().then(function(registrations) {
            for(let registration of registrations) {
                console.log('Unregistering existing service worker:', registration.scope);
                registration.unregister();
            }
            
            // Then register our new service worker
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                })
                .catch(function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
        });
    });
}
</script>

</body>
</html>