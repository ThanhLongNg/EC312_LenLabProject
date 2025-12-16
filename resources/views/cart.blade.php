@extends('layouts.main')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Cart.css') }}">
@endpush

@section('content')

<div class="cart-container">

    <h1 class="cart-title">Giỏ hàng của bạn</h1>

    <div id="cart-items"></div>

    {{-- VOUCHER --}}
    <div class="voucher-section">
        <h3>Nhập mã giảm giá</h3>
        <div class="voucher-input">
            <input type="text" id="voucher-code" placeholder="Nhập mã voucher">
            <button id="apply-voucher">Áp dụng</button>
        </div>
        <p id="voucher-message"></p>
    </div>

    {{-- TOTAL --}}
    <div class="cart-summary">
        <h3>Tổng kết đơn hàng</h3>

        <p>Tạm tính: <span id="subtotal">0 VND</span></p>
        <p>Giảm giá: <span id="discount">0 VND</span></p>
        <p class="total">Tổng cộng: <span id="total">0 VND</span></p>

        <button class="checkout-btn" id="checkout-btn">Tiến hành thanh toán</button>
    </div>

</div>

@endsection

@push('scripts')
<script>
    let cart = [];
    let discountAmount = 0;

    // Tải giỏ hàng từ API
    function loadCart() {
        $.get('/api/cart', function(response) {
            cart = response.cart;
            renderCart();
            updateSummary();
        });
    }

    function renderCart() {
        let html = "";

        if (cart.length === 0) {
            $("#cart-items").html("<p class='empty-cart'>Giỏ hàng trống.</p>");
            return;
        }

        cart.forEach(item => {
            html += `
            <div class="cart-item">
                <img src="/product-img/${item.product.image}" class="cart-img">

                <div class="cart-info">
                    <h3>${item.product.name}</h3>
                    <p class="price">${item.product.price.toLocaleString("vi-VN")} VND</p>

                    <div class="quantity-control">
                        <button class="qty-btn decrease" data-id="${item.id}">-</button>
                        <span class="qty">${item.quantity}</span>
                        <button class="qty-btn increase" data-id="${item.id}">+</button>
                    </div>
                </div>

                <button class="delete-btn" data-id="${item.id}"><i class="fas fa-trash"></i></button>
            </div>
        `;
        });

        $("#cart-items").html(html);
    }

    // Cập nhật tổng
    function updateSummary() {
        let subtotal = cart.reduce((sum, item) => sum + item.product.price * item.quantity, 0);
        let total = subtotal - discountAmount;

        $("#subtotal").text(subtotal.toLocaleString("vi-VN") + " VND");
        $("#discount").text(discountAmount.toLocaleString("vi-VN") + " VND");
        $("#total").text(total.toLocaleString("vi-VN") + " VND");
    }

    // Setup CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Tăng giảm số lượng
    $(document).on("click", ".increase", function() {
        let id = $(this).data("id");

        $.post('/api/cart/update', {
            id: id,
            action: 'increase'
        }, loadCart);
    });

    $(document).on("click", ".decrease", function() {
        let id = $(this).data("id");

        $.post('/api/cart/update', {
            id: id,
            action: 'decrease'
        }, loadCart);
    });

    // Xóa sản phẩm
    $(document).on("click", ".delete-btn", function() {
        let id = $(this).data("id");

        $.post('/api/cart/delete', {
            id: id
        }, loadCart);
    });

    // Áp dụng voucher
    $("#apply-voucher").click(function() {
        let code = $("#voucher-code").val();

        $.post('/api/cart/voucher', {
            code: code
        }, function(response) {
            if (response.success) {
                discountAmount = response.discount;
                $("#voucher-message").text("Áp dụng thành công!");
            } else {
                discountAmount = 0;
                $("#voucher-message").text(response.message);
            }
            updateSummary();
        });
    });

    // Thanh toán
    $("#checkout-btn").click(function() {
        window.location.href = "/checkout";
    });

    // Load trang
    loadCart();
</script>
@endpush