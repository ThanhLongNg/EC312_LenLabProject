

@extends('admin.layout')

@section('title', 'LENLAB - Dashboard')

@php
    // Variables for header
    $pageTitle = 'Dashboard';
    $pageHeading = 'Chào mừng trở lại!';
    $pageDescription = 'Đây là tổng quan hoạt động của cửa hàng';
    $createUrl = '#';
@endphp

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Customers Card -->
    <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 border border-border-light dark:border-border-dark shadow-sm">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <span class="material-icons-round text-white text-xl">people</span>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Khách hàng</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $customerCount }}</p>
            </div>
        </div>
    </div>

    <!-- Products Card -->
    <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 border border-border-light dark:border-border-dark shadow-sm">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                    <span class="material-icons-round text-white text-xl">inventory_2</span>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sản phẩm</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $productCount }}</p>
            </div>
        </div>
    </div>

    <!-- Orders Card -->
    <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 border border-border-light dark:border-border-dark shadow-sm">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-primary to-primary-hover rounded-lg flex items-center justify-center">
                    <span class="material-icons-round text-white text-xl">shopping_bag</span>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Đơn hàng</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $orderCount }}</p>
            </div>
        </div>
    </div>

    <!-- Pending Orders Card -->
    <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 border border-border-light dark:border-border-dark shadow-sm">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center">
                    <span class="material-icons-round text-white text-xl">schedule</span>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Chờ xử lý</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pendingOrderCount }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Recent Orders -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Order Status Chart -->
    <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 border border-border-light dark:border-border-dark shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Trạng thái đơn hàng</h3>
        <div class="relative h-64">
            <canvas id="orderStatusChart"></canvas>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 border border-border-light dark:border-border-dark shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Đơn hàng gần nhất</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-primary hover:text-primary-hover text-sm font-medium">
                Xem tất cả
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border-light dark:border-border-dark">
                        <th class="text-left py-2 text-gray-500 dark:text-gray-400 font-medium">Mã đơn</th>
                        <th class="text-left py-2 text-gray-500 dark:text-gray-400 font-medium">Khách hàng</th>
                        <th class="text-left py-2 text-gray-500 dark:text-gray-400 font-medium">Tổng tiền</th>
                        <th class="text-left py-2 text-gray-500 dark:text-gray-400 font-medium">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-light dark:divide-border-dark">
                    @forelse($recentOrders as $order)
                    <tr>
                        <td class="py-3 text-gray-900 dark:text-white font-mono">ORD-{{ $order->order_id }}</td>
                        <td class="py-3 text-gray-900 dark:text-white">{{ $order->full_name ?? 'N/A' }}</td>
                        <td class="py-3 text-gray-900 dark:text-white font-medium">{{ number_format($order->total_amount ?? 0) }}₫</td>
                        <td class="py-3">
                            @switch($order->status)
                                @case('pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                        Chờ xử lý
                                    </span>
                                    @break
                                @case('confirmed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                        Đã xác nhận
                                    </span>
                                    @break
                                @case('shipping')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                        Đang giao
                                    </span>
                                    @break
                                @case('delivered')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                        Đã giao
                                    </span>
                                    @break
                                @case('cancelled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                        Đã hủy
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300">
                                        {{ $order->status }}
                                    </span>
                            @endswitch
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <span class="material-icons-round text-4xl text-gray-400">shopping_bag</span>
                                <p class="text-gray-500 dark:text-gray-400">Không có đơn hàng nào</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-8">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Thao tác nhanh</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('admin.products.create') }}" class="bg-surface-light dark:bg-surface-dark rounded-xl p-4 border border-border-light dark:border-border-dark shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                    <span class="material-icons-round text-primary">add</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Thêm sản phẩm</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Tạo sản phẩm mới</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.orders.index') }}" class="bg-surface-light dark:bg-surface-dark rounded-xl p-4 border border-border-light dark:border-border-dark shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center group-hover:bg-blue-500/20 transition-colors">
                    <span class="material-icons-round text-blue-500">shopping_bag</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Quản lý đơn hàng</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Xem tất cả đơn hàng</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.customers.index') }}" class="bg-surface-light dark:bg-surface-dark rounded-xl p-4 border border-border-light dark:border-border-dark shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center group-hover:bg-green-500/20 transition-colors">
                    <span class="material-icons-round text-green-500">people</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Khách hàng</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Quản lý khách hàng</p>
                </div>
            </div>
        </a>

        <a href="#" class="bg-surface-light dark:bg-surface-dark rounded-xl p-4 border border-border-light dark:border-border-dark shadow-sm hover:shadow-md transition-shadow group">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-purple-500/10 rounded-lg flex items-center justify-center group-hover:bg-purple-500/20 transition-colors">
                    <span class="material-icons-round text-purple-500">analytics</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Báo cáo</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Xem thống kê</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Order Status Chart
    const ctx = document.getElementById('orderStatusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Chờ xử lý', 'Đã xác nhận', 'Đang giao', 'Đã giao', 'Đã hủy'],
            datasets: [{
                data: [{{ $pendingOrderCount }}, 0, 0, 0, 0],
                backgroundColor: [
                    '#fbbf24', // yellow-400
                    '#3b82f6', // blue-500
                    '#8b5cf6', // purple-500
                    '#10b981', // green-500
                    '#ef4444'  // red-500
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });
</script>
@endpush