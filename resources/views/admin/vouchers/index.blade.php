@extends('admin.layout')

{{-- Ghi đè Header mặc định để thêm Ô tìm kiếm & Bộ lọc --}}
@section('header')
<div class="bg-surface-light dark:bg-surface-dark border-b border-gray-200 dark:border-gray-700 px-4 py-4 sm:px-8 sticky top-0 z-20">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Quản lý mã giảm giá</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tạo và quản lý các mã giảm giá cho khách hàng.</p>
        </div>
        
        {{-- Công cụ tìm kiếm & Filter --}}
        <div class="flex items-center gap-2">
            <form method="GET" class="flex items-center gap-2">
                <div class="relative w-full sm:w-auto">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <span class="material-icons-round text-lg">search</span>
                    </span>
                    <input name="search" value="{{ $currentSearch }}" 
                           class="w-full sm:w-64 pl-9 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 focus:ring-primary focus:border-primary dark:text-white" 
                           placeholder="Tìm mã voucher..." type="text"/>
                </div>
                <div class="relative">
                    <select name="type" onchange="this.form.submit()" class="appearance-none bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 px-4 py-2 pr-8 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium transition-colors">
                        <option value="all">Tất cả loại</option>
                        <option value="fixed" {{ $currentType == 'fixed' ? 'selected' : '' }}>Giảm cố định</option>
                        <option value="percent" {{ $currentType == 'percent' ? 'selected' : '' }}>Giảm phần trăm</option>
                    </select>
                    <span class="material-icons-round absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">expand_more</span>
                </div>
                <input type="hidden" name="status" value="{{ $currentStatus }}">
            </form>
            
            {{-- Nút tạo mới --}}
            <a href="{{ route('admin.vouchers.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition-colors font-medium">
                <span class="material-icons-round mr-2 text-sm">add</span>
                Tạo mới
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')

{{-- Tabs Trạng thái --}}
<div class="mb-6 overflow-x-auto pb-2">
    <div class="flex gap-2">
        <a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}" 
           class="whitespace-nowrap px-4 py-1.5 rounded-full text-sm font-medium shadow-sm transition-colors {{ $currentStatus == 'all' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-primary hover:text-primary' }}">
            Tất cả ({{ $counts['all'] }})
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" 
           class="whitespace-nowrap px-4 py-1.5 rounded-full text-sm font-medium transition-colors {{ $currentStatus == 'active' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-primary hover:text-primary' }}">
            Đang hoạt động ({{ $counts['active'] }})
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}" 
           class="whitespace-nowrap px-4 py-1.5 rounded-full text-sm font-medium transition-colors {{ $currentStatus == 'inactive' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-primary hover:text-primary' }}">
            Tạm dừng ({{ $counts['inactive'] }})
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'expired']) }}" 
           class="whitespace-nowrap px-4 py-1.5 rounded-full text-sm font-medium transition-colors {{ $currentStatus == 'expired' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-primary hover:text-primary' }}">
            Đã hết hạn ({{ $counts['expired'] }})
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'upcoming']) }}" 
           class="whitespace-nowrap px-4 py-1.5 rounded-full text-sm font-medium transition-colors {{ $currentStatus == 'upcoming' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-primary hover:text-primary' }}">
            Sắp diễn ra ({{ $counts['upcoming'] }})
        </a>
    </div>
</div>

