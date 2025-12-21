<header class="bg-surface-light dark:bg-surface-dark border-b border-border-light dark:border-border-dark sticky top-0 z-10 px-6 py-4 flex items-center justify-between">
    {{-- Mobile --}}
    <div class="flex items-center gap-4 md:hidden">
        <button type="button" class="text-gray-500 dark:text-gray-400" id="btnSidebar">
            <span class="material-icons-round">menu</span>
        </button>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">
            {{ $pageTitle ?? 'Sản phẩm' }}
        </h1>
    </div>

    {{-- Desktop --}}
    <div class="hidden md:block">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $pageHeading ?? 'Danh sách sản phẩm' }}
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ $pageDescription ?? 'Quản lý kho hàng và danh mục sản phẩm của bạn.' }}
        </p>
    </div>

    <div class="flex items-center gap-3">
        {{-- Dark mode --}}
        <button type="button"
            class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
            id="btnTheme">
            <span class="material-icons-round" id="themeIcon">dark_mode</span>
        </button>

        {{-- Bulk delete (submit form bulkDeleteForm nếu có) --}}
        <button type="button"
            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 font-medium shadow-md shadow-red-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            id="btnBulkDelete"
            disabled>
            <span class="material-icons-round text-sm">delete</span>
            Xóa mục đã chọn
        </button>

        {{-- Add new --}}
        <a href="{{ $createUrl ?? '#' }}"
            class="bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-lg flex items-center gap-2 font-medium shadow-md shadow-primary/20 transition-all">
            <span class="material-icons-round text-sm">add</span>
            Thêm mới
        </a>
    </div>

    <script>
        // Theme toggle: add/remove class "dark" on <html>
        (function() {
            const root = document.documentElement;
            const btn = document.getElementById('btnTheme');
            const icon = document.getElementById('themeIcon');

            // load from localStorage
            const saved = localStorage.getItem('theme');
            if (saved === 'dark') root.classList.add('dark');
            if (icon) icon.textContent = root.classList.contains('dark') ? 'light_mode' : 'dark_mode';

            btn?.addEventListener('click', () => {
                root.classList.toggle('dark');
                const isDark = root.classList.contains('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                if (icon) icon.textContent = isDark ? 'light_mode' : 'dark_mode';
            });
        })();

        // Bulk delete button: enable when any checkbox checked, submit form #bulkDeleteForm
        (function() {
            const btn = document.getElementById('btnBulkDelete');

            function updateBulkBtn() {
                const anyChecked = document.querySelectorAll('.rowChk:checked').length > 0;
                if (btn) btn.disabled = !anyChecked;
            }

            document.addEventListener('change', (e) => {
                const t = e.target;
                if (t && (t.classList.contains('rowChk') || t.id === 'checkAll')) updateBulkBtn();
            });

            btn?.addEventListener('click', () => {
                const form = document.getElementById('bulkDeleteForm');
                if (!form) return alert('Không tìm thấy form bulkDeleteForm');
                const ok = confirm('Bạn có chắc chắn muốn xóa các mục đã chọn không?');
                if (ok) form.submit();
            });

            // initial
            updateBulkBtn();
        })();
    </script>
</header>
