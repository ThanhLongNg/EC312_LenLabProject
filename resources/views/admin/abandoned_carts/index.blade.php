@extends('admin.layouts.app')

@section('title', 'Giỏ hàng bị lãng quên')

@section('content')
<div class="container-fluid py-4">
    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ url()->current() }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label mb-1">Tìm kiếm khách hàng</label>
                    <input name="q" value="{{ $q }}" class="form-control" placeholder="Tên, email, số điện thoại...">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label mb-1">Sắp xếp theo giá trị</label>
                    <select name="sort_value" class="form-select">
                        <option value="desc" {{ $sortValue === 'desc' ? 'selected' : '' }}>Cao đến thấp</option>
                        <option value="asc" {{ $sortValue === 'asc' ? 'selected' : '' }}>Thấp đến cao</option>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label mb-1">Sắp xếp theo thời gian</label>
                    <select name="sort_time" class="form-select">
                        <option value="desc" {{ $sortTime === 'desc' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="asc" {{ $sortTime === 'asc' ? 'selected' : '' }}>Cũ nhất</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> Lọc
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary w-100">
                        Xóa
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted mb-2">Tổng giỏ hàng bỏ quên</div>
                    <div class="fs-3 fw-bold text-warning">
                        {{ $rows->total() }}
                    </div>
                    <div class="small text-muted">Khách hàng có giỏ hàng > 24h</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted mb-2">Tổng giá trị</div>
                    <div class="fs-3 fw-bold text-danger">
                        {{ number_format(collect($rows->items())->sum('cart_total')) }}đ
                    </div>
                    <div class="small text-muted">Giá trị các giỏ hàng bỏ quên</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted mb-2">Giá trị trung bình</div>
                    <div class="fs-3 fw-bold text-info">
                        @php
                            $total = collect($rows->items())->sum('cart_total');
                            $count = $rows->total();
                            $avg = $count > 0 ? $total / $count : 0;
                        @endphp
                        {{ number_format($avg) }}đ
                    </div>
                    <div class="small text-muted">Giá trị trung bình mỗi giỏ</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions Bar --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" 
                    onclick="showDisabledFeature('Thêm giỏ hàng bị lãng quên')" 
                    title="Tính năng tạm thời bị vô hiệu hóa">
                <i class="fas fa-plus me-1"></i> Thêm mới
            </button>
            <a href="{{ url()->current() }}" class="btn btn-outline-primary">
                <i class="fas fa-sync-alt me-1"></i> Làm mới
            </a>
        </div>

        <div class="text-muted small">
            <i class="fas fa-info-circle me-1"></i>
            Hiển thị giỏ hàng không cập nhật trong 24h trở lên
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:200px;">Khách hàng</th>
                            <th style="width:120px;">Giá trị giỏ</th>
                            <th style="width:100px;">Số items</th>
                            <th style="width:150px;">Lần cuối cập nhật</th>
                            <th>Sản phẩm trong giỏ</th>
                            <th style="width:200px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            @php
                                $user = $users->get($row->user_id);
                                $items = $cartItems->get($row->user_id, collect());
                            @endphp
                            <tr>
                                <td>
                                    @if($user)
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                        @if($user->phone)
                                            <div class="text-muted small">📞 {{ $user->phone }}</div>
                                        @endif
                                    @else
                                        <span class="text-muted">User không tồn tại</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-danger">
                                        {{ number_format($row->cart_total) }}đ
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle">
                                        {{ $row->lines_count }} items
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold">
                                        {{ \Carbon\Carbon::parse($row->last_activity)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ \Carbon\Carbon::parse($row->last_activity)->format('H:i') }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ \Carbon\Carbon::parse($row->last_activity)->diffForHumans() }}
                                    </div>
                                </td>
                                <td>
                                    <div style="max-width: 300px;">
                                        @foreach($items->take(3) as $item)
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                @if($item->product && $item->product->image)
                                                    <img src="{{ asset('storage/products/' . $item->product->image) }}" 
                                                         alt="{{ $item->product->name ?? 'Product' }}"
                                                         class="rounded"
                                                         style="width: 30px; height: 30px; object-fit: cover;"
                                                         onerror="this.src='{{ asset('images/default.jpg') }}'">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                         style="width: 30px; height: 30px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div class="flex-1">
                                                    <div class="small fw-semibold">
                                                        {{ $item->product->name ?? 'Sản phẩm không tồn tại' }}
                                                    </div>
                                                    <div class="text-muted small">
                                                        SL: {{ $item->quantity }} × {{ number_format($item->price_at_time) }}đ
                                                        @if($item->variant_info && isset($item->variant_info['variant_name']))
                                                            - {{ $item->variant_info['variant_name'] }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        @if($items->count() > 3)
                                            <div class="text-muted small">
                                                ... và {{ $items->count() - 3 }} sản phẩm khác
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($user)
                                        <div class="d-flex flex-column gap-2">
                                            <form method="POST" action="{{ route('admin.abandoned-carts.send-reminder', $user) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                                    <i class="fas fa-envelope me-1"></i> Gửi nhắc nhở
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-shopping-cart fa-3x mb-3 opacity-50"></i>
                                    <p class="mb-0">Không có giỏ hàng bị lãng quên nào.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($rows->hasPages())
                <div class="mt-3">
                    {{ $rows->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Info Box --}}
    <div class="alert alert-info mt-4">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-info-circle mt-1"></i>
            <div>
                <h6 class="mb-2">Thông tin về giỏ hàng bị lãng quên</h6>
                <ul class="mb-0 small">
                    <li>Hiển thị các giỏ hàng không được cập nhật trong <strong>24 giờ</strong> trở lên</li>
                    <li><strong>Gửi nhắc nhở:</strong> Gửi email nhắc khách hàng về giỏ hàng của họ</li>
                    <li>Giá trị giỏ hàng được tính theo giá tại thời điểm thêm vào giỏ</li>
                    <li><strong>Thêm mới:</strong> <span class="text-muted">Tính năng tạm thời bị vô hiệu hóa</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Function to show disabled feature notification for "Add New" button only
function showDisabledFeature(featureName) {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white bg-warning border-0 position-fixed';
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-ban me-2"></i>
                <strong>${featureName}</strong> tạm thời bị vô hiệu hóa
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Initialize and show toast
    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 3000
    });
    bsToast.show();
    
    // Remove toast element after it's hidden
    toast.addEventListener('hidden.bs.toast', function() {
        document.body.removeChild(toast);
    });
    
    // Also show browser alert as fallback
    setTimeout(() => {
        if (document.body.contains(toast)) {
            alert(`🚫 ${featureName} tạm thời bị vô hiệu hóa`);
        }
    }, 100);
}
</script>
@endsection