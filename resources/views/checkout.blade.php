@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Checkout.css') }}">
@endpush

@section('content')

<div class="checkout-container">

    <h1 class="checkout-title">Thanh toán</h1>

    <div class="checkout-layout">
        {{-- Cột trái: giỏ hàng --}}
        <div class="checkout-left">
            <h2>Giỏ hàng</h2>
            <div id="checkout-cart-items"></div>

            <div class="summary-block">
                <div class="summary-row">
                    <span>Tạm tính</span>
                    <span id="ck-subtotal">0 VND</span>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển (ước tính)</span>
                    <span id="ck-shipping-fee">0 VND</span>
                </div>
                <div class="summary-row">
                    <span>Giảm giá (voucher)</span>
                    <span id="ck-discount">0 VND</span>
                </div>
                <div class="summary-total">
                    <span>Tổng dự kiến</span>
                    <span id="ck-total">0 VND</span>
                </div>
            </div>
        </div>

        {{-- Cột phải: thông tin khách + địa chỉ --}}
        <div class="checkout-right">
            <h2>Thông tin khách hàng</h2>

            <form id="checkout-form">
                @csrf

                <label>Họ và tên</label>
                <input type="text" name="full_name" required>

                <label>Số điện thoại</label>
                <input type="text" name="phone" required>

                <label>Email</label>
                <input type="email" name="email" required>

                <label>Tỉnh/Thành phố</label>
                <input type="text" name="province" required>

                <label>Quận/Huyện</label>
                <input type="text" name="district" required>

                <label>Địa chỉ cụ thể</label>
                <input type="text" name="specific_address" required>

                <label>Phương thức giao hàng</label>
                <select name="shipping_method" id="shipping_method">
                    <option value="delivery">Giao hàng tận nơi</option>
                    <option value="store">Nhận tại cửa hàng</option>
                </select>

                <label>Voucher (nếu có)</label>
                <input type="text" name="voucher_code" id="voucher_code" placeholder="Nhập mã giảm giá">

                <label>Ghi chú đơn hàng</label>
                <textarea name="note" rows="3"></textarea>

                <button type="submit" class="btn-place-order">Tiếp tục đặt hàng</button>
                <p id="checkout-message"></p>
            </form>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    let subtotal = 0;
    let estimatedShipping = 0;
    let discount = 0;

    function formatPrice(amount) {
        return amount.toLocaleString('vi-VN') + ' VND';
    }

    // Load giỏ hàng cho checkout
    function loadCheckoutCart() {
        $.get('/api/checkout/summary', function(res) {
            const list = res.cart_items;
            subtotal = res.subtotal;

            let html = '';
            list.forEach(item => {
                html += `
                <div class="ck-cart-item">
                    <img src="/product-img/${item.variant.image}" class="ck-cart-img">
                    <div class="ck-cart-info">
                        <h4>${item.product.name}</h4>
                        <p>Phân loại: ${item.variant.variant_name || ''}</p>
                        <p>Số lượng: ${item.quantity}</p>
                    </div>
                    <div class="ck-cart-price">
                        ${formatPrice(item.variant.price * item.quantity)}
                    </div>
                </div>
            `;
            });

            if (!list.length) {
                html = '<p>Giỏ hàng trống.</p>';
            }

            $('#checkout-cart-items').html(html);

            updateSummary();
        });
    }

    function updateSummary() {
        // shipping ước tính: giao hàng = 30k, nhận tại cửa hàng = 0
        const method = $('#shipping_method').val();
        estimatedShipping = (method === 'delivery') ? 30000 : 0;

        const total = Math.max(0, subtotal + estimatedShipping - discount);

        $('#ck-subtotal').text(formatPrice(subtotal));
        $('#ck-shipping-fee').text(formatPrice(estimatedShipping));
        $('#ck-discount').text(formatPrice(discount));
        $('#ck-total').text(formatPrice(total));
    }

    $('#shipping_method').change(updateSummary);

    // Submit form tạo order
    $('#checkout-form').on('submit', function(e) {
        e.preventDefault();
        $('#checkout-message').text('');

        const data = $(this).serialize();

        $.ajax({
            url: '/api/checkout/create',
            method: 'POST',
            data: data,
            success: function(res) {
                if (res.success) {
                    window.location.href = '/order/' + res.order_code;
                } else {
                    $('#checkout-message').text(res.message || 'Có lỗi xảy ra.');
                }
            },
            error: function(xhr) {
                $('#checkout-message').text('Lỗi khi tạo đơn hàng.');
            }
        });
    });

    $(document).ready(function() {
        loadCheckoutCart();
    });
</script>
@endpush