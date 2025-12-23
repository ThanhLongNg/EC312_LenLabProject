<!DOCTYPE html>
<html lang="vi" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($siteName ?? 'Lenlab Official') . ' - Admin')</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ $faviconUrl ?? asset('favicon.ico') }}">

    {{-- Dynamic CSS from settings --}}
    <style>
        {!! $dynamicCss ?? '' !!}
    </style>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '{{ $primaryColor ?? "#D1A272" }}',
                        'primary-hover': '{{ \App\Helpers\SettingsHelper::adjustBrightness($primaryColor ?? "#D1A272", -20) }}',
                        secondary: '#64748b',
                        success: '#10b981',
                        danger: '#ef4444',
                        warning: '#f59e0b',
                        info: '#06b6d4',
                        'background-light': '#F3F4F6',
                        'background-dark': '#18181B',
                        'surface-light': '#FFFFFF',
                        'surface-dark': '#27272A',
                        'text-light': '#1F2937',
                        'text-dark': '#E5E7EB',
                        'border-light': '#E5E7EB',
                        'border-dark': '#3F3F46'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    borderRadius: {
                        DEFAULT: '0.5rem',
                        'xl': '1rem'
                    }
                }
            }
        };
    </script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <!-- Bootstrap CSS (for components compatibility) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS for Bootstrap + Tailwind compatibility -->
    <style>
        /* Reset Bootstrap conflicts with Tailwind */
        .btn {
            border-radius: 0.5rem !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
        }
        
        .btn-primary {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
        }
        
        .btn-primary:hover {
            background-color: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            transform: translateY(-1px) !important;
        }
        
        .card {
            border: none !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
            border-radius: 0.75rem !important;
        }
        
        .table {
            border-radius: 0.5rem !important;
            overflow: hidden !important;
        }
        
        .table thead th {
            background-color: #f8fafc !important;
            border: none !important;
            font-weight: 600 !important;
            color: #1e293b !important;
        }
        
        .badge {
            font-weight: 500 !important;
            padding: 0.375rem 0.75rem !important;
            border-radius: 0.5rem !important;
        }
        
        .form-control, .form-select {
            border-radius: 0.5rem !important;
            border: 1px solid #d1d5db !important;
            transition: all 0.3s ease !important;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
        }
        
        .alert {
            border-radius: 0.5rem !important;
            border: none !important;
        }

        /* Mobile sidebar toggle */
        @media (max-width: 768px) {
            .sidebar-mobile {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar-mobile.show {
                transform: translateX(0);
            }
        }

        /* ✅ FIX: Sidebar không gạch chân link */
        aside a,
        aside a:hover,
        aside a:focus,
        aside a:active,
        aside a:visited {
            text-decoration: none !important;
        }

        /* Nếu có class "underline" / "text-decoration-underline" bị bootstrap/tailwind gắn */
        aside a.underline,
        aside a.text-decoration-underline {
            text-decoration: none !important;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark font-sans transition-colors duration-200 min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark">
            <!-- Header -->
            @include('admin.partials.header')

            <!-- Content Area -->
            <div class="p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg flex items-center gap-2" 
                         id="successAlert">
                        <span class="material-icons-round text-green-600 dark:text-green-400">check_circle</span>
                        <span>{{ session('success') }}</span>
                        <button onclick="document.getElementById('successAlert').remove()" 
                                class="ml-auto text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200">
                            <span class="material-icons-round text-sm">close</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg flex items-center gap-2" 
                         id="errorAlert">
                        <span class="material-icons-round text-red-600 dark:text-red-400">error</span>
                        <span>{{ session('error') }}</span>
                        <button onclick="document.getElementById('errorAlert').remove()" 
                                class="ml-auto text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200">
                            <span class="material-icons-round text-sm">close</span>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg" 
                         id="errorsAlert">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="material-icons-round text-red-600 dark:text-red-400">warning</span>
                            <span class="font-medium">Có lỗi xảy ra:</span>
                            <button onclick="document.getElementById('errorsAlert').remove()" 
                                    class="ml-auto text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200">
                                <span class="material-icons-round text-sm">close</span>
                            </button>
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Main Content -->
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Mobile sidebar toggle (sử dụng ID từ header của bạn)
        document.getElementById('btnSidebar')?.addEventListener('click', function() {
            const sidebar = document.querySelector('aside');
            sidebar.classList.toggle('sidebar-mobile');
            sidebar.classList.toggle('show');
        });

        // Auto dismiss alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('[id$="Alert"]');
            alerts.forEach(alert => {
                if (alert) {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            });
        }, 5000);

        // CSRF Token for AJAX
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>

    {{-- Delete Confirm Modal (Global) --}}
    <div id="deleteModal" class="fixed inset-0 z-[9999] hidden">
        {{-- overlay --}}
        <div class="absolute inset-0 bg-black/40"></div>

        {{-- modal --}}
        <div class="relative flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-surface-dark shadow-xl border border-border-light dark:border-border-dark">
                <div class="p-6 flex gap-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                        <span class="material-icons-round text-red-600">warning</span>
                    </div>

                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="deleteModalTitle">
                            Xác nhận xóa
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="deleteModalDesc">
                            Bạn có chắc chắn muốn xóa? Hành động này không thể hoàn tác.
                        </p>
                    </div>
                </div>

                <div class="px-6 pb-6 flex justify-end gap-3">
                    <button type="button" id="btnDeleteCancel"
                        class="px-4 py-2 rounded-lg border border-border-light dark:border-border-dark text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5">
                        Hủy
                    </button>
                    <button type="button" id="btnDeleteConfirm"
                        class="px-4 py-2 rounded-lg bg-danger text-white hover:bg-red-600">
                        Xác nhận xóa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function() {
        const modal = document.getElementById('deleteModal');
        const btnCancel = document.getElementById('btnDeleteCancel');
        const btnConfirm = document.getElementById('btnDeleteConfirm');
        const titleEl = document.getElementById('deleteModalTitle');
        const descEl = document.getElementById('deleteModalDesc');

        let onConfirm = null;

        function openModal({ title, desc, onOk }) {
            if (titleEl) titleEl.textContent = title || 'Xác nhận xóa';
            if (descEl) descEl.textContent = desc || 'Bạn có chắc chắn muốn xóa? Hành động này không thể hoàn tác.';
            onConfirm = onOk || null;
            modal?.classList.remove('hidden');
        }

        function closeModal() {
            modal?.classList.add('hidden');
            onConfirm = null;
        }

        // click overlay to close
        modal?.addEventListener('click', (e) => {
            if (e.target === modal.firstElementChild) closeModal();
        });

        btnCancel?.addEventListener('click', closeModal);

        btnConfirm?.addEventListener('click', () => {
            if (typeof onConfirm === 'function') onConfirm();
            closeModal();
        });

        window.LenlabConfirmDelete = { open: openModal, close: closeModal };
    })();
    </script>

    {{-- Transfer Image Modal --}}
    <div id="transferImgModal" class="fixed inset-0 z-[9999] hidden">
      <div class="absolute inset-0 bg-black/50"></div>
      <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-3xl rounded-2xl bg-white dark:bg-surface-dark border border-border-light dark:border-border-dark overflow-hidden">
          <div class="flex items-center justify-between px-5 py-4 border-b border-border-light dark:border-border-dark">
            <div class="font-semibold">Minh chứng chuyển khoản</div>
            <button type="button" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5" id="btnCloseTransferImg">
              <span class="material-icons-round">close</span>
            </button>
          </div>
          <div class="p-4">
            <img id="transferImgPreview" src="" alt="Transfer"
                 class="w-full max-h-[70vh] object-contain rounded-xl border border-border-light dark:border-border-dark">
          </div>
        </div>
      </div>
    </div>

    <script>
    (function(){
      const modal = document.getElementById('transferImgModal');
      const img = document.getElementById('transferImgPreview');
      const btnClose = document.getElementById('btnCloseTransferImg');

      function open(url){
        if (img) img.src = url;
        modal?.classList.remove('hidden');
      }
      function close(){
        modal?.classList.add('hidden');
        if (img) img.src = '';
      }

      modal?.addEventListener('click', (e) => {
        if (e.target === modal.firstElementChild) close();
      });
      btnClose?.addEventListener('click', close);

      window.openTransferImage = open;
    })();
    </script>

    @stack('scripts')
</body>
</html>
