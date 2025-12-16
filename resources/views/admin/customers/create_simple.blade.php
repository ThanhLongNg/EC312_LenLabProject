<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm khách hàng - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
            <div class="navbar-nav ms-auto">
                <a href="{{ route('admin.customers.index') }}" class="nav-link">Quay lại danh sách</a>
                <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Đăng xuất</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Thêm khách hàng mới</h4>
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

                        <form method="POST" action="{{ route('admin.customers.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Tên khách hàng *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu *</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="mb-3">
                                <label for="gender" class="form-label">Giới tính *</label>
                                <select class="form-control" id="gender" name="gender" required>
                                    <option value="">-- Chọn giới tính --</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                                </select>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Thêm khách hàng</button>
                                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Hủy</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>