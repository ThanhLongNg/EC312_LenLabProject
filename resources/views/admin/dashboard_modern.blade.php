<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: none;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        .bg-primary-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .bg-success-gradient { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .bg-warning-gradient { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .bg-info-gradient { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-4">
                    <h4 class="text-white mb-4">
                        <i class="fas fa-store me-2"></i>
                        LenLab Admin
                    </h4>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                        <a class="nav-link" href="{{ route('admin.customers.index') }}">
                            <i class="fas fa-users me-2"></i> Khách hàng
                        </a>
                        <a class="nav-link" href="{{ route('admin.products.index') }}">
                            <i class="fas fa-box me-2"></i> Sản phẩm
                        </a>
                        <a class="nav-link" href="{{ route('admin.orders.index') }}">
                            <i class="fas fa-shopping-cart me-2"></i> Đơn hàng
                        </a>
                        <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                                <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                            </button>
                        </form>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-1">Chào mừng trở lại!</h2>
                        <p class="text-muted">Đây là tổng quan hoạt động của cửa hàng</p>
                    </div>
                    <div class="text-muted">
                        <i class="fas fa-calendar me-2"></i>
                        {{ now()->format('d/m/Y') }}
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-primary-gradient me-3">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0">{{ $customerCount }}</h3>
                                    <p class="text-muted mb-0">Khách hàng</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-success-gradient me-3">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0">{{ $productCount }}</h3>
                                    <p class="text-muted mb-0">Sản phẩm</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-warning-gradient me-3">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0">{{ $orderCount }}</h3>
                                    <p class="text-muted mb-0">Đơn hàng</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-info-gradient me-3">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0">{{ $pendingOrderCount }}</h3>
                                    <p class="text-muted mb-0">Chờ xử lý</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Biểu đồ trạng thái đơn hàng -->
                    <div class="col-md-6">
                        <div class="chart-container">
                            <h5 class="mb-3">Trạng thái đơn hàng</h5>
                            <canvas id="orderStatusChart" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Đơn hàng gần nhất -->
                    <div class="col-md-6">
                        <div class="chart-container">
                            <h5 class="mb-3">Đơn hàng gần nhất</h5>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Mã đơn</th>
                                            <th>Khách hàng</th>
                                            <th>Tổng tiền</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentOrders as $order)
                                        <tr>
                                            <td>ORD-{{ $order->order_id }}</td>
                                            <td>{{ $order->full_name ?? 'N/A' }}</td>
                                            <td>{{ number_format($order->total_amount ?? 0) }}đ</td>
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
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Không có đơn hàng nào</td>
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
    </div>

    <script>
        // Biểu đồ trạng thái đơn hàng
        const ctx = document.getElementById('orderStatusChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Chờ xử lý', 'Đã xác nhận', 'Đang giao', 'Đã giao', 'Đã hủy'],
                datasets: [{
                    data: [{{ $pendingOrderCount }}, 0, 0, 0, 0], // Tạm thời chỉ có pending
                    backgroundColor: [
                        '#ffc107',
                        '#17a2b8', 
                        '#007bff',
                        '#28a745',
                        '#dc3545'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>