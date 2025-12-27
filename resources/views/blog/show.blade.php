<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $post->title }} - LENLAB</title>
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
        body {
            font-family: 'Spline Sans', sans-serif;
            background: #1a1a1a;
            min-height: 100vh;
            color: white;
        }
        
        .blog-container {
            background: #1a1a1a;
            max-width: 400px;
            margin: 0 auto;
            min-height: 100vh;
        }
        
        .blog-header {
            background: rgba(45, 45, 45, 0.9);
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .back-btn, .share-btn {
            color: white;
            font-size: 20px;
            cursor: pointer;
        }
        
        .featured-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 16px;
            margin: 16px 0;
        }
        
        .article-content {
            padding: 0 16px;
        }
        
        .category-tag {
            background: rgba(250, 198, 56, 0.9);
            color: #1a1a1a;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 12px;
        }
        
        .article-title {
            color: white;
            font-size: 20px;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 16px;
        }
        
        .article-excerpt {
            color: #ccc;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        
        .content-section {
            margin-bottom: 24px;
        }
        
        .section-number {
            background: #FAC638;
            color: #1a1a1a;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            margin-right: 8px;
        }
        
        .section-title {
            color: white;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        
        .section-content {
            color: #ddd;
            font-size: 14px;
            line-height: 1.5;
            margin-left: 32px;
        }
        
        .content-image {
            width: 100%;
            border-radius: 12px;
            margin: 16px 0;
        }
        
        .related-products {
            background: rgba(45, 45, 45, 0.8);
            border-radius: 16px;
            padding: 20px 16px;
            margin: 24px 16px;
        }
        
        .related-title {
            color: white;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
        }
        
        .related-title .material-symbols-outlined {
            color: #FAC638;
            margin-right: 8px;
            font-size: 20px;
        }
        
        .product-item {
            display: flex;
            gap: 12px;
            padding: 16px;
            background: rgba(60, 60, 60, 0.6);
            border-radius: 16px;
            margin-bottom: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            align-items: center;
        }
        
        .product-item:last-child {
            margin-bottom: 0;
        }
        
        .product-image {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            object-fit: cover;
            flex-shrink: 0;
        }
        
        .product-info {
            flex: 1;
            min-width: 0;
        }
        
        .product-name {
            color: white;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 4px;
            line-height: 1.3;
        }
        
        .product-price {
            color: #FAC638;
            font-size: 13px;
            font-weight: 700;
        }
        
        .product-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-shrink: 0;
        }
        
        .view-btn {
            background: #FAC638;
            color: #1a1a1a;
            border: none;
            padding: 10px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .view-btn:hover {
            background: #e6a832;
            transform: translateY(-1px);
        }
        
        .add-btn {
            background: #2d5a5a;
            color: white;
            border: none;
            padding: 10px 12px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .add-btn:hover {
            background: #1e4040;
            transform: translateY(-1px);
        }
        
        /* Chatbot styles */
        .bg-primary {
            background-color: #FAC638 !important;
        }
        
        .text-primary {
            color: #FAC638 !important;
        }
        
        .text-background-dark {
            color: #1a1a1a !important;
        }
        
        .hover\:bg-primary\/90:hover {
            background-color: rgba(250, 198, 56, 0.9) !important;
        }
    </style>
</head>
<body>
    <div class="blog-container">
        <!-- Header -->
        <div class="blog-header">
            <span class="material-symbols-outlined back-btn" onclick="window.location.href='{{ route('blog.index') }}'">arrow_back</span>
            <h1 style="color: white; font-size: 16px; font-weight: 600; margin: 0;">Chi tiết bài viết</h1>
            <span class="material-symbols-outlined share-btn">share</span>
        </div>

        <!-- Featured Image -->
        @if($post->thumbnail)
        <div style="padding: 0 16px;">
            <img src="{{ asset('storage/' . $post->thumbnail) }}" 
                 alt="{{ $post->title }}" 
                 class="featured-image">
        </div>
        @endif

        <!-- Article Content -->
        <div class="article-content">
            <span class="category-tag">{{ strtoupper($post->category ?? 'BLOG') }}</span>
            
            <h1 class="article-title">{{ $post->title }}</h1>
            
            @if($post->excerpt)
            <p class="article-excerpt">{{ $post->excerpt }}</p>
            @endif
            

            <!-- Main Content -->
            <div style="margin: 24px 0;">
                {!! $contentHtml !!}
            </div>
        </div>

        <!-- Related Products -->
        @if(!empty($relatedProducts))
        <div class="related-products">
            <div class="related-title">
                <span class="material-symbols-outlined">shopping_bag</span>
                Sản phẩm được đề cập
            </div>
            
            @foreach($relatedProducts as $product)
            <div class="product-item">
                <img src="{{ $product['image'] ? asset('storage/products/' . $product['image']) : asset('placeholder.png') }}" 
                     alt="{{ $product['name'] }}" 
                     class="product-image">
                <div class="product-info">
                    <div class="product-name">{{ $product['name'] }}</div>
                    <div class="product-price">
                        {{ $product['price'] ? number_format($product['price'], 0, ',', '.') . '₫' : 'Liên hệ' }}
                    </div>
                </div>
                <div class="product-actions">
                    <button class="view-btn" onclick="window.location.href='/san-pham/{{ $product['id'] }}'">Xem</button>
                    <button class="add-btn js-add-to-cart" 
                            data-product-id="{{ $product['id'] }}">
                        +
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Chatbot Widget -->
    @include('components.chatbot')

    <script>
    (function () {
        function getCsrfToken() {
            const el = document.querySelector('meta[name="csrf-token"]');
            return el ? el.getAttribute('content') : '';
        }

        async function addToCart(productId, qty, btn) {
            btn.disabled = true;
            const oldText = btn.textContent;
            btn.textContent = 'Đang thêm...';

            try {
                const res = await fetch('/api/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ product_id: Number(productId), quantity: Number(qty || 1) })
                });

                let data = null;
                try { data = await res.json(); } catch(e) {}

                if (!res.ok) {
                    const msg = (data && (data.message || data.error)) ? (data.message || data.error) : 'Thêm vào giỏ thất bại';
                    throw new Error(msg);
                }

                alert('Đã thêm vào giỏ!');
            } catch (err) {
                alert(err.message || 'Có lỗi xảy ra');
            } finally {
                btn.disabled = false;
                btn.textContent = oldText;
            }
        }

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.js-add-to-cart');
            if (!btn) return;

            const productId = btn.dataset.productId;
            const qty = btn.dataset.qty || 1;
            addToCart(productId, qty, btn);
        });
    })();
    </script>
</body>
</html>
