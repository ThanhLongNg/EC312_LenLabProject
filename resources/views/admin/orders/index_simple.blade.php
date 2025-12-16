<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
            <div class="navbar-nav ms-auto">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
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
                    <h1>Quản lý đơn hàng</h1>
                    <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
                        Thêm đơn hàng mới
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Mã đơn hàng</th>
                                        <th>Khách hàng</th>
                                        <th>Ngày đặt</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                    <tr>
                                        <td>{{ $order->order_id ?? 'N/A' }}</td>
                                        <td>
                                            <strong>ORD-{{ $order->order_id }}</strong>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $order->full_name ?? ($order->user->name ?? 'Không có') }}</strong>
                                                @if($order->phone)
                                                    <br><small class="text-muted">{{ $order->phone }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($order->created_at)
                                                @if(is_string($order->created_at))
                                                    {{ $order->created_at }}
                                                @else
                                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                                @endif
                                            @else
                                                Không có
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ number_format($order->total_amount ?? 0) }} đ</strong>
                                        </td>
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
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if($order->order_id)
                                                    <a href="/admin/orders/{{ $order->order_id }}" class="btn btn-sm btn-info">Chi tiết</a>
                                                @else
                                                    <span class="btn btn-sm btn-secondary disabled">No ID</span>
                                                @endif
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        Trạng thái
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @if($order->order_id)
                                                            <li><a class="dropdown-item" href="#" onclick="updateStatus('{{ $order->order_id }}', 'pending')">Chờ xử lý</a></li>
                                                            <li><a class="dropdown-item" href="#" onclick="updateStatus('{{ $order->order_id }}', 'confirmed')">Đã xác nhận</a></li>
                                                            <li><a class="dropdown-item" href="#" onclick="updateStatus('{{ $order->order_id }}', 'shipping')">Đang giao</a></li>
                                                            <li><a class="dropdown-item" href="#" onclick="updateStatus('{{ $order->order_id }}', 'delivered')">Đã giao</a></li>
                                                            <li><a class="dropdown-item" href="#" onclick="updateStatus('{{ $order->order_id }}', 'cancelled')">Đã hủy</a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                                @if($order->order_id)
                                                    <form method="POST" action="/admin/orders/{{ $order->order_id }}" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa đơn hàng này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Không có đơn hàng nào</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Hidden form for status update -->
    <form id="statusForm" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="status" id="statusInput">
    </form>

    <script>
        function updateStatus(orderId, status) {
            console.log('Updating status for order:', orderId, 'to:', status);
            
            if (!confirm('Bạn có chắc muốn thay đổi trạng thái đơn hàng?')) return;
            
            // Sử dụng form submit thay vì fetch
            const form = document.getElementById('statusForm');
            const statusInput = document.getElementById('statusInput');
            
            form.action = `/admin/orders/${orderId}/status`;
            statusInput.value = status;
            form.submit();
        }
    </script>
</body>
</html>