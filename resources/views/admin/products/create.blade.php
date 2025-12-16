@extends('admin.layout')

@section('content')

<div class="app-title">
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Danh sách sản phẩm</a></li>
        <li class="breadcrumb-item"><b>Thêm sản phẩm</b></li>
    </ul>
</div>

<div class="tile">
    <h3 class="tile-title">Thêm sản phẩm</h3>

    <form class="row" method="POST" enctype="multipart/form-data" action="{{ route('admin.products.store') }}">
        @csrf

        <div class="form-group col-md-3">
            <label>Tên sản phẩm</label>
            <input class="form-control" type="text" name="name" required>
        </div>

        <div class="form-group col-md-3">
            <label>Giá</label>
            <input class="form-control" type="number" name="price" required>
        </div>

        <div class="form-group col-md-3">
            <label>Số lượng</label>
            <input class="form-control" type="number" name="quantity" required>
        </div>

        <div class="form-group col-md-3">
            <label>Sản phẩm mới</label>
            <input type="checkbox" name="new" value="1">
        </div>

        <div class="form-group col-md-3">
            <label>Màu sắc</label>
            <input class="form-control" type="text" name="color">
        </div>

        <div class="form-group col-md-3">
            <label>Kích thước</label>
            <input class="form-control" type="text" name="size">
        </div>

        <div class="form-group col-md-12">
            <label>Mô tả</label>
            <textarea class="form-control" name="description"></textarea>
        </div>

        <div class="form-group col-md-3">
            <label>Trạng thái</label>
            <select class="form-control" name="status">
                <option value="còn hàng">Còn hàng</option>
                <option value="hết hàng">Hết hàng</option>
            </select>
        </div>

        <div class="form-group col-md-3">
            <label>Danh mục</label>
            <select class="form-control" name="category_id">
                <option value="1">Nguyên phụ liệu</option>
                <option value="2">Đồ trang trí</option>
                <option value="3">Thời trang len</option>
                <option value="4">Combo tự làm</option>
                <option value="5">Sách hướng dẫn</option>
                <option value="6">Thú bông len</option>
            </select>
        </div>

        <div class="form-group col-md-12">
            <label>Ảnh sản phẩm</label>
            <input type="file" name="image" class="form-control" required>
        </div>

        <div class="col-md-12 mt-3">
            <button class="btn btn-save">Lưu lại</button>
        </div>

    </form>
</div>

@endsection