@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa khách hàng')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Chỉnh sửa khách hàng</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Khách hàng</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.customers.show', $customer->id) }}">{{ $customer->name }}</a></li>
                    <li class="breadcrumb-item active">Chỉnh sửa</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title mb-0">Thông tin khách hàng</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.customers.update', $customer->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            {{-- Name --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $customer->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $customer->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phone --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', $customer->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Gender --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label">Giới tính <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                    <option value="">Chọn giới tính</option>
                                    <option value="male" {{ old('gender', $customer->gender) === 'male' ? 'selected' : '' }}>Nam</option>
                                    <option value="female" {{ old('gender', $customer->gender) === 'female' ? 'selected' : '' }}>Nữ</option>
                                    <option value="other" {{ old('gender', $customer->gender) === 'other' ? 'selected' : '' }}>Khác</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Birth Date --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label">Ngày sinh</label>
                                <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" 
                                       value="{{ old('birth_date', $customer->birth_date ? $customer->birth_date->format('Y-m-d') : '') }}">
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label">Mật khẩu mới</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                       placeholder="Để trống nếu không muốn thay đổi">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Để trống nếu không muốn thay đổi mật khẩu</div>
                            </div>

                            {{-- Registration Info (Read-only) --}}
                            <div class="col-12">
                                <hr class="my-4">
                                <h6 class="text-muted mb-3">Thông tin đăng ký</h6>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Ngày đăng ký</label>
                                <input type="text" class="form-control" 
                                       value="{{ $customer->created_at->format('d/m/Y H:i:s') }}" readonly>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Lần cuối cập nhật</label>
                                <input type="text" class="form-control" 
                                       value="{{ $customer->updated_at->format('d/m/Y H:i:s') }}" readonly>
                            </div>

                            {{-- Order Stats (Read-only) --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label">Tổng số đơn hàng</label>
                                <input type="text" class="form-control" 
                                       value="{{ $customer->orders->count() }} đơn" readonly>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Tổng chi tiêu</label>
                                <input type="text" class="form-control" 
                                       value="{{ number_format($customer->orders->sum('total_amount')) }}đ" readonly>
                            </div>

                            {{-- Submit Buttons --}}
                            <div class="col-12">
                                <hr class="my-4">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Lưu thay đổi
                                    </button>
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i> Hủy
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection