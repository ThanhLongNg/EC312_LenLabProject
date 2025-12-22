@extends('admin.layout')

@section('title', ($siteName ?? 'Lenlab Official') . ' - Chỉnh sửa bài viết')

@php
    $pageTitle = 'Marketing';
    $pageHeading = 'Chỉnh sửa bài viết';
    $pageDescription = 'Cập nhật bài viết blog/tin tức và chèn sản phẩm mua ngay.';
@endphp

@section('content')

<div class="flex items-center gap-6 border-b border-gray-200 dark:border-gray-800 mb-6">
    <a href="{{ route('admin.posts.index') }}"
       class="pb-3 font-semibold text-primary border-b-2 border-primary">
        <span class="inline-flex items-center gap-2">
            <span class="material-icons-round text-base">article</span> Bài viết & Blog
        </span>
    </a>
    <a href="#" class="pb-3 font-semibold text-gray-500">
        <span class="inline-flex items-center gap-2">
            <span class="material-icons-round text-base">photo</span> Banner quảng cáo
        </span>
    </a>
</div>

<form method="POST" action="{{ route('admin.posts.update', $post->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm p-6">
                <label class="text-sm font-semibold">Tiêu đề bài viết</label>
                <input name="title" value="{{ old('title', $post->title) }}" placeholder="Nhập tiêu đề bài viết tại đây..."
                       class="mt-2 w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                @error('title') <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm p-6">
                <div class="text-sm font-semibold mb-3">Nội dung</div>
                <textarea name="content" rows="14" placeholder="Viết nội dung... dùng shortcode [product:ID] để chèn sản phẩm"
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">{{ old('content', $post->content) }}</textarea>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm p-6">
                <div class="text-sm font-semibold mb-2">Mô tả ngắn</div>
                <textarea name="excerpt" rows="3" placeholder="Tóm tắt hiển thị ở landing page..."
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="space-y-6">

            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm p-6">
                <div class="font-semibold mb-4">Thông tin bài viết</div>

                <div class="text-sm text-gray-500 mb-2">Tác giả</div>
                <div class="flex items-center gap-2 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <span class="material-icons-round text-gray-400">person</span>
                    <span>{{ auth('admin')->user()->name ?? 'Admin User' }}</span>
                </div>

                <div class="text-sm text-gray-500 mt-4 mb-2">Danh mục</div>
                <select name="category"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <option value="">-- Chọn danh mục --</option>
                    <option value="Tin tức" {{ old('category', $post->category) === 'Tin tức' ? 'selected' : '' }}>Tin tức</option>
                    <option value="Xu hướng" {{ old('category', $post->category) === 'Xu hướng' ? 'selected' : '' }}>Xu hướng</option>
                    <option value="Tutorial" {{ old('category', $post->category) === 'Tutorial' ? 'selected' : '' }}>Tutorial</option>
                    <option value="Tips" {{ old('category', $post->category) === 'Tips' ? 'selected' : '' }}>Tips</option>
                </select>
            </div>

            {{-- Card chèn sản phẩm (bạn có $products thì bật lên, chưa có thì bỏ card này) --}}
            @isset($products)
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm p-6">
                <div class="font-semibold mb-4">Chèn sản phẩm vào nội dung</div>
                <select id="insertProductSelect"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <option value="">-- Chọn sản phẩm --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (#{{ $p->id }})</option>
                    @endforeach
                </select>
                <button type="button" id="insertProductBtn"
                        class="w-full mt-3 px-4 py-3 rounded-xl bg-black text-white font-semibold">
                    Chèn vào nội dung
                </button>
                <div class="text-xs text-gray-500 mt-3">Sẽ chèn mã: <b>[product:ID]</b></div>
            </div>
            @endisset

            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm p-6">
                <div class="font-semibold mb-4">Ảnh bìa</div>

                <input id="thumbnailInput" type="file" name="thumbnail" class="hidden" accept="image/*">

                <label for="thumbnailInput"
                       class="block border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl p-8 text-center cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">

                    {{-- Current image or placeholder --}}
                    @if($post->thumbnail)
                        <div id="thumbPreviewWrap">
                            <img id="thumbPreview" src="{{ asset('storage/' . $post->thumbnail) }}" class="mx-auto w-full max-w-xs rounded-xl" alt="current thumbnail">
                            <div id="thumbName" class="text-xs text-gray-500 mt-3">{{ basename($post->thumbnail) }}</div>
                            <div class="mt-3 text-xs text-gray-400">Bấm để đổi ảnh</div>
                        </div>
                        <div id="thumbPlaceholder" class="hidden flex flex-col items-center gap-2 text-gray-500">
                            <span class="material-icons-round text-4xl">cloud_upload</span>
                            <div class="font-semibold">Bấm để tải lên hoặc kéo thả</div>
                            <div class="text-xs">PNG, JPG (gợi ý 800×400)</div>
                        </div>
                    @else
                        <div id="thumbPlaceholder" class="flex flex-col items-center gap-2 text-gray-500">
                            <span class="material-icons-round text-4xl">cloud_upload</span>
                            <div class="font-semibold">Bấm để tải lên hoặc kéo thả</div>
                            <div class="text-xs">PNG, JPG (gợi ý 800×400)</div>
                        </div>
                        <div id="thumbPreviewWrap" class="hidden">
                            <img id="thumbPreview" class="mx-auto w-full max-w-xs rounded-xl" alt="preview">
                            <div id="thumbName" class="text-xs text-gray-500 mt-3"></div>
                            <div class="mt-3 text-xs text-gray-400">Bấm lại để đổi ảnh</div>
                        </div>
                    @endif

                </label>

                @error('thumbnail') <div class="text-red-500 text-sm mt-2">{{ $message }}</div> @enderror
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm p-6">
                <div class="font-semibold mb-4">Hành động</div>

                <button name="action" value="publish"
                        class="w-full px-4 py-3 rounded-xl bg-[#d6b46a] text-white font-semibold hover:opacity-90">
                    {{ $post->is_published ? 'Cập nhật & Đăng' : 'Đăng bài' }}
                </button>

                <button name="action" value="draft"
                        class="w-full mt-3 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 font-semibold">
                    Lưu nháp
                </button>
            </div>

        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('thumbnailInput');
  const img = document.getElementById('thumbPreview');
  const nameEl = document.getElementById('thumbName');
  const placeholder = document.getElementById('thumbPlaceholder');
  const previewWrap = document.getElementById('thumbPreviewWrap');

  if (!input) return;

  input.addEventListener('change', () => {
    const file = input.files && input.files[0];
    if (!file) return;

    // Show filename
    if (nameEl) nameEl.textContent = file.name;

    // Create preview URL
    const url = URL.createObjectURL(file);
    if (img) img.src = url;

    // Hide placeholder, show preview
    if (placeholder) placeholder.classList.add('hidden');
    if (previewWrap) previewWrap.classList.remove('hidden');
  });
});
</script>

@endsection