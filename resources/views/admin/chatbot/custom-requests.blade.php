@extends('admin.layouts.app')

@section('title', 'Quản lý yêu cầu sản phẩm cá nhân hóa')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Yêu cầu sản phẩm cá nhân hóa</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Khách hàng</th>
                                    <th>Sản phẩm</th>
                                    <th>Kích thước</th>
                                    <th>Trạng thái</th>
                                    <th>Giá ước tính</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>
                                        @if($request->user)
                                            {{ $request->user->name }}
                                        @else
                                            @php
                                                $contactInfo = json_decode($request->contact_info, true);
                                            @endphp
                                            {{ $contactInfo['name'] ?? 'Guest' }}
                                        @endif
                                    </td>
                                    <td>{{ $request->product_type }}</td>
                                    <td>{{ $request->size }}</td>
                                    <td>
                                        <span class="badge badge-{{ $request->status === 'pending_admin_response' ? 'warning' : ($request->status === 'completed' ? 'success' : 'info') }}">
                                            {{ $request->status_text }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($request->estimated_price)
                                            {{ number_format($request->estimated_price) }}đ
                                        @else
                                            <em>Chưa báo giá</em>
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="viewRequest({{ $request->id }})">
                                            <i class="fas fa-eye"></i> Xem
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="updateRequest({{ $request->id }})">
                                            <i class="fas fa-edit"></i> Cập nhật
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $requests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for viewing/updating request -->
<div class="modal fade" id="requestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Chi tiết yêu cầu</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="requestModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewRequest(id) {
    // Implementation for viewing request details
    $('#requestModal').modal('show');
    $('#requestModalBody').html('<p>Loading...</p>');
    
    // You would load the request details via AJAX here
    setTimeout(() => {
        $('#requestModalBody').html(`
            <div class="alert alert-info">
                <strong>Chức năng xem chi tiết yêu cầu</strong><br>
                Đây là demo interface. Trong thực tế sẽ load chi tiết yêu cầu ID: ${id}
            </div>
        `);
    }, 500);
}

function updateRequest(id) {
    // Implementation for updating request
    $('#requestModal').modal('show');
    $('#requestModalBody').html(`
        <form id="updateRequestForm">
            <div class="form-group">
                <label>Trạng thái</label>
                <select class="form-control" name="status">
                    <option value="pending_admin_response">Chờ phản hồi</option>
                    <option value="admin_responded">Đã phản hồi</option>
                    <option value="confirmed">Đã xác nhận</option>
                    <option value="completed">Hoàn thành</option>
                    <option value="cancelled">Đã hủy</option>
                </select>
            </div>
            <div class="form-group">
                <label>Phản hồi admin</label>
                <textarea class="form-control" name="admin_response" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Giá ước tính (VNĐ)</label>
                <input type="number" class="form-control" name="estimated_price">
            </div>
            <div class="form-group">
                <label>% Đặt cọc</label>
                <input type="number" class="form-control" name="deposit_percentage" value="30" min="0" max="100">
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </form>
    `);
}
</script>
@endsection