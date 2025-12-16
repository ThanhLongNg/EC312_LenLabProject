<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng - {{ config('app.name') }}</title>
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
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1>Chi tiết đơn hàng #ORD-{{ $order->order_id }}</h1>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>

                <div class="row">
                    <!-- Thông tin đơn hàng -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Thông tin đơn hàng</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Mã đơn hàng:</strong></td>
                                        <td>ORD-{{ $order->order_id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ngày đặt:</strong></td>
                                        <td>
                                            @if($order->created_at)
                                                @if(is_string($order->created_at))
                                                    {{ $order->created_at }}
                                                @else
                                                    {{ $order->created_at->format('d/m/Y H:i:s') }}
                                                @endif
                                            @else
                                                Không có
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Trạng thái:</strong></td>
                                        <td>
                                            @switch($order->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Chờ xử lý</span>
                                                    @break
                                                @case('confirmed')
                                                    <span class="badge bg-info">Đã xác nhận</span>
                                                    @break
                                                @case('shipping')
                                                    <span class="badge bg-primary">Đang giao</span>
                                                    @break
                                                @case('delivered')
                                                    <span class="badge bg-success">Đã giao</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-danger">Đã hủy</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $order->status }}</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phương thức thanh toán:</strong></td>
                                        <td>{{ $order->payment_method ?? 'Không có' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phí ship:</strong></td>
                                        <td>{{ number_format($order->shipping_fee ?? 0) }} đ</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Giảm giá:</strong></td>
                                        <td>{{ number_format($order->discount_amount ?? 0) }} đ</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tổng tiền:</strong></td>
                                        <td><strong class="text-danger">{{ number_format($order->total_amount ?? 0) }} đ</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin khách hàng -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Thông tin khách hàng</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Họ tên:</strong></td>
                                        <td>{{ $order->full_name ?? ($order->user->name ?? 'Không có') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Số điện thoại:</strong></td>
                                        <td>{{ $order->phone ?? 'Không có' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $order->email ?? ($order->user->email ?? 'Không có') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tỉnh/Thành:</strong></td>
                                        <td>{{ $order->province ?? 'Không có' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Quận/Huyện:</strong></td>
                                        <td>{{ $order->district ?? 'Không có' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Địa chỉ cụ thể:</strong></td>
                                        <td>{{ $order->specific_address ?? 'Không có' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ghi chú:</strong></td>
                                        <td>{{ $order->order_note ?? 'Không có' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danh sách sản phẩm -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Danh sách sản phẩm</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th>Đơn giá</th>
                                                <th>Số lượng</th>
                                                <th>Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($order->items as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($item->product && $item->product->image)
                                                            <img src="{{ $item->product->image }}" width="50" height="50" style="object-fit: cover;" class="rounded me-3">
                                                        @endif
                                                        <div>
                                                            <strong>{{ $item->product->name ?? 'Sản phẩm đã xóa' }}</strong>
                                                            @if($item->product)
                                                                <br><small class="text-muted">ID: {{ $item->product->id }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ number_format($item->price ?? 0) }} đ</td>
                                                <td>{{ $item->quantity ?? 0 }}</td>
                                                <td><strong>{{ number_format(($item->price ?? 0) * ($item->quantity ?? 0)) }} đ</strong></td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Không có sản phẩm nào</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-info">
                                                <th colspan="3">Tổng cộng:</th>
                                                <th>{{ number_format($order->total_amount ?? 0) }} đ</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>