@extends('admin.layout')

@section('content')

<div class="app-title">
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Danh sách khách hàng</a></li>
        <li class="breadcrumb-item"><b>Thêm khách hàng</b></li>
    </ul>
</div>

<div class="tile">
    <h3 class="tile-title">Tạo mới khách hàng</h3>

    <form class="row" method="POST" action="{{ route('admin.customers.store') }}">
        @csrf

        <div class="form-group col-md-4">
            <label>Họ và tên</label>
            <input class="form-control" type="text" name="name" required>
        </div>

        <div class="form-group col-md-4">
            <label>Email</label>
            <input class="form-control" type="email" name="email" required>
        </div>

        <div class="form-group col-md-4">
            <label>Mật khẩu</label>
            <input class="form-control" type="password" name="password" required>
        </div>

        <div class="form-group col-md-3">
            <label>Giới tính</label>
            <select class="form-control" name="gender" required>
                <option value="male">Nam</option>
                <option value="female">Nữ</option>
                <option value="other">Khác</option>
            </select>
        </div>

        <button class="btn btn-save mt-3 ml-3">Lưu lại</button>
    </form>
</div>

@endsection