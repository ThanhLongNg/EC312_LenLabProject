@extends('layouts.main')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/listingpage.css') }}">
@endpush

@section('content')

{{-- Banner ưu đãi --}}
<div id="saleoff" class="rounded-banner">
    <div class="box-left">
        <h1>
            <span class="slide-in">SẢN PHẨM CHẤT LƯỢNG</span>
            <span class="pulse">100%</span>
        </h1>
    </div>

    <div class="box-right" style="background-image: url('{{ asset('Uudai2.jpg') }}')"></div>
</div>

<div class="container">

    {{-- Sidebar --}}
    <aside class="sidebar">
        <h2>Sản phẩm</h2>
        <p class="product-count">0 items</p>

        <div class="filter-section">
            <h4>DANH MỤC</h4>
            <ul class="category-list">
                <li><input type="checkbox" value="Nguyên phụ liệu"> Nguyên phụ liệu</li>
                <li><input type="checkbox" value="Đồ trang trí"> Đồ trang trí</li>
                <li><input type="checkbox" value="Thời trang len"> Thời trang len</li>
                <li><input type="checkbox" value="Combo tự làm"> Combo tự làm</li>
                <li><input type="checkbox" value="Sách hướng dẫn"> Sách hướng dẫn</li>
                <li><input type="checkbox" value="Thú bông len"> Thú bông len</li>
            </ul>

            <h4>GIÁ</h4>
            <div class="price-range-buttons">
                <button data-price="low">0 - 99.999 VND</button>
                <button data-price="high">100.000 - 500.000 VND</button>
            </div>

            <input type="range" min="0" max="1000000" value="500000" id="priceRange" />
            <div class="price-labels">
                <span>0 VND</span>
                <span class="range-max">1.000.000 VND</span>
            </div>
        </div>
    </aside>

    {{-- Danh sách sản phẩm --}}
    <main class="product-list">
        <div class="sort-bar">
            <select id="sortOptions" class="sort-options">
                <option value="trending">Sắp xếp theo</option>
                <option value="price-low">Giá: Thấp đến cao</option>
                <option value="price-high">Giá: Cao đến thấp</option>
                <option value="newest">Mới nhất</option>
            </select>
        </div>

        <div class="grid" id="productGrid"></div>

        <div class="pagination">
            <button id="prev">&lt; TRƯỚC</button>
            <button class="page-btn active" data-page="1">1</button>
            <button class="page-btn" data-page="2">2</button>
            <button class="page-btn" data-page="3">3</button>
            <button id="next">SAU &gt;</button>
        </div>
    </main>

</div>

@endsection

@push('scripts')
<script>
    let products = [];
    let filteredProducts = [];
    let selectedCategories = [];
    let activePriceFilter = null;
    let currentPage = 1;
    const itemsPerPage = 9;

    // Lấy từ khóa tìm kiếm từ URL
    const keyword = new URLSearchParams(window.location.search).get("search") || "";

    // Lấy sản phẩm từ API Laravel
    function fetchProducts() {
        $("#productGrid").html("<p class='loading'>Đang tải sản phẩm...</p>");

        const url = keyword ? `/api/products?keyword=${keyword}` : `/api/products`;

        $.get(url, function(data) {
            if (data.error) {
                $("#productGrid").html("<p class='error'>Lỗi: " + data.error + "</p>");
                return;
            }
            
            products = data.products || [];
            filteredProducts = [...products];
            $(".product-count").text(products.length + " items");
            applyFiltersAndSort();
        }).fail(function(xhr, status, error) {
            $("#productGrid").html("<p class='error'>Không thể tải sản phẩm. Lỗi: " + error + "</p>");
        });
    }

    // Áp dụng lọc + sắp xếp → cập nhật giao diện
    function applyFiltersAndSort() {
        filteredProducts = [...products];

        // Lọc theo danh mục
        if (selectedCategories.length > 0) {
            filteredProducts = filteredProducts.filter(p => selectedCategories.includes(p.category));
        }

        // Lọc theo mức giá
        if (activePriceFilter === "low") {
            filteredProducts = filteredProducts.filter(p => p.price < 100000);
        } else if (activePriceFilter === "high") {
            filteredProducts = filteredProducts.filter(p => p.price >= 100000 && p.price <= 500000);
        }

        // Lọc theo thanh trượt
        const maxPrice = parseInt($("#priceRange").val());
        filteredProducts = filteredProducts.filter(p => p.price <= maxPrice);

        // Sắp xếp
        switch ($("#sortOptions").val()) {
            case "price-low":
                filteredProducts.sort((a, b) => a.price - b.price);
                break;
            case "price-high":
                filteredProducts.sort((a, b) => b.price - a.price);
                break;
            case "newest":
                filteredProducts.sort((a, b) => b.id - a.id);
                break;
        }

        updatePagination();
        displayCurrentPage();
    }

    function displayCurrentPage() {
        const start = (currentPage - 1) * itemsPerPage;
        const items = filteredProducts.slice(start, start + itemsPerPage);
        renderProducts(items);
    }

    // Hiển thị sản phẩm
    function renderProducts(list) {
        const grid = $("#productGrid");
        grid.empty();

        if (list.length === 0) {
            grid.html(`<p class="no-products">Không tìm thấy sản phẩm phù hợp</p>`);
            return;
        }

        list.forEach(product => {
            grid.append(`
            <div class="product-card">
                ${product.is_new ? '<span class="label-new">New</span>' : ''}
                <img src="/product-img/${product.image}" class="product-img" alt="${product.name}">
                <h4>${product.name}</h4>
                <p class="product-price">${product.price.toLocaleString("vi-VN")} VND</p>
                <p class="product-category">${product.category}</p>
                <a href="/san-pham/${product.id}" class="view-product">Xem sản phẩm</a>
            </div>
        `);
        });
    }

    function updatePagination() {
        const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);
        $(".page-btn").each(function(i) {
            if (i < totalPages) {
                $(this).show().toggleClass("active", currentPage == i + 1);
            } else $(this).hide();
        });

        $("#prev").prop("disabled", currentPage === 1);
        $("#next").prop("disabled", currentPage === totalPages);
    }

    // EVENTS
    $(".price-range-buttons button").click(function() {
        $(".price-range-buttons button").removeClass("active");
        $(this).addClass("active");
        activePriceFilter = $(this).data("price");
        currentPage = 1;
        applyFiltersAndSort();
    });

    $("#priceRange").on("input", function() {
        $(".range-max").text(parseInt(this.value).toLocaleString("vi-VN") + " VND");
        currentPage = 1;
        applyFiltersAndSort();
    });

    $("#sortOptions").change(function() {
        currentPage = 1;
        applyFiltersAndSort();
    });

    $(".category-list input").change(function() {
        const val = $(this).val();
        if (this.checked) selectedCategories.push(val);
        else selectedCategories = selectedCategories.filter(c => c !== val);
        currentPage = 1;
        applyFiltersAndSort();
    });

    // Nút phân trang
    $(".page-btn").click(function() {
        currentPage = parseInt($(this).data("page"));
        displayCurrentPage();
        updatePagination();
    });

    $("#prev").click(() => {
        if (currentPage > 1) {
            currentPage--;
            displayCurrentPage();
            updatePagination();
        }
    });
    $("#next").click(() => {
        currentPage++;
        displayCurrentPage();
        updatePagination();
    });

    // Load trang
    $(document).ready(() => fetchProducts());
</script>
@endpush