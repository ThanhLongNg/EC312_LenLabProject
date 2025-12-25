@extends('admin.layout')

@php
    $pageTitle = 'Đơn hàng';
    $pageHeading = 'Thêm đơn hàng mới';
    $pageDescription = 'Tạo đơn hàng mới cho khách hàng.';
@endphp

@section('content')
<div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-2xl overflow-hidden">
    <div class="p-6">
        @if ($errors->any())
            <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-icons-round text-red-600 dark:text-red-400">warning</span>
                    <span class="font-medium">Có lỗi xảy ra:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.orders.store') }}" class="space-y-6">
            @csrf

            {{-- Customer Information --}}
            <div class="bg-gray-50 dark:bg-gray-900/20 rounded-xl p-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Thông tin khách hàng</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Họ tên khách hàng *</label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary" 
                               id="full_name" 
                               name="full_name" 
                               value="{{ old('full_name') }}" 
                               required>
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Số điện thoại *</label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone') }}" 
                               required>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                        <input type="email" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Trạng thái *</label>
                        <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary" 
                                id="status" 
                                name="status" 
                                required>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="shipped" {{ old('status') == 'shipped' ? 'selected' : '' }}>Đang giao</option>
                            <option value="delivered" {{ old('status') == 'delivered' ? 'selected' : '' }}>Đã giao</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Address Information --}}
            <div class="bg-gray-50 dark:bg-gray-900/20 rounded-xl p-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Thông tin giao hàng</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="province" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tỉnh/Thành</label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary" 
                               id="province" 
                               name="province" 
                               value="{{ old('province') }}">
                    </div>
                    <div>
                        <label for="district" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quận/Huyện</label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary" 
                               id="district" 
                               name="district" 
                               value="{{ old('district') }}">
                    </div>
                    <div>
                        <label for="shipping_fee" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phí ship</label>
                        <input type="number" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary" 
                               id="shipping_fee" 
                               name="shipping_fee" 
                               value="{{ old('shipping_fee', 0) }}" 
                               min="0">
                    </div>
                </div>
                <div>
                    <label for="specific_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Địa chỉ cụ thể</label>
                    <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary" 
                              id="specific_address" 
                              name="specific_address" 
                              rows="2">{{ old('specific_address') }}</textarea>
                </div>
            </div>

            {{-- Products --}}
            <div class="bg-gray-50 dark:bg-gray-900/20 rounded-xl p-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Sản phẩm</h3>
                <div id="products-container" class="space-y-3">
                    <div class="product-row bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sản phẩm</label>
                                <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary product-select" 
                                        name="products[0][id]" 
                                        onchange="updatePrice(0)">
                                    <option value="">-- Chọn sản phẩm --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                            {{ $product->name }} - {{ number_format($product->price) }}đ
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Số lượng</label>
                                <input type="number" 
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary quantity-input" 
                                       name="products[0][quantity]" 
                                       min="1" 
                                       value="1" 
                                       onchange="calculateTotal()">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Đơn giá</label>
                                <input type="number" 
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white price-input" 
                                       name="products[0][price]" 
                                       readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Thành tiền</label>
                                <input type="number" 
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white subtotal-input" 
                                       readonly>
                            </div>
                            <div>
                                <button type="button" 
                                        class="w-full px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors" 
                                        onclick="removeProduct(0)">
                                    <span class="material-icons-round text-sm">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" 
                        class="mt-3 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors flex items-center gap-2" 
                        onclick="addProduct()">
                    <span class="material-icons-round text-sm">add</span>
                    Thêm sản phẩm
                </button>
            </div>

            {{-- Payment & Total --}}
            <div class="bg-gray-50 dark:bg-gray-900/20 rounded-xl p-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Thanh toán</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phương thức thanh toán</label>
                        <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary" 
                                id="payment_method" 
                                name="payment_method">
                            <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>COD</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản</option>
                        </select>
                    </div>
                    <div>
                        <label for="discount_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Giảm giá</label>
                        <input type="number" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary" 
                               id="discount_amount" 
                               name="discount_amount" 
                               value="{{ old('discount_amount', 0) }}" 
                               min="0" 
                               onchange="calculateTotal()">
                    </div>
                    <div>
                        <label for="total_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tổng tiền *</label>
                        <input type="number" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-semibold" 
                               id="total_amount" 
                               name="total_amount" 
                               value="{{ old('total_amount') }}" 
                               required 
                               min="0" 
                               readonly>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ghi chú</label>
                <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary" 
                          id="note" 
                          name="note" 
                          rows="3">{{ old('note') }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" 
                        class="px-6 py-2 bg-primary hover:bg-primary-hover text-white rounded-lg transition-colors flex items-center gap-2">
                    <span class="material-icons-round text-sm">save</span>
                    Tạo đơn hàng
                </button>
                <a href="{{ route('admin.orders.index') }}" 
                   class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors flex items-center gap-2">
                    <span class="material-icons-round text-sm">arrow_back</span>
                    Quay lại
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
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
        newRow.className = 'product-row bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4';
        newRow.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sản phẩm</label>
                    <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary product-select" 
                            name="products[${productIndex}][id]" 
                            onchange="updatePrice(${productIndex})">
                        <option value="">-- Chọn sản phẩm --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                {{ $product->name }} - {{ number_format($product->price) }}đ
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Số lượng</label>
                    <input type="number" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary quantity-input" 
                           name="products[${productIndex}][quantity]" 
                           min="1" 
                           value="1" 
                           onchange="calculateTotal()">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Đơn giá</label>
                    <input type="number" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white price-input" 
                           name="products[${productIndex}][price]" 
                           readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Thành tiền</label>
                    <input type="number" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white subtotal-input" 
                           readonly>
                </div>
                <div>
                    <button type="button" 
                            class="w-full px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors" 
                            onclick="removeProduct(${productIndex})">
                        <span class="material-icons-round text-sm">delete</span>
                    </button>
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
@endpush
@endsection