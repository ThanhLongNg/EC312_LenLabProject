@extends('admin.layouts.app')

@section('title', 'Quản lý khách hàng')

@section('content')
<div class="container-fluid py-4">
    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted mb-2">Tổng khách hàng</div>
                    <div class="fs-3 fw-bold text-primary">
                        {{ number_format($stats['total_customers'] ?? 0) }}
                    </div>
                    <div class="small text-muted">Tất cả khách hàng đã đăng ký</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted mb-2">Mới tháng này</div>
                    <div class="fs-3 fw-bold text-success">
                        {{ number_format($stats['new_this_month'] ?? 0) }}
                    </div>
                    <div class="small text-muted">Khách hàng đăng ký mới</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted mb-2">Khách hàng tích cực</div>
                    <div class="fs-3 fw-bold text-info">
                        {{ number_format($stats['active_customers'] ?? 0) }}
                    </div>
                    <div class="small text-muted">Có đơn hàng trong 90 ngày</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted mb-2">Giá trị đơn TB</div>
                    <div class="fs-3 fw-bold text-warning">
                        {{ number_format($stats['avg_order_value'] ?? 0) }}đ
                    </div>
                    <div class="small text-muted">Trung bình mỗi đơn hàng</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ url()->current() }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1">Tìm kiếm</label>
                    <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Tên, email, số điện thoại...">
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label mb-1">Giới tính</label>
                    <select name="gender" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Nam</option>
                        <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Nữ</option>
                        <option value="other" {{ request('gender') === 'other' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label mb-1">Trạng thái đơn hàng</label>
                    <select name="order_status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="has_orders" {{ request('order_status') === 'has_orders' ? 'selected' : '' }}>Có đơn hàng</option>
                        <option value="no_orders" {{ request('order_status') === 'no_orders' ? 'selected' : '' }}>Chưa có đơn</option>
                        <option value="recent_orders" {{ request('order_status') === 'recent_orders' ? 'selected' : '' }}>Mua gần đây</option>
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label mb-1">Từ ngày</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label mb-1">Đến ngày</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>

                <div class="col-12 col-md-1 d-flex flex-column gap-2">
                    <button class="btn btn-primary">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Actions Bar --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex gap-2">
            <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Thêm khách hàng
            </a>
            <button type="button" class="btn btn-outline-danger" id="bulkDeleteBtn" disabled>
                <i class="fas fa-trash me-1"></i> Xóa đã chọn
            </button>
        </div>

        <div class="d-flex gap-2 align-items-center">
            <span class="text-muted small">Sắp xếp:</span>
            <select name="sort_by" class="form-select form-select-sm" style="width: auto;" onchange="updateSort(this)">
                <option value="created_at" {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}>Ngày đăng ký</option>
                <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Tên</option>
                <option value="orders_count" {{ request('sort_by') === 'orders_count' ? 'selected' : '' }}>Số đơn hàng</option>
                <option value="total_spent" {{ request('sort_by') === 'total_spent' ? 'selected' : '' }}>Tổng chi tiêu</option>
            </select>
            <select name="sort_order" class="form-select form-select-sm" style="width: auto;" onchange="updateSort(this)">
                <option value="desc" {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}>Giảm dần</option>
                <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Tăng dần</option>
            </select>
        </div>
    </div>

    {{-- Customers Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form id="bulkDeleteForm" method="POST" action="{{ route('admin.customers.bulk-delete') }}">
                @csrf
                @method('DELETE')
                
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="checkAll" class="form-check-input">
                                </th>
                                <th style="width: 200px;">Khách hàng</th>
                                <th style="width: 120px;">Giới tính</th>
                                <th style="width: 150px;">Ngày đăng ký</th>
                                <th style="width: 100px;">Đơn hàng</th>
                                <th style="width: 120px;">Tổng chi tiêu</th>
                                <th>Đơn hàng gần đây</th>
                                <th style="width: 150px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="ids[]" value="{{ $customer->id }}" class="form-check-input rowChk">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px; font-weight: 600;">
                                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $customer->name }}</div>
                                                <div class="text-muted small">{{ $customer->email }}</div>
                                                @if($customer->phone)
                                                    <div class="text-muted small">📞 {{ $customer->phone }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($customer->gender)
                                            <span class="badge bg-{{ $customer->gender === 'male' ? 'info' : ($customer->gender === 'female' ? 'pink' : 'secondary') }}-subtle 
                                                         text-{{ $customer->gender === 'male' ? 'info' : ($customer->gender === 'female' ? 'pink' : 'secondary') }} 
                                                         border border-{{ $customer->gender === 'male' ? 'info' : ($customer->gender === 'female' ? 'pink' : 'secondary') }}-subtle">
                                                {{ $customer->gender === 'male' ? 'Nam' : ($customer->gender === 'female' ? 'Nữ' : 'Khác') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $customer->created_at ? $customer->created_at->format('d/m/Y') : '-' }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $customer->created_at ? $customer->created_at->diffForHumans() : '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                            {{ $customer->orders_count ?? 0 }} đơn
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">
                                            {{ number_format($customer->orders_sum_total_amount ?? 0) }}đ
                                        </div>
                                    </td>
                                    <td>
                                        @if(isset($customer->orders) && $customer->orders && $customer->orders->count() > 0)
                                            <div class="small">
                                                @foreach($customer->orders->take(2) as $order)
                                                    <div class="mb-1">
                                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                                            #{{ $order->order_code ?? $order->id }}
                                                        </span>
                                                        <span class="text-muted">{{ number_format($order->total_amount ?? 0) }}đ</span>
                                                    </div>
                                                @endforeach
                                                @if($customer->orders->count() > 2)
                                                    <div class="text-muted small">+{{ $customer->orders->count() - 2 }} đơn khác</div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted small">Chưa có đơn hàng</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.customers.show', $customer->id) }}" 
                                               class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.customers.edit', $customer->id) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete({{ $customer->id }}, '{{ $customer->name }}')" 
                                                    title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-users fa-3x mb-3 opacity-50"></i>
                                        <p class="mb-0">Không tìm thấy khách hàng nào.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            {{-- Pagination --}}
            @if(method_exists($customers, 'links'))
                <div class="mt-3">
                    {{ $customers->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Top Spenders --}}
    @if(isset($stats['top_spenders']) && $stats['top_spenders'] && $stats['top_spenders']->count() > 0)
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-transparent border-0">
                <h5 class="card-title mb-0">🏆 Top khách hàng chi tiêu nhiều nhất</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($stats['top_spenders'] as $index => $spender)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-lg">
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px; font-weight: 600;">
                                    #{{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <div class="fw-semibold">{{ $spender->name ?? 'N/A' }}</div>
                                    <div class="text-success fw-bold">{{ number_format($spender->orders_sum_total_amount ?? 0) }}đ</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa khách hàng <strong id="customerName"></strong>?</p>
                <p class="text-danger small">Lưu ý: Không thể xóa khách hàng đã có đơn hàng.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Sort functionality
function updateSort(select) {
    const url = new URL(window.location);
    url.searchParams.set(select.name, select.value);
    window.location = url.toString();
}

// Bulk selection
document.getElementById('checkAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.rowChk');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateBulkDeleteButton();
});

document.querySelectorAll('.rowChk').forEach(cb => {
    cb.addEventListener('change', updateBulkDeleteButton);
});

function updateBulkDeleteButton() {
    const checkedBoxes = document.querySelectorAll('.rowChk:checked');
    const bulkBtn = document.getElementById('bulkDeleteBtn');
    if (bulkBtn) bulkBtn.disabled = checkedBoxes.length === 0;
}

// Bulk delete
document.getElementById('bulkDeleteBtn')?.addEventListener('click', function() {
    const checkedBoxes = document.querySelectorAll('.rowChk:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Bạn có chắc chắn muốn xóa ${checkedBoxes.length} khách hàng đã chọn?`)) {
        document.getElementById('bulkDeleteForm').submit();
    }
});

// Single delete
function confirmDelete(id, name) {
    document.getElementById('customerName').textContent = name;
    document.getElementById('deleteForm').action = `/admin/customers/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection