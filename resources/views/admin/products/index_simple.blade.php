<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm - {{ config('app.name') }}</title>
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
                    <h1>Quản lý sản phẩm</h1>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                        Thêm sản phẩm mới
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
                                        <th>Ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Số lượng</th>
                                        <th>Danh mục</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $product)
                                    <tr>
                                        <td>{{ $product->id }}</td>
                                        <td>
                                            @if($product->image)
                                                <img src="{{ $product->image }}" width="50" height="50" style="object-fit: cover;" class="rounded">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    No Image
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ number_format($product->price) }} đ</td>
                                        <td>{{ $product->quantity }}</td>
                                        <td>
                                            @switch($product->category_id)
                                                @case(1) Nguyên phụ liệu @break
                                                @case(2) Đồ trang trí @break
                                                @case(3) Thời trang len @break
                                                @case(4) Combo tự làm @break
                                                @case(5) Sách hướng dẫn @break
                                                @case(6) Thú bông len @break
                                                @default Không rõ
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($product->status == 'còn hàng')
                                                <span class="badge bg-success">Còn hàng</span>
                                            @else
                                                <span class="badge bg-danger">Hết hàng</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                                                <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Không có sản phẩm nào</td>
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
</body>
</html>