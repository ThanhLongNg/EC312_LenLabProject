<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sản phẩm - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
            <div class="navbar-nav ms-auto">
                <a href="{{ route('admin.products.index') }}" class="nav-link">Quay lại danh sách</a>
                <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Đăng xuất</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Sửa sản phẩm: {{ $product->name }}</h4>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Tên sản phẩm *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="{{ old('name', $product->name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Giá *</label>
                                        <input type="number" class="form-control" id="price" name="price" 
                                               value="{{ old('price', $product->price) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Số lượng *</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" 
                                               value="{{ old('quantity', $product->quantity) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="color" class="form-label">Màu sắc</label>
                                        <input type="text" class="form-control" id="color" name="color" 
                                               value="{{ old('color', $product->color) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="size" class="form-label">Kích thước</label>
                                        <input type="text" class="form-control" id="size" name="size" 
                                               value="{{ old('size', $product->size) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Danh mục *</label>
                                        <select class="form-control" id="category_id" name="category_id" required>
                                            <option value="1" {{ $product->category_id == 1 ? 'selected' : '' }}>Nguyên phụ liệu</option>
                                            <option value="2" {{ $product->category_id == 2 ? 'selected' : '' }}>Đồ trang trí</option>
                                            <option value="3" {{ $product->category_id == 3 ? 'selected' : '' }}>Thời trang len</option>
                                            <option value="4" {{ $product->category_id == 4 ? 'selected' : '' }}>Combo tự làm</option>
                                            <option value="5" {{ $product->category_id == 5 ? 'selected' : '' }}>Sách hướng dẫn</option>
                                            <option value="6" {{ $product->category_id == 6 ? 'selected' : '' }}>Thú bông len</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Trạng thái *</label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="còn hàng" {{ $product->status == 'còn hàng' ? 'selected' : '' }}>Còn hàng</option>
                                            <option value="hết hàng" {{ $product->status == 'hết hàng' ? 'selected' : '' }}>Hết hàng</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" id="new" name="new" value="1" 
                                                   {{ $product->new ? 'checked' : '' }}>
                                            <label class="form-check-label" for="new">
                                                Sản phẩm mới
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Ảnh sản phẩm</label>
                                @if($product->image)
                                    <div class="mb-2">
                                        <img src="{{ $product->image }}" width="100" height="100" style="object-fit: cover;" class="rounded">
                                        <small class="text-muted d-block">Ảnh hiện tại</small>
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <small class="text-muted">Chọn ảnh mới nếu muốn thay đổi</small>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Cập nhật sản phẩm</button>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Hủy</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>