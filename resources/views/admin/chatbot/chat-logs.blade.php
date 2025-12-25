@extends('admin.layouts.app')

@section('title', 'Chat Logs')

@section('content')
<div class="container-fluid py-4">

    {{-- STATS --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted mb-1">Tổng logs</div>
                    <div class="fs-3 fw-bold">
                        {{ is_iterable($logs ?? null) ? ($logs->total() ?? count($logs)) : 0 }}
                    </div>
                    <div class="small text-muted mt-1">Tổng số bản ghi theo bộ lọc hiện tại</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted mb-2">Phân bố theo intent</div>
                    @php
                        $intentStats = $stats['by_intent'] ?? $stats ?? [];
                        // Nếu controller truyền thẳng $stats = ['intentA' => 12, ...] thì vẫn render được
                        if (is_array($intentStats) && array_key_exists('total', $intentStats)) {
                            unset($intentStats['total']);
                        }
                        
                        // Đảm bảo $intentStats là array và có dữ liệu hợp lệ
                        if (!is_array($intentStats)) {
                            $intentStats = [];
                        }
                        
                        // Lọc bỏ các giá trị không hợp lệ
                        $intentStats = array_filter($intentStats, function($value, $key) {
                            return is_string($key) && (is_numeric($value) || is_string($value));
                        }, ARRAY_FILTER_USE_BOTH);
                    @endphp

                    @if(empty($intentStats))
                        <div class="text-muted small">Chưa có dữ liệu thống kê.</div>
                    @else
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($intentStats as $intent => $count)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                    {{ $intent }}: <b>{{ $count }}</b>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ url()->current() }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1">Từ khóa</label>
                    <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Tìm message / user / intent...">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label mb-1">Intent</label>
                    <input name="intent" value="{{ request('intent') }}" class="form-control" placeholder="VD: support, order...">
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label mb-1">Từ ngày</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label mb-1">Đến ngày</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button class="btn btn-primary w-100">
                        <i class="fa-solid fa-filter me-1"></i> Lọc
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary w-100">
                        Xóa
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:70px;">#</th>
                            <th style="width:160px;">Thời gian</th>
                            <th style="width:180px;">User</th>
                            <th style="width:160px;">Intent</th>
                            <th>Nội dung</th>
                            <th style="width:120px;">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs ?? [] as $i => $log)
                            @php
                                // Đảm bảo $log là object hoặc array
                                if (!is_object($log) && !is_array($log)) {
                                    continue;
                                }
                                
                                // Chuyển array thành object để dễ xử lý
                                if (is_array($log)) {
                                    $log = (object) $log;
                                }
                                
                                // các field phổ biến (tuỳ schema của bạn)
                                $id = $log->id ?? null;
                                $created = $log->created_at ?? null;

                                $userName = ($log->user->name ?? null)
                                    ?? ($log->user->email ?? null)
                                    ?? 'Guest';

                                $intent = $log->intent ?? '-';

                                $message = $log->user_message
                                    ?? $log->bot_reply
                                    ?? '';

                                $status = $log->status ?? ($log->is_success ?? null);
                                $statusText = is_bool($status) ? ($status ? 'OK' : 'Fail') : ($status ?? '—');
                                $statusClass = ($statusText === 'OK' || $statusText === 1 || $statusText === 'success')
                                    ? 'success'
                                    : (($statusText === 'Fail' || $statusText === 0 || $statusText === 'error') ? 'danger' : 'secondary');
                                    
                                // Đảm bảo tất cả biến đều là string
                                $userName = (string) $userName;
                                $intent = (string) $intent;
                                $message = (string) $message;
                                $statusText = (string) $statusText;
                            @endphp

                            <tr>
                                <td class="text-muted">
                                    {{ $id ?? ($logs->firstItem() ?? 0) + $i }}
                                </td>
                                <td>
                                    <div class="fw-semibold">
                                        {{ $created ? \Carbon\Carbon::parse($created)->format('d/m/Y') : '-' }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $created ? \Carbon\Carbon::parse($created)->format('H:i:s') : '' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $userName }}</div>
                                    @if(!empty($log->user_id))
                                        <div class="text-muted small">ID: {{ $log->user_id }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                        {{ $intent }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $messageLength = strlen($message);
                                    @endphp
                                    
                                    <div style="max-width: 520px; white-space: normal;">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($message), 220) }}
                                    </div>

                                    @if($messageLength > 220)
                                        <button class="btn btn-link p-0 mt-1 small" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#msg-{{ $id ?? $i }}">
                                            Xem thêm
                                        </button>

                                        <div class="collapse mt-2" id="msg-{{ $id ?? $i }}">
                                            <div class="bg-light rounded p-3 small" style="white-space: pre-wrap;">
                                                {{ $message }}
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Không có log nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(method_exists($logs, 'links'))
                <div class="mt-3">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
