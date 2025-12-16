@extends('admin.layout')

@section('content')

<div class="app-title">
    <ul class="app-breadcrumb breadcrumb side">
        <li class="breadcrumb-item"><b>Danh sách khách hàng</b></li>
    </ul>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="tile">
    <div class="tile-body">
        <a class="btn btn-add btn-sm" href="{{ route('admin.customers.create') }}">
            <i class="fas fa-plus"></i> Thêm khách hàng
        </a>

        <table class="table table-hover table-bordered mt-3">
            <thead>
                <tr>
                    <th>Họ và tên</th>
                    <th>Email</th>
                    <th>Giới tính</th>
                    <th>Tổng đơn hàng</th>
                    <th>Tổng tiền đã mua</th>
                    <th>Chức năng</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->email }}</td>
                    <td>
                        @if($c->gender == 'male') Nam
                        @elseif($c->gender == 'female') Nữ
                        @else Khác
                        @endif
                    </td>
                    <td>{{ $c->total_orders ?? 0 }}</td>
                    <td>{{ number_format($c->total_spent ?? 0) }} đ</td>

                    <td>
                        <form action="{{ route('admin.customers.destroy', $c->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm"
                                onclick="return confirm('Bạn có chắc muốn xóa khách hàng này?')">
                                <i class="fas fa-trash-alt"></i> Xóa
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection