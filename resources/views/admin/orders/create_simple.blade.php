<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm đơn hàng - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
            <div class="navbar-nav ms-auto">
                <a href="{{ route('admin.orders.index') }}" class="nav-link">Quay lại danh sách</a>
                <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Đăng xuất</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Thêm đơn hàng mới</h4>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.orders.store') }}">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">Họ tên khách hàng *</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" 
                                               value="{{ old('full_name') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Số điện thoại *</label>
                                        <input type="text" class="form-control" id="phone" name="phone" 
                                               value="{{ old('phone') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="{{ old('email') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Trạng thái *</label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                            <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                            <option value="shipping" {{ old('status') == 'shipping' ? 'selected' : '' }}>Đang giao</option>
                                            <option value="delivered" {{ old('status') == 'delivered' ? 'selected' : '' }}>Đã giao</option>
                                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="province" class="form-label">Tỉnh/Thành</label>
                                        <input type="text" class="form-control" id="province" name="province" 
                                               value="{{ old('province') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="district" class="form-label">Quận/Huyện</label>
                                        <input type="text" class="form-control" id="district" name="district" 
                                               value="{{ old('district') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="shipping_fee" class="form-label">Phí ship</label>
                                        <input type="number" class="form-control" id="shipping_fee" name="shipping_fee" 
                                               value="{{ old('shipping_fee', 0) }}" min="0">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="specific_address" class="form-label">Địa chỉ cụ thể</label>
                                <textarea class="form-control" id="specific_address" name="specific_address" rows="2">{{ old('specific_address') }}</textarea>
                            </div>

                            <!-- Chọn sản phẩm -->
                            <div class="mb-3">
                                <label class="form-label">Chọn sản phẩm</label>
                                <div id="products-container">
                                    <div class="product-row border p-3 mb-2">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <select class="form-control product-select" name="products[0][id]" onchange="updatePrice(0)">
                                                    <option value="">-- Chọn sản phẩm --</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                            {{ $product->name }} - {{ number_format($product->price) }}đ
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control quantity-input" name="products[0][quantity]" 
                                                       placeholder="Số lượng" min="1" value="1" onchange="calculateTotal()">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control price-input" name="products[0][price]" 
                                                       placeholder="Đơn giá" readonly>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control subtotal-input" 
                                                       placeholder="Thành tiền" readonly>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeProduct(0)">Xóa</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-success btn-sm" onclick="addProduct()">Thêm sản phẩm</button>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="payment_method" class="form-label">Phương thức thanh toán</label>
                                        <select class="form-control" id="payment_method" name="payment_method">
                                            <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>COD</option>
                                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="discount_amount" class="form-label">Giảm giá</label>
                                        <input type="number" class="form-control" id="discount_amount" name="discount_amount" 
                                               value="{{ old('discount_amount', 0) }}" min="0" onchange="calculateTotal()">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="total_amount" class="form-label">Tổng tiền *</label>
                                        <input type="number" class="form-control" id="total_amount" name="total_amount" 
                                               value="{{ old('total_amount') }}" required min="0" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="note" class="form-label">Ghi chú</label>
                                <textarea class="form-control" id="note" name="note" rows="3">{{ old('note') }}</textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Tạo đơn hàng</button>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Hủy</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let productIndex = 1;

        function updatePrice(index) {
            const select = document.querySelector(`select[name="products[${index}][id]"]`);
            const priceInput = document.querySelector(`input[name="products[${index}][price]"]`);
            
            if (select.value) {
                const selectedOption = select.options[select.selectedIndex];
                const price = selectedOption.getAttribute('data-price');
                priceInput.value = price;
            } else {
                priceInput.value = '';
            }
            calculateTotal();
        }

        function calculateTotal() {
            let subtotal = 0;
            
            document.querySelectorAll('.product-row').forEach((row, index) => {
                const quantity = row.querySelector('.quantity-input').value || 0;
                const price = row.querySelector('.price-input').value || 0;
                const itemSubtotal = quantity * price;
                
                row.querySelector('.subtotal-input').value = itemSubtotal;
                subtotal += itemSubtotal;
            });

            const shippingFee = parseFloat(document.getElementById('shipping_fee').value) || 0;
            const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
            const total = subtotal + shippingFee - discount;
            
            document.getElementById('total_amount').value = Math.max(0, total);
        }

        function addProduct() {
            const container = document.getElementById('products-container');
            const newRow = document.createElement('div');
            newRow.className = 'product-row border p-3 mb-2';
            newRow.innerHTML = `
                <div class="row">
                    <div class="col-md-5">
                        <select class="form-control product-select" name="products[${productIndex}][id]" onchange="updatePrice(${productIndex})">
                            <option value="">-- Chọn sản phẩm --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                    {{ $product->name }} - {{ number_format($product->price) }}đ
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control quantity-input" name="products[${productIndex}][quantity]" 
                               placeholder="Số lượng" min="1" value="1" onchange="calculateTotal()">
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control price-input" name="products[${productIndex}][price]" 
                               placeholder="Đơn giá" readonly>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control subtotal-input" 
                               placeholder="Thành tiền" readonly>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeProduct(${productIndex})">Xóa</button>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
            productIndex++;
        }

        function removeProduct(index) {
            const row = document.querySelector(`button[onclick="removeProduct(${index})"]`).closest('.product-row');
            row.remove();
            calculateTotal();
        }

        // Tính tổng khi thay đổi phí ship hoặc giảm giá
        document.getElementById('shipping_fee').addEventListener('change', calculateTotal);
        document.getElementById('discount_amount').addEventListener('change', calculateTotal);
    </script>
</body>
</html>