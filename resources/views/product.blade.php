@extends('layouts.main')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endpush

@section('content')

<div class="container">
    <div class="product-detail">
        
        {{-- Hình ảnh sản phẩm --}}
        <div class="product-images">
            <div class="main-image">
                <img src="/product-img/{{ $product->image ?? 'default.jpg' }}" alt="{{ $product->name }}" id="mainImage">
            </div>
        </div>

        {{-- Thông tin sản phẩm --}}
        <div class="product-info">
            <h1 class="product-title">{{ $product->name }}</h1>
            
            <div class="product-price">
                <span class="current-price">{{ number_format($product->price) }} VND</span>
            </div>

            <div class="product-category">
                <strong>Danh mục:</strong> {{ $product->category ?? 'Chưa phân loại' }}
            </div>

            @if($product->description)
            <div class="product-description">
                <h3>Mô tả sản phẩm</h3>
                <p>{{ $product->description }}</p>
            </div>
            @endif

            {{-- Thêm vào giỏ hàng --}}
            <div class="add-to-cart-section">
                <div class="quantity-selector">
                    <label>Số lượng:</label>
                    <div class="quantity-controls">
                        <button type="button" id="decreaseQty">-</button>
                        <input type="number" id="quantity" value="1" min="1">
                        <button type="button" id="increaseQty">+</button>
                    </div>
                </div>

                <button class="add-to-cart-btn" id="addToCart">
                    <i class="fas fa-shopping-cart"></i>
                    Thêm vào giỏ hàng
                </button>
            </div>

            {{-- Thông tin bổ sung --}}
            <div class="product-features">
                <div class="feature-item">
                    <i class="fas fa-truck"></i>
                    <span>Giao hàng toàn quốc</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Bảo hành chất lượng</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-phone"></i>
                    <span>Hỗ trợ 24/7</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Sản phẩm liên quan --}}
    <section class="related-products">
        <h2>Sản phẩm liên quan</h2>
        <div class="products-grid" id="relatedProducts"></div>
    </section>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    
    // Tăng giảm số lượng
    $("#increaseQty").click(function() {
        let qty = parseInt($("#quantity").val());
        $("#quantity").val(qty + 1);
    });

    $("#decreaseQty").click(function() {
        let qty = parseInt($("#quantity").val());
        if (qty > 1) {
            $("#quantity").val(qty - 1);
        }
    });

    // Thêm vào giỏ hàng
    $("#addToCart").click(function() {
        @auth
            const quantity = parseInt($("#quantity").val());
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.post('/api/cart/add', {
                product_id: {{ $product->id }},
                quantity: quantity
            }, function(response) {
                if (response.success) {
                    alert('Đã thêm sản phẩm vào giỏ hàng!');
                } else {
                    alert('Có lỗi xảy ra, vui lòng thử lại!');
                }
            });
        @else
            alert('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!');
            window.location.href = '/login';
        @endauth
    });

    // Load sản phẩm liên quan
    function loadRelatedProducts() {
        $.get('/api/products', function(response) {
            let html = '';
            const products = response.products.slice(0, 4); // Lấy 4 sản phẩm đầu
            
            products.forEach(item => {
                html += `
                <div class="product-item">
                    <div class="product-image">
                        <img src="/product-img/${item.image}" alt="${item.name}">
                    </div>
                    <div class="product-info">
                        <h3>${item.name}</h3>
                        <p class="price">${item.price.toLocaleString('vi-VN')} VND</p>
                        <a href="/san-pham/${item.id}" class="view-product">Xem sản phẩm</a>
                    </div>
                </div>
                `;
            });
            
            $("#relatedProducts").html(html);
        });
    }

    loadRelatedProducts();
});
</script>
@endpush