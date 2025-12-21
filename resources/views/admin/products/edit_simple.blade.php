@extends('admin.layout')

@section('title', 'Sửa sản phẩm')

@section('content')
<div class="p-6 max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4 mb-6">
        <div class="flex items-start gap-3">
            <a href="{{ route('admin.products.index') }}"
               class="p-2 -ml-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <span class="material-icons-round">arrow_back</span>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Sửa sản phẩm</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Cập nhật thông tin sản phẩm trong kho hàng.
                </p>
            </div>
        </div>
    </div>

    {{-- Errors --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg">
            <div class="font-medium mb-1">Có lỗi xảy ra:</div>
            <ul class="list-disc list-inside space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('admin.products.update', $product->id) }}"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Basic info --}}
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Thông tin cơ bản</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tên sản phẩm <span class="text-red-500">*</span>
                            </label>
                            <input name="name"
                                   value="{{ old('name', $product->name) }}"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-primary focus:border-primary transition-colors"
                                   required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- SKU (nếu DB có sku thì giữ, không có thì bạn xóa block này) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Mã SKU
                                </label>
                                <input name="sku"
                                       value="{{ old('sku', $product->sku ?? '') }}"
                                       placeholder="SKU-001"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-primary focus:border-primary transition-colors">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Giá bán (VNĐ) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input name="price"
                                           value="{{ old('price', $product->price) }}"
                                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-primary focus:border-primary transition-colors pl-4 pr-12 text-right"
                                           placeholder="0"
                                           required>
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 dark:text-gray-400 pointer-events-none">đ</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Số lượng <span class="text-red-500">*</span>
                                </label>
                                <input name="quantity"
                                       value="{{ old('quantity', $product->quantity) }}"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-primary focus:border-primary transition-colors"
                                       required>
                            </div>

                            <div class="flex items-center gap-6 pt-6">
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="new" value="1"
                                           class="rounded border-gray-300 dark:border-gray-600"
                                           {{ old('new', $product->new) ? 'checked' : '' }}>
                                    Sản phẩm mới
                                </label>

                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="is_active" value="1"
                                           class="rounded border-gray-300 dark:border-gray-600"
                                           {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    Hiển thị
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Danh mục <span class="text-red-500">*</span>
                                </label>
                                <select name="category_id"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-primary focus:border-primary transition-colors"
                                        required>
                                    <option value="1" {{ (int)old('category_id', $product->category_id)===1 ? 'selected' : '' }}>Nguyên phụ liệu</option>
                                    <option value="2" {{ (int)old('category_id', $product->category_id)===2 ? 'selected' : '' }}>Đồ trang trí</option>
                                    <option value="3" {{ (int)old('category_id', $product->category_id)===3 ? 'selected' : '' }}>Thời trang len</option>
                                    <option value="4" {{ (int)old('category_id', $product->category_id)===4 ? 'selected' : '' }}>Combo tự làm</option>
                                    <option value="5" {{ (int)old('category_id', $product->category_id)===5 ? 'selected' : '' }}>Sách hướng dẫn</option>
                                    <option value="6" {{ (int)old('category_id', $product->category_id)===6 ? 'selected' : '' }}>Thú bông len</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Trạng thái <span class="text-red-500">*</span>
                                </label>
                                <select name="status"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-primary focus:border-primary transition-colors"
                                        required>
                                    <option value="còn hàng" {{ old('status', $product->status)==='còn hàng' ? 'selected' : '' }}>Còn hàng</option>
                                    <option value="hết hàng" {{ old('status', $product->status)==='hết hàng' ? 'selected' : '' }}>Hết hàng</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Màu sắc</label>
                                <input name="color"
                                       value="{{ old('color', $product->color) }}"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-primary focus:border-primary transition-colors"
                                       placeholder="Ví dụ: Trắng, Đỏ...">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kích thước</label>
                                <input name="size"
                                       value="{{ old('size', $product->size) }}"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-primary focus:border-primary transition-colors"
                                       placeholder="Ví dụ: S, M, L...">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mô tả</label>
                            <textarea name="description"
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-primary focus:border-primary transition-colors h-32 resize-none"
                                      placeholder="Nhập mô tả chi tiết...">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Placeholder variants (sau bạn muốn làm động mình nối) --}}
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Phân loại / Biến thể</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        (Đang để khung theo đúng style. Khi bạn làm “variant động”, mình nối JS + xử lý submit về controller.)
                    </p>

                    <button type="button"
                            class="mt-4 inline-flex items-center gap-2 px-4 py-2 border border-dashed border-primary text-primary hover:bg-primary/5 rounded-lg text-sm font-medium transition-colors">
                        <span class="material-icons-round text-lg">add</span>
                        Thêm nhóm phân loại
                    </button>
                </div>

            </div>

            {{-- RIGHT --}}
            <div class="space-y-6">

                {{-- Image --}}
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Hình ảnh</h3>

                    <div class="mb-3">
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">Ảnh hiện tại</p>
                        <img id="imagePreview"
                             src="{{ $product->image ? $product->image : '' }}"
                             class="w-full h-56 object-contain rounded-lg border border-border-light dark:border-border-dark bg-gray-50 dark:bg-gray-800"
                             alt="Preview">
                    </div>

                    <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <span class="material-icons-round text-4xl text-gray-400 mb-2">cloud_upload</span>
                            <p class="mb-1 text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-semibold">Nhấn để chọn ảnh</span> (hoặc kéo thả)
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG (Tối đa 2MB)</p>
                        </div>
                        <input id="imageInput" type="file" name="image" class="hidden" accept="image/*">
                    </label>

                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Chọn ảnh mới nếu muốn thay đổi.</p>
                </div>

                {{-- Finish --}}
                <div class="bg-surface-light dark:bg-surface-dark rounded-xl p-6 shadow-sm border border-border-light dark:border-border-dark">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Hoàn tất</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        Kiểm tra lại thông tin trước khi lưu vào hệ thống.
                    </p>

                    <div class="flex flex-col gap-3">
                        <button type="submit"
                                class="w-full py-2.5 px-4 bg-primary hover:bg-primary-hover text-white font-medium rounded-lg shadow-md shadow-primary/20 transition-all flex items-center justify-center gap-2">
                            <span class="material-icons-round text-sm">save</span>
                            Cập nhật sản phẩm
                        </button>

                        <a href="{{ route('admin.products.index') }}"
                           class="w-full py-2.5 px-4 border border-border-light dark:border-border-dark text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                            Hủy
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Preview ảnh ngay khi chọn
    const input = document.getElementById('imageInput');
    const preview = document.getElementById('imagePreview');

    if (input && preview) {
        input.addEventListener('change', function () {
            const file = this.files?.[0];
            if (!file) return;

            const url = URL.createObjectURL(file);
            preview.src = url;
        });

        // Drag & drop nhanh (đơn giản)
        const dropZone = input.closest('label');
        if (dropZone) {
            ['dragenter','dragover'].forEach(ev => {
                dropZone.addEventListener(ev, (e) => {
                    e.preventDefault();
                    dropZone.classList.add('ring-2','ring-primary');
                });
            });
            ['dragleave','drop'].forEach(ev => {
                dropZone.addEventListener(ev, (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('ring-2','ring-primary');
                });
            });
            dropZone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                if (!dt || !dt.files || !dt.files.length) return;
                input.files = dt.files;
                input.dispatchEvent(new Event('change'));
            });
        }
    }
</script>
@endpush
