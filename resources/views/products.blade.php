<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="user-name" content="{{ auth()->user()->name }}">
        <meta name="user-email" content="{{ auth()->user()->email }}">
    @endauth
    <title>Tổng quan sản phẩm - LENLAB</title>
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&family=Noto+Sans:wght@400;500;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#FAC638",
                        "background-dark": "#1a1a1a",
                        "surface-dark": "#2d2d2d",
                        "card-dark": "#333333"
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
        /* CSS Variables for Chatbot */
        :root {
            --primary: #FAC638;
            --background-dark: #1a1a1a;
        }
        
        .bg-primary {
            background-color: var(--primary) !important;
        }
        
        .text-primary {
            color: var(--primary) !important;
        }
        
        .text-background-dark {
            color: var(--background-dark) !important;
        }
        
        .hover\:bg-primary\/90:hover {
            background-color: rgba(250, 198, 56, 0.9) !important;
        }
        
        body {
            font-family: 'Spline Sans', sans-serif;
            background: #1a1a1a;
            min-height: 100vh;
        }
        
        .products-container {
            background: #1a1a1a;
            max-width: 400px;
            margin: 0 auto;
            min-height: 100vh;
        }
        
        .product-card {
            background: rgba(45, 45, 45, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(250, 198, 56, 0.15);
        }
        
        .product-image {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
        }
        
        .add-btn {
            position: absolute;
            bottom: 8px;
            right: 8px;
            width: 28px;
            height: 28px;
            background: #FAC638;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .add-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(250, 198, 56, 0.4);
        }
        
        .filter-btn {
            background: rgba(45, 45, 45, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .filter-btn.active {
            background: #FAC638;
            color: #1a1a1a;
            font-weight: 600;
        }
        
        .rating-filter-btn.bg-primary,
        .sold-filter-btn.bg-primary {
            background: #FAC638 !important;
            color: #1a1a1a !important;
            border-color: #FAC638 !important;
        }
        
        .rating-stars {
            color: #FAC638;
        }
        
        .pagination-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .pagination-dot.active {
            background: #FAC638;
            transform: scale(1.2);
        }
        
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-background-dark">
    <div class="products-container">
        <!-- Header - Fixed -->
        <header class="sticky top-0 z-50 w-full bg-background-dark/95 backdrop-blur-md border-b border-gray-700">
            <div class="px-4 py-3 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <button id="mobileMenuBtn" class="flex items-center justify-center size-10 rounded-full hover:bg-white/10 transition-colors">
                        <span class="material-symbols-outlined text-white">menu</span>
                    </button>
                    <div class="text-xl font-bold tracking-tight text-white">LENLAB</div>
                </div>
                <div class="flex-1 max-w-md mx-2">
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-gray-400 text-[20px]">search</span>
                        </span>
                        <input class="w-full py-2 pl-10 pr-4 bg-surface-dark text-white rounded-full border-none text-sm focus:ring-2 focus:ring-primary/50 placeholder:text-gray-500 transition-all" 
                               placeholder="Tìm kiếm..." 
                               type="text" 
                               id="searchInput"/>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button class="relative flex items-center justify-center size-10 rounded-full hover:bg-white/10 transition-colors" onclick="window.location.href='/cart'">
                        <span class="material-symbols-outlined text-white">shopping_bag</span>
                        <span class="absolute top-2 right-2 flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-primary" id="cartIndicator"></span>
                        </span>
                    </button>
                    @auth
                        <button class="flex items-center justify-center size-10 rounded-full hover:bg-white/10 transition-colors relative" onclick="window.location.href='/profile'">
                            <span class="material-symbols-outlined text-white">account_circle</span>
                            <span id="header-requests-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full font-medium hidden"></span>
                        </button>
                    @else
                        <button class="flex items-center justify-center size-10 rounded-full hover:bg-white/10 transition-colors" onclick="showLoginPopup()">
                            <span class="material-symbols-outlined text-white">account_circle</span>
                        </button>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Filter Section -->
        <div class="p-4 bg-background-dark">
            <!-- Category Tabs -->
            <div class="flex gap-2 mb-4 overflow-x-auto hide-scrollbar pb-2" id="categoryTabs">
                <button class="filter-btn active px-4 py-2 rounded-full text-sm whitespace-nowrap" data-category="all">
                    Tất cả
                </button>
                <!-- Categories will be loaded here -->
            </div>
            
            <!-- Advanced Filters -->
            <div class="mb-4">
                <button id="filterBtn" class="text-primary text-sm flex items-center gap-1 hover:text-primary/80 transition-colors mb-3">
                    Bộ lọc nâng cao
                    <span class="material-symbols-outlined text-sm" id="filterIcon">expand_more</span>
                </button>
                
                <div id="advancedFilters" class="hidden space-y-3 bg-surface-dark/50 rounded-xl p-4">
                    <!-- Rating Filter -->
                    <div>
                        <label class="text-white text-sm font-medium mb-2 block">Đánh giá tối thiểu</label>
                        <div class="flex gap-2">
                            <button class="rating-filter-btn px-3 py-2 rounded-lg text-sm border border-gray-600 text-white hover:border-primary transition-colors" data-rating="">
                                Tất cả
                            </button>
                            <button class="rating-filter-btn px-3 py-2 rounded-lg text-sm border border-gray-600 text-white hover:border-primary transition-colors" data-rating="4">
                                4⭐+
                            </button>
                            <button class="rating-filter-btn px-3 py-2 rounded-lg text-sm border border-gray-600 text-white hover:border-primary transition-colors" data-rating="4.5">
                                4.5⭐+
                            </button>
                        </div>
                    </div>
                    
                    <!-- Sold Filter -->
                    <div>
                        <label class="text-white text-sm font-medium mb-2 block">Số lượt mua tối thiểu</label>
                        <div class="flex gap-2">
                            <button class="sold-filter-btn px-3 py-2 rounded-lg text-sm border border-gray-600 text-white hover:border-primary transition-colors" data-sold="">
                                Tất cả
                            </button>
                            <button class="sold-filter-btn px-3 py-2 rounded-lg text-sm border border-gray-600 text-white hover:border-primary transition-colors" data-sold="10">
                                10+ lượt
                            </button>
                            <button class="sold-filter-btn px-3 py-2 rounded-lg text-sm border border-gray-600 text-white hover:border-primary transition-colors" data-sold="50">
                                50+ lượt
                            </button>
                            <button class="sold-filter-btn px-3 py-2 rounded-lg text-sm border border-gray-600 text-white hover:border-primary transition-colors" data-sold="100">
                                100+ lượt
                            </button>
                        </div>
                    </div>
                    
                    <!-- Clear Filters -->
                    <div class="pt-2">
                        <button id="clearFilters" class="text-gray-400 text-sm hover:text-white transition-colors">
                            Xóa tất cả bộ lọc
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between mb-4">
                <p class="text-white text-sm">Hiển thị <span id="productCount">0</span> sản phẩm</p>
                <button id="sortBtn" class="text-primary text-sm flex items-center gap-1 hover:text-primary/80 transition-colors">
                    Sắp xếp
                    <span class="material-symbols-outlined text-sm">expand_more</span>
                </button>
            </div>
        </div>

        <!-- Sort Dropdown -->
        <div id="sortDropdown" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50">
            <div class="flex items-end justify-center min-h-screen">
                <div class="bg-surface-dark w-full max-w-md rounded-t-3xl p-6 transform translate-y-full transition-transform duration-300" id="sortContent">
                    <div class="w-12 h-1 bg-gray-600 rounded-full mx-auto mb-6"></div>
                    
                    <h3 class="text-white text-xl font-semibold mb-6">Sắp xếp theo</h3>
                    
                    <div class="space-y-3">
                        <button class="sort-option w-full text-left p-4 bg-card-dark rounded-xl text-white hover:bg-gray-600 transition-colors" data-sort="default">
                            Mặc định
                        </button>
                        <button class="sort-option w-full text-left p-4 bg-card-dark rounded-xl text-white hover:bg-gray-600 transition-colors" data-sort="price-asc">
                            Giá: Thấp đến cao
                        </button>
                        <button class="sort-option w-full text-left p-4 bg-card-dark rounded-xl text-white hover:bg-gray-600 transition-colors" data-sort="price-desc">
                            Giá: Cao đến thấp
                        </button>
                        <button class="sort-option w-full text-left p-4 bg-card-dark rounded-xl text-white hover:bg-gray-600 transition-colors" data-sort="rating-desc">
                            Đánh giá cao nhất
                        </button>
                        <button class="sort-option w-full text-left p-4 bg-card-dark rounded-xl text-white hover:bg-gray-600 transition-colors" data-sort="sold-desc">
                            Bán chạy nhất
                        </button>
                        <button class="sort-option w-full text-left p-4 bg-card-dark rounded-xl text-white hover:bg-gray-600 transition-colors" data-sort="name-asc">
                            Tên: A-Z
                        </button>
                        <button class="sort-option w-full text-left p-4 bg-card-dark rounded-xl text-white hover:bg-gray-600 transition-colors" data-sort="name-desc">
                            Tên: Z-A
                        </button>
                    </div>
                    
                    <button onclick="hideSortDropdown()" class="w-full mt-6 py-3 text-gray-400 hover:text-white transition-colors">
                        Đóng
                    </button>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="px-4 pb-20">
            <div class="grid grid-cols-2 gap-3" id="productsGrid">
                <!-- Products will be loaded here -->
            </div>
            
            <!-- Loading -->
            <div id="loading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                <p class="text-gray-400 mt-2">Đang tải sản phẩm...</p>
            </div>
        </div>

        <!-- Pagination -->
        <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-40">
            <div class="flex items-center gap-3 bg-surface-dark/90 backdrop-blur-md px-5 py-3 rounded-full shadow-lg border border-gray-700">
                <button id="prevPage" class="text-gray-400 hover:text-primary transition-colors disabled:opacity-30" onclick="changePage(-1)">
                    <span class="material-symbols-outlined text-xl">chevron_left</span>
                </button>
                <div class="flex gap-2" id="paginationDots">
                    <div class="pagination-dot active" data-page="1"></div>
                    <div class="pagination-dot" data-page="2"></div>
                    <div class="pagination-dot" data-page="3"></div>
                </div>
                <button id="nextPage" class="text-primary hover:text-primary/80 transition-colors disabled:opacity-30" onclick="changePage(1)">
                    <span class="material-symbols-outlined text-xl">chevron_right</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu (will be injected by JavaScript) -->
    <div id="mobileMenuContainer"></div>

    <script>
        let currentPage = 1;
        let currentCategory = 'all';
        let currentSort = 'default';
        let currentMinRating = '';
        let currentMinSold = '';
        let products = [];
        let categories = [];

        // Helper function để tìm ảnh thông minh - sửa lại thứ tự
        function getSmartImageUrl(imageName, timestamp) {
    timestamp = timestamp || Date.now();

    if (!imageName || imageName === 'default.jpg') {
        return 'https://via.placeholder.com/200x120/FAC638/FFFFFF?text=SP';
    }

    // Nếu là URL đầy đủ
    if (imageName.startsWith('http://') || imageName.startsWith('https://')) {
        return `${imageName}?v=${timestamp}`;
    }

    // Nếu DB/API đã trả về dạng /storage/... hoặc storage/...
    if (imageName.startsWith('/storage/') || imageName.startsWith('storage/')) {
        const path = imageName.startsWith('/') ? imageName : `/${imageName}`;
        return `${path}?v=${timestamp}`;
    }

    // Nếu chỉ là filename
    return `/storage/products/${imageName}?v=${timestamp}`;
}

        
        // Fallback function nếu ảnh không load được
        function handleImageError(img, imageName, timestamp) {
    timestamp = timestamp || Date.now();

    // Thử lại đúng đường dẫn storage/products nếu imageName là filename
    if (imageName && !imageName.startsWith('/') && !imageName.startsWith('http')) {
        img.src = `/storage/products/${imageName}?v=${timestamp}`;
        return;
    }

    // Nếu imageName là path /storage/... mà vẫn lỗi thì dùng default
    img.src = `{{ asset('images/default.jpg') }}`;
}


        $(document).ready(function() {
            loadCategories();
            loadProducts();
            
            // Check for search parameter in URL
            const urlParams = new URLSearchParams(window.location.search);
            const searchParam = urlParams.get('search');
            if (searchParam) {
                $('#searchInput').val(searchParam);
                filterProducts(searchParam);
            }
            
            // Mobile menu button
            $('#mobileMenuBtn').click(function() {
                showMobileMenu();
            });
            
            // Search functionality
            $('#searchInput').on('input', function() {
                const searchTerm = $(this).val();
                filterProducts(searchTerm);
            });
            
            // Sort button
            $('#sortBtn').click(function() {
                showSortDropdown();
            });
            
            // Sort options
            $('.sort-option').click(function() {
                currentSort = $(this).data('sort');
                loadProducts(); // Reload with new sort
                hideSortDropdown();
            });
            
            // Advanced filters toggle
            $('#filterBtn').click(function() {
                const filters = $('#advancedFilters');
                const icon = $('#filterIcon');
                
                if (filters.hasClass('hidden')) {
                    filters.removeClass('hidden');
                    icon.text('expand_less');
                } else {
                    filters.addClass('hidden');
                    icon.text('expand_more');
                }
            });
            
            // Rating filter buttons
            $('.rating-filter-btn').click(function() {
                $('.rating-filter-btn').removeClass('bg-primary text-background-dark').addClass('text-white');
                $(this).addClass('bg-primary text-background-dark').removeClass('text-white');
                currentMinRating = $(this).data('rating');
                loadProducts();
            });
            
            // Sold filter buttons
            $('.sold-filter-btn').click(function() {
                $('.sold-filter-btn').removeClass('bg-primary text-background-dark').addClass('text-white');
                $(this).addClass('bg-primary text-background-dark').removeClass('text-white');
                currentMinSold = $(this).data('sold');
                loadProducts();
            });
            
            // Clear filters
            $('#clearFilters').click(function() {
                currentMinRating = '';
                currentMinSold = '';
                $('.rating-filter-btn, .sold-filter-btn').removeClass('bg-primary text-background-dark').addClass('text-white');
                $('.rating-filter-btn[data-rating=""], .sold-filter-btn[data-sold=""]').addClass('bg-primary text-background-dark').removeClass('text-white');
                loadProducts();
            });
        });

        function showMobileMenu() {
            const menuHtml = `
                <div class="fixed inset-0 bg-black/60 z-50 backdrop-blur-sm" id="mobileMenuOverlay">
                    <div class="fixed left-0 top-0 h-full w-80 bg-gradient-to-b from-[#2a2318] to-[#1a1610] shadow-2xl transform -translate-x-full transition-transform duration-300 ease-out" id="mobileMenuPanel">
                        <div class="flex justify-between items-center p-6 border-b border-primary/20">
                            <h3 class="text-xl font-bold text-white">Menu</h3>
                            <button id="closeMobileMenu" class="p-2 hover:bg-white/10 rounded-full transition-colors">
                                <span class="material-symbols-outlined text-white">close</span>
                            </button>
                        </div>
                        
                        <div class="p-6">
                            <nav class="space-y-2">
                                <a href="/" class="flex items-center gap-4 p-3 rounded-xl hover:bg-white/10 transition-all duration-200 group">
                                    <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">home</span>
                                    <span class="text-white font-medium">Trang chủ</span>
                                </a>
                                <a href="/san-pham" class="flex items-center gap-4 p-3 rounded-xl bg-white/10">
                                    <span class="material-symbols-outlined text-primary">shopping_bag</span>
                                    <span class="text-white font-medium">Sản phẩm</span>
                                </a>
                                <a href="/san-pham-so" class="flex items-center gap-4 p-3 rounded-xl hover:bg-white/10 transition-all duration-200 group">
                                    <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">cloud_download</span>
                                    <span class="text-white font-medium">Sản phẩm số</span>
                                </a>
                                @auth
                                <a href="/my-requests" class="flex items-center gap-4 p-3 rounded-xl hover:bg-white/10 transition-all duration-200 group">
                                    <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">assignment</span>
                                    <span class="text-white font-medium">Yêu cầu của tôi</span>
                                    <span id="mobile-requests-badge" class="bg-red-500 text-white text-xs px-2 py-1 rounded-full font-medium hidden"></span>
                                </a>
                                @endauth
                                <a href="/gioi-thieu" class="flex items-center gap-4 p-3 rounded-xl hover:bg-white/10 transition-all duration-200 group">
                                    <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">info</span>
                                    <span class="text-white font-medium">Giới thiệu</span>
                                </a>
                                <a href="{{ route('blog.index') }}" class="flex items-center gap-4 p-3 rounded-xl hover:bg-white/10 transition-all duration-200 group">
                                    <span class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">article</span>
                                    <span class="text-white font-medium">Tin tức & Blog</span>
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>
            `;
            
            $('#mobileMenuContainer').html(menuHtml);
            
            setTimeout(() => {
                $('#mobileMenuPanel').removeClass('-translate-x-full');
            }, 10);
            
            // Close menu handlers
            $('#closeMobileMenu, #mobileMenuOverlay').click(function(e) {
                if (e.target === this) {
                    closeMobileMenu();
                }
            });
        }
        
        function closeMobileMenu() {
            $('#mobileMenuPanel').addClass('-translate-x-full');
            setTimeout(() => {
                $('#mobileMenuContainer').empty();
            }, 300);
        }

        function loadCategories() {
            console.log('Loading categories...');
            $.ajax({
                url: '/api/product-categories',
                method: 'GET',
                success: function(response) {
                    console.log('Categories loaded successfully:', response);
                    categories = response.categories || [];
                    renderCategoryTabs();
                },
                error: function(xhr, status, error) {
                    console.error('Không thể tải danh mục:', xhr.responseText, status, error);
                }
            });
        }

        function renderCategoryTabs() {
            console.log('Rendering category tabs with categories:', categories);
            const tabsContainer = $('#categoryTabs');
            
            // Clear existing tabs except "Tất cả"
            tabsContainer.find('.filter-btn:not([data-category="all"])').remove();
            
            // Add category tabs
            categories.forEach(function(category) {
                const tab = `
                    <button class="filter-btn px-4 py-2 rounded-full text-sm text-white whitespace-nowrap" data-category="${category.id}">
                        ${category.name}
                    </button>
                `;
                tabsContainer.append(tab);
            });
            
            // Use event delegation for dynamically added buttons
            $('#categoryTabs').off('click').on('click', '.filter-btn', function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                currentCategory = $(this).data('category');
                if (currentCategory === 'all') {
                    currentCategory = '';
                }
                currentPage = 1;
                loadProducts();
            });
        }

        function loadProducts() {
            console.log('Loading products...');
            console.log('Current category:', currentCategory);
            $('#loading').show();
            
            const requestData = {
                page: currentPage,
                sort: currentSort
            };
            
            // Only add category if it's not empty
            if (currentCategory && currentCategory !== 'all') {
                requestData.category = currentCategory;
            }
            
            // Thêm filter rating nếu có
            if (currentMinRating) {
                requestData.min_rating = currentMinRating;
            }
            
            // Thêm filter sold nếu có
            if (currentMinSold) {
                requestData.min_sold = currentMinSold;
            }
            
            console.log('Request data:', requestData);
            
            $.ajax({
                url: '/api/products',
                method: 'GET',
                data: requestData,
                success: function(response) {
                    products = response.products || [];
                    renderProducts();
                    updateProductCount();
                    $('#loading').hide();
                },
                error: function(xhr, status, error) {
                    console.error('Error loading products:', xhr.responseText, status, error);
                    $('#loading').hide();
                    
                    // Fallback: Hiển thị sản phẩm mẫu với ảnh thật
                    products = [
                        {
                            id: 1,
                            name: 'Áo Len Cổ Lọ Handknit',
                            price: 850000,
                            category: 'Thời trang len',
                            image: 'product1.1.webp', // Sẽ tự động tìm ở cả 2 thư mục
                            updated_at: Date.now(),
                            is_new: true,
                            quantity: 10
                        },
                        {
                            id: 2,
                            name: 'Mũ Len Beanie Basic',
                            price: 320000,
                            category: 'Thời trang len',
                            image: 'product2.1.webp',
                            updated_at: Date.now(),
                            is_new: false,
                            quantity: 15
                        },
                        {
                            id: 3,
                            name: 'Tất Len Cổ Cao',
                            price: 150000,
                            category: 'Thời trang len',
                            image: 'product3.1.webp',
                            updated_at: Date.now(),
                            is_new: false,
                            quantity: 20
                        },
                        {
                            id: 4,
                            name: 'Túi Tote Crochet Hoa',
                            price: 600000,
                            category: 'Đồ trang trí',
                            image: 'product4.1.webp',
                            updated_at: Date.now(),
                            is_new: true,
                            quantity: 8
                        }
                    ];
                    
                    renderProducts();
                    updateProductCount();
                }
            });
        }

        function filterProducts(searchTerm) {
            if (!searchTerm) {
                renderProducts();
                return;
            }
            
            const filteredProducts = products.filter(product => 
                product.name.toLowerCase().includes(searchTerm.toLowerCase())
            );
            
            renderFilteredProducts(filteredProducts);
            $('#productCount').text(filteredProducts.length);
        }

        function sortProducts() {
            let sortedProducts = [...products];
            
            switch(currentSort) {
                case 'price-asc':
                    sortedProducts.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
                    break;
                case 'price-desc':
                    sortedProducts.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
                    break;
                case 'name-asc':
                    sortedProducts.sort((a, b) => a.name.localeCompare(b.name));
                    break;
                case 'name-desc':
                    sortedProducts.sort((a, b) => b.name.localeCompare(a.name));
                    break;
                default:
                    // Keep original order
                    break;
            }
            
            renderFilteredProducts(sortedProducts);
        }

        function showSortDropdown() {
            const modal = $('#sortDropdown');
            const content = $('#sortContent');
            
            modal.removeClass('hidden');
            setTimeout(() => {
                content.removeClass('translate-y-full');
            }, 10);
        }
        
        function hideSortDropdown() {
            const modal = $('#sortDropdown');
            const content = $('#sortContent');
            
            content.addClass('translate-y-full');
            setTimeout(() => {
                modal.addClass('hidden');
            }, 300);
        }

        function renderProducts() {
            renderFilteredProducts(products);
        }

        function renderFilteredProducts(productList) {
            const grid = $('#productsGrid');
            grid.empty();
            
            if (productList.length === 0) {
                grid.html('<div class="col-span-2 text-center text-gray-400 py-8">Không có sản phẩm nào</div>');
                return;
            }
            
            productList.forEach(function(product, index) {
                const productCard = createProductCard(product, index);
                grid.append(productCard);
            });
        }

        function createProductCard(product, index) {
            const colors = ['#FAC638', '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7'];
            const bgColor = colors[index % colors.length];
            
            const imageUrl = product.image && product.image !== 'default.jpg' 
                ? getSmartImageUrl(product.image, product.updated_at ? new Date(product.updated_at).getTime() : Date.now()) 
                : `https://via.placeholder.com/200x120/${bgColor.substring(1)}/FFFFFF?text=${encodeURIComponent(product.name.substring(0, 2))}`;
            
            const price = parseFloat(product.price) || 0;
            const formattedPrice = price.toLocaleString('vi-VN');
            
            // Hiển thị rating thực tế
            const rating = parseFloat(product.average_rating) || 0;
            const reviewCount = product.review_count || 0;
            const totalSold = product.total_sold || 0;
            
            // Tạo sao dựa trên rating thực tế
            let starsHtml = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= Math.floor(rating)) {
                    starsHtml += '★';
                } else if (i === Math.ceil(rating) && rating % 1 >= 0.5) {
                    starsHtml += '☆'; // Half star (có thể thay bằng icon khác)
                } else {
                    starsHtml += '☆';
                }
            }
            
            return `
                <div class="product-card rounded-xl p-3 cursor-pointer" onclick="viewProduct(${product.id})">
                    <div class="relative mb-3 responsive-image-container">
                        <img 
                            src="${imageUrl}" 
                            alt="${product.name}" 
                            class="product-image"
                            onerror="handleImageError(this, '${product.image || ''}', ${product.updated_at ? new Date(product.updated_at).getTime() : Date.now()})"
                        />
                        ${product.is_new ? '<div class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full z-10">Mới</div>' : ''}
                        ${totalSold > 0 ? `<div class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">${totalSold} đã bán</div>` : ''}
                        ${product.has_variants ? '<div class="absolute bottom-2 left-2 bg-primary text-background-dark text-xs px-2 py-1 rounded-full font-medium">Nhiều lựa chọn</div>' : ''}
                    </div>
                    
                    <div class="mb-2">
                        <h3 class="text-white font-medium text-sm mb-1 line-clamp-2">${product.name}</h3>
                        <div class="flex items-center gap-1 mb-1">
                            <div class="rating-stars flex text-xs">
                                ${starsHtml}
                            </div>
                            <span class="text-gray-400 text-xs">${rating > 0 ? rating.toFixed(1) : '0.0'}</span>
                            ${reviewCount > 0 ? `<span class="text-gray-500 text-xs">(${reviewCount})</span>` : ''}
                        </div>
                        <p class="text-primary font-bold text-sm">${formattedPrice}đ</p>
                    </div>
                    
                    <div class="add-btn" onclick="event.stopPropagation(); addToCart(${product.id}, ${product.has_variants || false}, ${JSON.stringify(product.variants || []).replace(/"/g, '&quot;')})">
                        <span class="material-symbols-outlined text-background-dark text-sm">shopping_cart</span>
                    </div>
                </div>
            `;
        }

        function updateProductCount() {
            $('#productCount').text(products.length);
        }

        function viewProduct(productId) {
            window.location.href = `/san-pham/${productId}`;
        }

        function addToCart(productId, hasVariants = false, variants = []) {
            @auth
                // If product has variants, show variant selection modal
                if (hasVariants && variants.length > 0) {
                    showVariantSelectionModal(productId, variants);
                    return;
                }
                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                const cartData = {
                    product_id: productId,
                    quantity: 1
                };
                
                $.post('/api/cart/add', cartData, function(response) {
                    if (response.success) {
                        // Show success message
                        const toast = $('<div class="fixed top-4 right-4 bg-primary text-background-dark px-4 py-2 rounded-lg font-medium z-50">✓ Đã thêm vào giỏ hàng!</div>');
                        $('body').append(toast);
                        setTimeout(() => {
                            toast.fadeOut(() => toast.remove());
                        }, 2000);
                    } else {
                        console.error('Cart add error:', response);
                        alert(response.message || 'Có lỗi xảy ra, vui lòng thử lại!');
                    }
                }).fail(function(xhr, status, error) {
                    console.error('Cart add failed:', xhr.responseText, status, error);
                    let errorMessage = 'Có lỗi xảy ra, vui lòng thử lại!';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    alert(errorMessage);
                });
            @else
                // Show login popup or redirect
                if (confirm('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng. Chuyển đến trang đăng nhập?')) {
                    window.location.href = '{{ route("login") }}';
                }
            @endauth
        }

        function showVariantSelectionModal(productId, variants) {
            let modalHtml = `
                <div id="variantModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div class="bg-surface-dark rounded-2xl p-6 max-w-md w-full border border-white/10">
                        <h3 class="text-white font-semibold text-lg mb-4">Chọn phiên bản</h3>
                        <div class="space-y-3 mb-6">
            `;
            
            variants.forEach((variant, index) => {
                const variantLabel = variant.variant_name || `${variant.color || ''} ${variant.size || ''}`.trim() || `Phiên bản ${index + 1}`;
                const priceText = variant.price ? `${parseInt(variant.price).toLocaleString()}đ` : '';
                
                modalHtml += `
                    <div class="variant-option-modal p-3 rounded-xl border border-white/20 cursor-pointer hover:border-primary transition-colors" 
                         data-variant-id="${variant.id}" data-variant-name="${variantLabel}">
                        <div class="flex justify-between items-center">
                            <span class="text-white">${variantLabel}</span>
                            ${priceText ? `<span class="text-primary font-medium">${priceText}</span>` : ''}
                        </div>
                    </div>
                `;
            });
            
            modalHtml += `
                        </div>
                        <div class="flex gap-3">
                            <button onclick="closeVariantModal()" class="flex-1 py-2 px-4 rounded-xl border border-white/20 text-white hover:bg-white/10 transition-colors">
                                Hủy
                            </button>
                            <button onclick="addToCartWithVariant(${productId})" class="flex-1 py-2 px-4 rounded-xl bg-primary text-background-dark font-medium hover:bg-primary/90 transition-colors">
                                Thêm vào giỏ
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(modalHtml);
            
            // Handle variant selection
            $('.variant-option-modal').click(function() {
                $('.variant-option-modal').removeClass('border-primary bg-primary/10');
                $(this).addClass('border-primary bg-primary/10');
            });
            
            // Select first variant by default
            $('.variant-option-modal').first().click();
        }

        function closeVariantModal() {
            $('#variantModal').remove();
        }

        function addToCartWithVariant(productId) {
            const selectedVariant = $('.variant-option-modal.border-primary');
            if (selectedVariant.length === 0) {
                alert('Vui lòng chọn phiên bản sản phẩm');
                return;
            }
            
            const variantId = selectedVariant.data('variant-id');
            const variantName = selectedVariant.data('variant-name');
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            const cartData = {
                product_id: productId,
                variant_id: variantId,
                variant_name: variantName,
                quantity: 1
            };
            
            $.post('/api/cart/add', cartData, function(response) {
                if (response.success) {
                    closeVariantModal();
                    // Show success message
                    const toast = $('<div class="fixed top-4 right-4 bg-primary text-background-dark px-4 py-2 rounded-lg font-medium z-50">✓ Đã thêm vào giỏ hàng!</div>');
                    $('body').append(toast);
                    setTimeout(() => {
                        toast.fadeOut(() => toast.remove());
                    }, 2000);
                } else {
                    console.error('Cart add error:', response);
                    alert(response.message || 'Có lỗi xảy ra, vui lòng thử lại!');
                }
            }).fail(function(xhr, status, error) {
                console.error('Cart add failed:', xhr.responseText, status, error);
                let errorMessage = 'Có lỗi xảy ra, vui lòng thử lại!';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                alert(errorMessage);
            });
        }

        function changePage(direction) {
            const totalPages = 3; // You can make this dynamic based on total products
            currentPage += direction;
            
            if (currentPage < 1) currentPage = 1;
            if (currentPage > totalPages) currentPage = totalPages;
            
            // Update pagination dots
            $('.pagination-dot').removeClass('active');
            $(`.pagination-dot[data-page="${currentPage}"]`).addClass('active');
            
            // Update button states
            $('#prevPage').toggleClass('text-gray-400', currentPage === 1);
            $('#prevPage').toggleClass('text-primary', currentPage > 1);
            $('#nextPage').toggleClass('text-gray-400', currentPage === totalPages);
            $('#nextPage').toggleClass('text-primary', currentPage < totalPages);
            
            // Reload products for new page
            loadProducts();
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Make pagination dots clickable
        $(document).on('click', '.pagination-dot', function() {
            const page = $(this).data('page');
            const diff = page - currentPage;
            changePage(diff);
        });
    </script>

    <!-- Chatbot Widget -->
    @include('components.chatbot')

    <!-- Notification Checking Script -->
    @auth
    <script>
    // Check for unread messages and update notification badges
    function checkUnreadMessages() {
        fetch('/api/my-requests/unread-count', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.unread_count > 0) {
                // Update header badge
                const headerBadge = document.getElementById('header-requests-badge');
                if (headerBadge) {
                    headerBadge.textContent = data.unread_count;
                    headerBadge.classList.remove('hidden');
                }
                
                // Update mobile menu badge
                const mobileBadge = document.getElementById('mobile-requests-badge');
                if (mobileBadge) {
                    mobileBadge.textContent = data.unread_count + ' mới';
                    mobileBadge.classList.remove('hidden');
                }
            } else {
                // Hide badges if no unread messages
                const headerBadge = document.getElementById('header-requests-badge');
                const mobileBadge = document.getElementById('mobile-requests-badge');
                if (headerBadge) headerBadge.classList.add('hidden');
                if (mobileBadge) mobileBadge.classList.add('hidden');
            }
        })
        .catch(error => {
            console.log('Error checking unread messages:', error);
        });
    }

    // Check for unread messages on page load and periodically
    $(document).ready(function() {
        checkUnreadMessages();
        // Check every 30 seconds
        setInterval(checkUnreadMessages, 30000);
    });
    </script>
    @endauth

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