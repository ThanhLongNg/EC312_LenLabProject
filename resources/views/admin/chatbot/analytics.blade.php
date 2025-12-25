@extends('admin.layouts.app')

@section('title', 'Chatbot Analytics')

@section('content')
<div class="container-fluid py-4">
    {{-- Date Range Filter --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ url()->current() }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label mb-1">Khoảng thời gian</label>
                    <select name="range" class="form-select" onchange="this.form.submit()">
                        <option value="24hours" {{ request('range') === '24hours' ? 'selected' : '' }}>24 giờ qua</option>
                        <option value="7days" {{ request('range', '7days') === '7days' ? 'selected' : '' }}>7 ngày qua</option>
                        <option value="30days" {{ request('range') === '30days' ? 'selected' : '' }}>30 ngày qua</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted mb-2">Tổng tin nhắn</div>
                    <div class="fs-2 fw-bold text-primary">
                        {{ $messagesOverTime->sum('count') ?? 0 }}
                    </div>
                    <div class="small text-muted">Trong khoảng thời gian đã chọn</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted mb-2">Yêu cầu tùy chỉnh</div>
                    <div class="fs-2 fw-bold text-success">
                        {{ array_sum($requestsStatus ?? []) }}
                    </div>
                    <div class="small text-muted">Tổng yêu cầu sản phẩm</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted mb-2">Thời gian phản hồi</div>
                    <div class="fs-2 fw-bold text-info">
                        {{ $avgResponseTime ?? 'N/A' }}
                    </div>
                    <div class="small text-muted">Trung bình</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-muted mb-2">Độ hài lòng</div>
                    <div class="fs-2 fw-bold text-warning">
                        {{ $satisfactionRate ?? 'N/A' }}
                    </div>
                    <div class="small text-muted">Tỷ lệ hài lòng</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Messages Over Time Chart --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title mb-0">Tin nhắn theo thời gian</h5>
                </div>
                <div class="card-body">
                    @if($messagesOverTime && $messagesOverTime->count() > 0)
                        <canvas id="messagesChart" height="100"></canvas>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-chart-line fa-3x mb-3 opacity-50"></i>
                            <p>Chưa có dữ liệu tin nhắn trong khoảng thời gian này</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Intent Distribution --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title mb-0">Phân bố Intent</h5>
                </div>
                <div class="card-body">
                    @if(!empty($intentDistribution))
                        @foreach($intentDistribution as $intent => $count)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        {{ $intent }}
                                    </span>
                                </div>
                                <div class="fw-bold">{{ $count }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-pie-chart fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">Chưa có dữ liệu intent</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Request Status --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title mb-0">Trạng thái yêu cầu</h5>
                </div>
                <div class="card-body">
                    @if(!empty($requestsStatus))
                        @foreach($requestsStatus as $status => $count)
                            @php
                                $statusColors = [
                                    'pending_admin_response' => 'warning',
                                    'admin_responded' => 'info',
                                    'in_discussion' => 'primary',
                                    'finalized' => 'success',
                                    'payment_submitted' => 'info',
                                    'paid' => 'success',
                                    'completed' => 'success',
                                    'cancelled' => 'danger'
                                ];
                                $color = $statusColors[$status] ?? 'secondary';
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle">
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </span>
                                </div>
                                <div class="fw-bold">{{ $count }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-tasks fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">Chưa có yêu cầu nào</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Popular Material Estimates --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title mb-0">Ước tính vật liệu phổ biến</h5>
                </div>
                <div class="card-body">
                    @if($popularEstimates && $popularEstimates->count() > 0)
                        @foreach($popularEstimates as $estimate)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <div class="fw-semibold">{{ $estimate->product_type }}</div>
                                    <div class="small text-muted">
                                        Giá TB: {{ number_format($estimate->avg_cost ?? 0) }}đ
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">{{ $estimate->count }}</div>
                                    <div class="small text-muted">lượt ước tính</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-calculator fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">Chưa có ước tính vật liệu</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($messagesOverTime && $messagesOverTime->count() > 0)
    // Messages over time chart
    const ctx = document.getElementById('messagesChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($messagesOverTime->pluck('date')->toArray()) !!},
                datasets: [{
                    label: 'Tin nhắn',
                    data: {!! json_encode($messagesOverTime->pluck('count')->toArray()) !!},
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }
    @endif
});
</script>
@endsection