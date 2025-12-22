@extends('admin.layout')
@section('title', ($siteName ?? 'Lenlab Official') . ' - Quản lý Marketing & Nội dung')

@php
    $pageTitle = 'Marketing';
    $pageHeading = 'Quản lý Marketing & Nội dung';
    $pageDescription = 'Quản lý bài viết blog, tin tức và banner quảng cáo.';
    $createUrl = route('admin.posts.create');
@endphp

@section('content')

{{-- Tabs --}}
<div class="flex items-center gap-6 border-b border-gray-200 dark:border-gray-800 mb-6">
    <a href="{{ route('admin.posts.index') }}"
       class="pb-3 font-semibold {{ request()->routeIs('admin.posts.*') ? 'text-primary border-b-2 border-primary' : 'text-gray-500' }}">
        <span class="inline-flex items-center gap-2">
            <span class="material-icons-round text-base">article</span> Bài viết & Blog
        </span>
    </a>

    <a href="{{ route('admin.banners.edit') }}"
   class="pb-3 font-semibold {{ request()->routeIs('admin.banners.*') ? 'text-primary border-b-2 border-primary' : 'text-gray-500' }}">
    <span class="inline-flex items-center gap-2">
        <span class="material-icons-round text-base">photo</span> Banner quảng cáo
    </span>
</a>
</div>

{{-- Toolbar filter/search --}}
<form method="GET" action="{{ route('admin.posts.index') }}" class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm p-5 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex-1">
            <div class="relative">
                <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                <input
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Tìm kiếm bài viết..."
                    class="w-full pl-10 pr-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 outline-none focus:ring-2 focus:ring-primary"
                />
            </div>
        </div>

        <div class="flex items-center gap-3">
            <select name="category" class="px-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <option value="">Tất cả danh mục</option>
                <option value="Tips" {{ request('category')=='Tips'?'selected':'' }}>Tips</option>
                <option value="Trend" {{ request('category')=='Trend'?'selected':'' }}>Trend</option>
                <option value="Tutorial" {{ request('category')=='Tutorial'?'selected':'' }}>Tutorial</option>
            </select>

            <select name="status" class="px-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <option value="">Trạng thái</option>
                <option value="public" {{ request('status')=='public'?'selected':'' }}>Public</option>
                <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
            </select>

            <button class="px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:opacity-90 dark:bg-white dark:text-gray-900">
                Lọc
            </button>
        </div>
    </div>
</form>

{{-- Posts Table/Content --}}
<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Danh sách bài viết</h3>
        </div>

        @if(isset($posts) && $posts->count() > 0)

            {{-- Bulk delete form --}}
            <form id="bulkDeleteForm" method="POST" action="{{ route('admin.posts.bulkDelete') }}">
                @csrf
                @method('DELETE')

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 w-10">
                                    <input type="checkbox" id="checkAll" class="h-4 w-4 rounded border-gray-300">
                                </th>
                                <th class="text-left py-3 px-4">Tiêu đề</th>
                                <th class="text-left py-3 px-4">Danh mục</th>
                                <th class="text-left py-3 px-4">Trạng thái</th>
                                <th class="text-left py-3 px-4">Ngày tạo</th>
                                <th class="text-left py-3 px-4">Thao tác</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($posts as $post)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="py-3 px-4">
                                        <input
                                            type="checkbox"
                                            class="rowChk h-4 w-4 rounded border-gray-300"
                                            name="ids[]"
                                            value="{{ $post->id }}"
                                        >
                                    </td>

                                    <td class="py-3 px-4">{{ $post->title ?? '(no title)' }}</td>
                                    <td class="py-3 px-4">{{ $post->category ?? '-' }}</td>

                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $post->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $post->is_published ? 'Đã đăng' : 'Nháp' }}
                                        </span>
                                    </td>

                                    <td class="py-3 px-4">
                                        {{ optional($post->created_at)->format('d/m/Y') }}
                                    </td>

                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-blue-600 hover:text-blue-800">
                                                Sửa
                                            </a>

                                            {{-- ✅ ĐÃ BỎ nút xóa từng dòng --}}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>

            <div class="mt-4">
                {{ $posts->links() }}
            </div>

        @else
            <div class="text-center py-8">
                <span class="material-icons-round text-gray-400 text-4xl mb-2">article</span>
                <p class="text-gray-500">Chưa có bài viết nào</p>
                <a href="{{ route('admin.posts.create') }}" class="inline-block mt-4 px-4 py-2 bg-primary text-white rounded-lg hover:opacity-90">
                    Tạo bài viết đầu tiên
                </a>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    const bulkBtn = document.getElementById('btnBulkDelete');
    const checkAll = document.getElementById('checkAll');
    const form = document.getElementById('bulkDeleteForm');

    function getRowChecks() {
        return document.querySelectorAll('.rowChk');
    }

    function getCheckedCount() {
        return document.querySelectorAll('.rowChk:checked').length;
    }

    function updateBulkBtn() {
        const checked = getCheckedCount();
        if (bulkBtn) bulkBtn.disabled = checked === 0;
    }

    function updateCheckAllState() {
        const all = getRowChecks();
        const checked = document.querySelectorAll('.rowChk:checked');
        if (!checkAll) return;

        if (all.length === 0) {
            checkAll.checked = false;
            checkAll.indeterminate = false;
            return;
        }

        if (checked.length === 0) {
            checkAll.checked = false;
            checkAll.indeterminate = false;
            return;
        }

        if (checked.length === all.length) {
            checkAll.checked = true;
            checkAll.indeterminate = false;
            return;
        }

        checkAll.checked = false;
        checkAll.indeterminate = true; // có chọn nhưng chưa hết
    }

    // change events
    document.addEventListener('change', (e) => {
        const t = e.target;

        if (t && t.id === 'checkAll') {
            getRowChecks().forEach(cb => cb.checked = t.checked);
            updateCheckAllState();
            updateBulkBtn();
            return;
        }

        if (t && t.classList.contains('rowChk')) {
            updateCheckAllState();
            updateBulkBtn();
        }
    });

    // bulk delete click
    bulkBtn?.addEventListener('click', () => {
        if (!form) return alert('Không tìm thấy form bulkDeleteForm');

        const checked = getCheckedCount();
        if (checked === 0) return;

        // Nếu có modal confirm custom thì dùng, không có thì fallback confirm()
        if (window.LenlabConfirmDelete && typeof window.LenlabConfirmDelete.open === 'function') {
            window.LenlabConfirmDelete.open({
                title: 'Xác nhận xóa bài viết',
                desc: `Bạn có chắc chắn muốn xóa ${checked} bài viết đã chọn? Hành động này không thể hoàn tác.`,
                onOk: () => form.submit()
            });
        } else {
            if (confirm(`Bạn có chắc chắn muốn xóa ${checked} bài viết đã chọn?`)) {
                form.submit();
            }
        }
    });

    // init state
    updateCheckAllState();
    updateBulkBtn();
})();
</script>
@endpush
