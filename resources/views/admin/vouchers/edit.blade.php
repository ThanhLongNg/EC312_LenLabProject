@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-card border border-gray-200 dark:border-gray-700 overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Chỉnh sửa mã giảm giá</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Cập nhật thông tin mã giảm giá: <span class="font-medium text-primary">{{ $voucher->code }}</span></p>
                </div>
                <a href="{{ route('admin.vouchers.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                    <span class="material-icons-round mr-2 text-sm">arrow_back</span>
                    Quay lại
                </a>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.vouchers.update', $voucher) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Mã voucher --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Mã voucher <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code', $voucher->code) }}" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary uppercase"
                           placeholder="VD: GIAM20, CHAOBANMOI">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Mã sẽ được tự động chuyển thành chữ hoa</p>
                </div>

                {{-- Loại giảm giá --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Loại giảm giá <span class="text-red-500">*</span>
                    </label>
                    <select name="type" required onchange="updateDiscountLabel()"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary">
                        <option value="fixed" {{ old('type', $voucher->type) == 'fixed' ? 'selected' : '' }}>Giảm cố định (VNĐ)</option>
                        <option value="percent" {{ old('type', $voucher->type) == 'percent' ? 'selected' : '' }}>Giảm phần trăm (%)</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Giá trị giảm --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <span id="discount-label">Giá trị giảm (VNĐ)</span> <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="discount_value" value="{{ old('discount_value', $voucher->discount_value) }}" required min="1"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary"
                           placeholder="VD: 50000 hoặc 20">
                    @error('discount_value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Đơn hàng tối thiểu --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Đơn hàng tối thiểu (VNĐ)
                    </label>
                    <input type="number" name="min_order_value" value="{{ old('min_order_value', $voucher->min_order_value) }}" min="0"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary"
                           placeholder="VD: 100000">
                    @error('min_order_value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Để trống hoặc 0 nếu không có yêu cầu tối thiểu</p>
                </div>

                {{-- Ngày bắt đầu --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Ngày bắt đầu
                    </label>
                    <input type="date" name="start_date" value="{{ old('start_date', $voucher->start_date?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Để trống nếu có hiệu lực ngay</p>
                </div>

                {{-- Ngày kết thúc --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Ngày kết thúc
                    </label>
                    <input type="date" name="end_date" value="{{ old('end_date', $voucher->end_date?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Để trống nếu không có thời hạn</p>
                </div>

                {{-- Trạng thái --}}
                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input type="checkbox" name="active" id="active" value="1" {{ old('active', $voucher->active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-primary focus:ring-primary">
                        <label for="active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Kích hoạt mã giảm giá
                        </label>
                    </div>
                </div>
            </div>

            {{-- Preview --}}
            <div class="mt-8 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Xem trước mã giảm giá</h3>
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                <span class="material-icons-round text-primary">local_offer</span>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white" id="preview-code">{{ $voucher->code }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400" id="preview-description">Mô tả voucher</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-primary" id="preview-value">Giá trị</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400" id="preview-min-order">Đơn tối thiểu</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit buttons --}}
            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.vouchers.index') }}" 
                   class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    Hủy
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition-colors font-medium">
                    Cập nhật mã giảm giá
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function updateDiscountLabel() {
    const typeSelect = document.querySelector('select[name="type"]');
    const label = document.getElementById('discount-label');
    
    if (typeSelect.value === 'percent') {
        label.textContent = 'Giá trị giảm (%)';
    } else {
        label.textContent = 'Giá trị giảm (VNĐ)';
    }
    
    updatePreview();
}

function updatePreview() {
    const code = document.querySelector('input[name="code"]').value || 'VOUCHER_CODE';
    const type = document.querySelector('select[name="type"]').value;
    const discountValue = document.querySelector('input[name="discount_value"]').value || '0';
    const minOrderValue = document.querySelector('input[name="min_order_value"]').value || '0';
    
    document.getElementById('preview-code').textContent = code.toUpperCase();
    
    let valueText = '';
    let descriptionText = '';
    
    if (type === 'percent') {
        valueText = `Giảm ${discountValue}%`;
        descriptionText = `Giảm ${discountValue}% giá trị đơn hàng`;
    } else {
        valueText = `Giảm ${parseInt(discountValue).toLocaleString('vi-VN')}đ`;
        descriptionText = `Giảm ${parseInt(discountValue).toLocaleString('vi-VN')}đ cho đơn hàng`;
    }
    
    document.getElementById('preview-value').textContent = valueText;
    document.getElementById('preview-description').textContent = descriptionText;
    
    const minOrderText = parseInt(minOrderValue) > 0 
        ? `Đơn tối thiểu ${parseInt(minOrderValue).toLocaleString('vi-VN')}đ`
        : 'Không yêu cầu tối thiểu';
    document.getElementById('preview-min-order').textContent = minOrderText;
}

// Update preview on input changes
document.addEventListener('DOMContentLoaded', function() {
    const inputs = ['input[name="code"]', 'select[name="type"]', 'input[name="discount_value"]', 'input[name="min_order_value"]'];
    
    inputs.forEach(selector => {
        const element = document.querySelector(selector);
        if (element) {
            element.addEventListener('input', updatePreview);
            element.addEventListener('change', updatePreview);
        }
    });
    
    // Initial update
    updateDiscountLabel();
    updatePreview();
});
</script>
@endpush

@endsection