{{-- Danh sách voucher --}}
<div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-card border border-gray-200 dark:border-gray-700 overflow-hidden">
    @if($vouchers->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-primary focus:ring-primary">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mã voucher</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Loại</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Giá trị</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Đơn tối thiểu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Thời gian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($vouchers as $voucher)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors" data-voucher-id="{{ $voucher->id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="voucher-checkbox rounded border-gray-300 text-primary focus:ring-primary" value="{{ $voucher->id }}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <span class="material-icons-round text-primary">local_offer</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $voucher->code }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $voucher->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $voucher->type == 'fixed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                {{ $voucher->type == 'fixed' ? 'Cố định' : 'Phần trăm' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                            {{ $voucher->type == 'fixed' ? number_format($voucher->discount_value) . 'đ' : $voucher->discount_value . '%' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $voucher->min_order_value ? number_format($voucher->min_order_value) . 'đ' : 'Không' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @if($voucher->start_date && $voucher->end_date)
                                <div>{{ $voucher->start_date->format('d/m/Y') }}</div>
                                <div>{{ $voucher->end_date->format('d/m/Y') }}</div>
                            @elseif($voucher->start_date)
                                <div>Từ {{ $voucher->start_date->format('d/m/Y') }}</div>
                            @elseif($voucher->end_date)
                                <div>Đến {{ $voucher->end_date->format('d/m/Y') }}</div>
                            @else
                                <div>Không giới hạn</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $now = \Carbon\Carbon::now();
                                $isExpired = $voucher->end_date && $voucher->end_date < $now;
                                $isUpcoming = $voucher->start_date && $voucher->start_date > $now;
                                $isActive = $voucher->active && !$isExpired && !$isUpcoming;
                            @endphp
                            
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($isExpired)
                                    bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @elseif($isUpcoming)
                                    bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($isActive)
                                    bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @else
                                    bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                @endif
                            ">
                                @if($isExpired)
                                    Đã hết hạn
                                @elseif($isUpcoming)
                                    Sắp diễn ra
                                @elseif($isActive)
                                    Đang hoạt động
                                @else
                                    Tạm dừng
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Toggle Active --}}
                                <button onclick="toggleVoucherActive({{ $voucher->id }})" 
                                        class="text-gray-400 hover:text-primary transition-colors" 
                                        title="{{ $voucher->active ? 'Tắt voucher' : 'Kích hoạt voucher' }}">
                                    <span class="material-icons-round text-lg">{{ $voucher->active ? 'toggle_on' : 'toggle_off' }}</span>
                                </button>
                                
                                {{-- Edit --}}
                                <a href="{{ route('admin.vouchers.edit', $voucher) }}" 
                                   class="text-gray-400 hover:text-blue-600 transition-colors" 
                                   title="Chỉnh sửa">
                                    <span class="material-icons-round text-lg">edit</span>
                                </a>
                                
                                {{-- Delete --}}
                                <button onclick="deleteVoucher({{ $voucher->id }})" 
                                        class="text-gray-400 hover:text-red-600 transition-colors" 
                                        title="Xóa">
                                    <span class="material-icons-round text-lg">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- Bulk Actions --}}
        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        <span id="selected-count">0</span> mục được chọn
                    </span>
                    <div class="flex items-center gap-2" id="bulk-actions" style="display: none;">
                        <button onclick="bulkAction('activate')" class="text-sm text-green-600 hover:text-green-800 font-medium">Kích hoạt</button>
                        <button onclick="bulkAction('deactivate')" class="text-sm text-yellow-600 hover:text-yellow-800 font-medium">Tắt</button>
                        <button onclick="bulkAction('delete')" class="text-sm text-red-600 hover:text-red-800 font-medium">Xóa</button>
                    </div>
                </div>
                
                {{-- Pagination info --}}
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Hiển thị {{ $vouchers->firstItem() ?? 0 }} - {{ $vouchers->lastItem() ?? 0 }} trong {{ $vouchers->total() }} kết quả
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <span class="material-icons-round text-6xl text-gray-400 mb-4">local_offer</span>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Chưa có mã giảm giá nào</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Tạo mã giảm giá đầu tiên để thu hút khách hàng</p>
            <a href="{{ route('admin.vouchers.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition-colors">
                <span class="material-icons-round mr-2">add</span>
                Tạo mã giảm giá
            </a>
        </div>
    @endif
</div>

{{-- Pagination --}}
@if($vouchers->hasPages())
<div class="flex items-center justify-center mt-6">
    {{ $vouchers->appends(request()->query())->links() }}
</div>
@endif

@push('scripts')
<script>
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Select all functionality
document.getElementById('select-all')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.voucher-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkActions();
});

// Individual checkbox functionality
document.querySelectorAll('.voucher-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActions);
});

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.voucher-checkbox:checked');
    const count = checkboxes.length;
    
    document.getElementById('selected-count').textContent = count;
    document.getElementById('bulk-actions').style.display = count > 0 ? 'flex' : 'none';
    
    // Update select all checkbox
    const selectAll = document.getElementById('select-all');
    const allCheckboxes = document.querySelectorAll('.voucher-checkbox');
    if (selectAll) {
        selectAll.checked = count === allCheckboxes.length;
        selectAll.indeterminate = count > 0 && count < allCheckboxes.length;
    }
}

// Toggle voucher active status
function toggleVoucherActive(voucherId) {
    fetch(`/admin/vouchers/${voucherId}/toggle-active`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            location.reload();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Có lỗi xảy ra', 'error');
    });
}

// Delete voucher
function deleteVoucher(voucherId) {
    if (confirm('Bạn có chắc muốn xóa mã giảm giá này?')) {
        fetch(`/admin/vouchers/${voucherId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                location.reload();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Có lỗi xảy ra', 'error');
        });
    }
}

// Bulk actions
function bulkAction(action) {
    const checkboxes = document.querySelectorAll('.voucher-checkbox:checked');
    const voucherIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (voucherIds.length === 0) {
        showNotification('Vui lòng chọn ít nhất một mã giảm giá', 'error');
        return;
    }
    
    let confirmMessage = '';
    switch (action) {
        case 'activate':
            confirmMessage = `Bạn có chắc muốn kích hoạt ${voucherIds.length} mã giảm giá?`;
            break;
        case 'deactivate':
            confirmMessage = `Bạn có chắc muốn tắt ${voucherIds.length} mã giảm giá?`;
            break;
        case 'delete':
            confirmMessage = `Bạn có chắc muốn xóa ${voucherIds.length} mã giảm giá?`;
            break;
    }
    
    if (confirm(confirmMessage)) {
        fetch('/admin/vouchers/bulk-action', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                action: action,
                voucher_ids: voucherIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                location.reload();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Có lỗi xảy ra', 'error');
        });
    }
}

// Notification
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center gap-3">
            <span class="material-icons-round">${type === 'success' ? 'check_circle' : 'error'}</span>
            <span class="flex-1">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="hover:opacity-70">
                <span class="material-icons-round text-sm">close</span>
            </button>
        </div>
    `;

    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 5000);
}
</script>
@endpush

@endsection