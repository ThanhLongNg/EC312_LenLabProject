@extends('admin.layouts.app')

@section('title', 'Chi tiết khách hàng')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Chi tiết khách hàng</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Khách hàng</a></li>
                    <li class="breadcrumb-item active">{{ $customer->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Customer Info --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 80px; height: 80px; font-size: 32px; font-weight: 600;">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                    </div>
                    
                    <h5 class="mb-1">{{ $customer->name }}</h5>
                    <p class="text-muted mb-3">{{ $customer->email }}</p>
                    
                    <div class="row g-3 text-start">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">ID:</span>
                                <span class="fw-semibold">#{{ $customer->id }}</span>
                            </div>
                        </div>
                        
                        @if($customer->phone)
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Điện thoại:</span>
                                <span class="fw-semibold">{{ $customer->phone }}</span>
                            </div>
                        </div>
                        @endif
                        
                        @if($customer->gender)
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Giới tính:</span>
                                <span class="badge bg-{{ $customer->gender === 'male' ? 'info' : ($customer->gender === 'female' ? 'pink' : 'secondary') }}-subtle 
                                             text-{{ $customer->gender === 'male' ? 'info' : ($customer->gender === 'female' ? 'pink' : 'secondary') }}">
                                    {{ $customer->gender === 'male' ? 'Nam' : ($customer->gender === 'female' ? 'Nữ' : 'Khác') }}
                                </span>
                            </div>
                        </div>
                        @endif
                        
                        @if($customer->birth_date)
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Ngày sinh:</span>
                                <span class="fw-semibold">{{ $customer->birth_date->format('d/m/Y') }}</span>
                            </div>
                        </div>
                        @endif
                        
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Ngày đăng ký:</span>
                                <span class="fw-semibold">{{ $customer->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Lần cuối cập nhật:</span>
                                <span class="fw-semibold">{{ $customer->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Statistics --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title mb-0">📊 Thống kê đơn hàng</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="fs-4 fw-bold text-primary">{{ $orderStats['total_orders'] }}</div>
                                <div class="small text-muted">Tổng đơn hàng</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="fs-4 fw-bold text-success">{{ number_format($orderStats['total_spent']) }}đ</div>
                                <div class="small text-muted">Tổng chi tiêu</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="fs-4 fw-bold text-info">{{ number_format($orderStats['avg_order_value']) }}đ</div>
                                <div class="small text-muted">Giá trị TB/đơn</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="fs-4 fw-bold text-warning">
                                    @if($orderStats['last_order_date'])
                                        {{ \Carbon\Carbon::parse($orderStats['last_order_date'])->diffForHumans() }}
                                    @else
                                        Chưa có
                                    @endif
                                </div>
                                <div class="small text-muted">Đơn cuối</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Favorite Products --}}
            @if($orderStats['favorite_products']->count() > 0)
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title mb-0">❤️ Sản phẩm yêu thích</h6>
                </div>
                <div class="card-body">
                    @foreach($orderStats['favorite_products'] as $product)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="fw-semibold">{{ $product->name }}</div>
                            <span class="badge bg-primary-subtle text-primary">{{ $product->total_quantity }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Orders & Cart --}}
        <div class="col-12 col-lg-8">
            {{-- Current Cart --}}
            @if($cartItems->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title mb-0">🛒 Giỏ hàng hiện tại ({{ $cartItems->count() }} sản phẩm)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th>Giá</th>
                                    <th>Thành tiền</th>
                                    <th>Thêm vào</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($item->product && $item->product->image)
                                                    <img src="{{ asset('storage/products/' . $item->product->image) }}" 
                                                         alt="{{ $item->product->name }}"
                                                         class="rounded"
                                                         style="width: 40px; height: 40px; object-fit: cover;"
                                                         onerror="this.src='{{ asset('images/default.jpg') }}'">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-semibold">{{ $item->product->name ?? 'Sản phẩm không tồn tại' }}</div>
                                                    @if($item->variant_info && isset($item->variant_info['variant_name']))
                                                        <div class="small text-muted">{{ $item->variant_info['variant_name'] }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->price_at_time) }}đ</td>
                                        <td class="fw-bold">{{ number_format($item->price_at_time * $item->quantity) }}đ</td>
                                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3">Tổng giỏ hàng:</th>
                                    <th class="text-danger">{{ number_format($cartItems->sum(fn($item) => $item->price_at_time * $item->quantity)) }}đ</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Order History --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title mb-0">📦 Lịch sử đơn hàng ({{ $customer->orders->count() }} đơn)</h6>
                </div>
                <div class="card-body">
                    @if($customer->orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Ngày đặt</th>
                                        <th>Trạng thái</th>
                                        <th>Số sản phẩm</th>
                                        <th>Tổng tiền</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->orders->sortByDesc('created_at') as $order)
                                        <tr>
                                            <td>
                                                <span class="fw-semibold">#{{ $order->order_code ?? $order->id }}</span>
                                            </td>
                                            <td>
                                                <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                                <div class="small text-muted">{{ $order->created_at->format('H:i') }}</div>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'warning',
                                                        'processing' => 'info',
                                                        'shipped' => 'primary',
                                                        'delivered' => 'success',
                                                        'cancelled' => 'danger'
                                                    ];
                                                    $color = $statusColors[$order->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle">
                                                    {{ $order->orderItems->sum('quantity') }} items
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-success">{{ number_format($order->total_amount) }}đ</div>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.orders.show', $order->order_code ?? $order->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-shopping-bag fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0">Khách hàng chưa có đơn hàng nào.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection