@extends('layouts.main')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/landingpage.css') }}">
@endpush

@section('content')

{{-- Banner --}}
<div id="banner" style="background-image: url('{{ asset('banner.png') }}')">
    <div class="box-left">
        <h2>
            <span>Len Lab</span><br>
            <span>Cửa hàng đan móc thủ công</span>
        </h2>
        <p>
            Chúng tôi cung cấp sản phẩm len chất lượng cao, mềm mại và bền vững,
            mang lại sự ấm áp và an toàn cho người sử dụng.
        </p>
        <button id="buy-now">MUA NGAY</button>
    </div>
</div>

{{-- Bộ sưu tập mới --}}
<section id="new-collection">
    <div class="container">
        <div class="section-header">
            <h2>BỘ SƯU TẬP MỚI</h2>
            <a href="/san-pham" class="view-all">XEM TẤT CẢ</a>
        </div>

        <div class="products-grid" id="products-grid"></div>
    </div>
</section>

{{-- Danh mục sản phẩm --}}
<section id="product-categories">
    <div class="container">
        <h2>DANH MỤC SẢN PHẨM</h2>

        <div class="categories-grid">
            <div class="category-item" data-keyword="Nguyên phụ liệu">
                <i class="fas fa-box-open"></i>
                <h3>Nguyên phụ liệu</h3>
            </div>

            <div class="category-item" data-keyword="Đồ trang trí">
                <i class="fas fa-ribbon"></i>
                <h3>Đồ trang trí</h3>
            </div>

            <div class="category-item" data-keyword="Combo tự làm">
                <i class="fas fa-boxes"></i>
                <h3>Combo tự làm</h3>
            </div>

            <div class="category-item" data-keyword="Thú bông">
                <i class="fas fa-paw"></i>
                <h3>Thú bông</h3>
            </div>

            <div class="category-item" data-keyword="Thời trang len">
                <i class="fas fa-tshirt"></i>
                <h3>Thời trang len</h3>
            </div>

            <div class="category-item" data-keyword="Sách hướng dẫn">
                <i class="fas fa-book"></i>
                <h3>Sách hướng dẫn</h3>
            </div>
        </div>
    </div>
</section>

{{-- Nhận xét khách hàng --}}
<section id="customer-reviews">
    <div class="container">
        <div class="section-header">
            <h2>KHÁCH HÀNG NHẬN XÉT</h2>
        </div>

        <div class="reviews-grid">

            <div class="review-item">
                <div class="review-content">
                    <p>"Len mềm, đẹp hơn ảnh. Màu sắc và chất lượng đúng như mô tả."</p>
                    <div class="review-author">
                        <div class="stars">
                            @for($i = 0; $i < 5; $i++)
                                <i class="fas fa-star"></i>
                                @endfor
                        </div>
                        <span>- Thành Quản</span>
                    </div>
                </div>
            </div>

            <div class="review-item">
                <div class="review-content">
                    <p>"Thú bông handmade đáng yêu, đóng gói cẩn thận."</p>
                    <div class="review-author">
                        <div class="stars">
                            @for($i = 0; $i < 5; $i++)
                                <i class="fas fa-star"></i>
                                @endfor
                        </div>
                        <span>- Minh Anh</span>
                    </div>
                </div>
            </div>

            <div class="review-item">
                <div class="review-content">
                    <p>"Hoa len để bàn siêu xinh, giao hàng nhanh."</p>
                    <div class="review-author">
                        <div class="stars">
                            @for($i = 0; $i < 5; $i++)
                                <i class="fas fa-star"></i>
                                @endfor
                        </div>
                        <span>- Trúc Linh</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection


@push('scripts')
<script>
    $(document).ready(function() {

        // Nút xem tất cả → chuyển đến trang sản phẩm
        $("#buy-now").click(function() {
            window.location.href = "/san-pham";
        });

        // Khi click danh mục
        $(".category-item").click(function() {
            const keyword = $(this).data("keyword");
            window.location.href = "/san-pham?search=" + keyword;
        });

        // Load sản phẩm mới
        function loadProducts() {
            $.get('/api/landing/products', function(response) {

                let html = '';

                response.products.forEach(item => {
                    html += `
                    <div class="product-item">
                        <div class="product-image">
                            <img src="/product-img/${item.image}" alt="${item.name}">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">${item.name}</h3>
                            <div class="product-actions">
                                <a href="/san-pham/${item.id}" class="view-product">XEM SẢN PHẨM</a>
                            </div>
                        </div>
                    </div>
                `;
                });

                $("#products-grid").html(html);
            });
        }

        loadProducts();

    });
</script>
@endpush