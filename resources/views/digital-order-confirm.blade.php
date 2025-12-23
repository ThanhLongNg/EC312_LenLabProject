<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Xác nhận đơn hàng số - LENLAB</title>
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&family=Noto+Sans:wght@400;500;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#FAC638", 
                        "background-light": "#f8f8f5", 
                        "background-dark": "#231e0f", 
                        "surface-dark": "#1c2e24", 
                        "surface-light": "#ffffff"
                    },
                    fontFamily: {
                        "display": ["Spline Sans", "sans-serif"],
                        "body": ["Noto Sans", "sans-serif"]
                    }
                },
            },
        }
    </script>
    <style>
        .hide-scrollbar::-webkit-scrollbar {display: none;}
        .hide-scrollbar {-ms-overflow-style: none; scrollbar-width: none;}
    </style>
</head>
<body class="bg-background-dark font-display min-h-screen flex flex-col antialiased">

<!-- Header -->
<header class="sticky top-0 z-50 w-full bg-background-dark/95 backdrop-blur-md border-b border-white/10">
    <div class="px-4 py-3 flex items-center gap-3">
        <button class="flex items-center justify-center size-10 rounded-full hover:bg-white/10 transition-colors" onclick="history.back()">
            <span class="material-symbols-outlined text-white">arrow_back</span>
        </button>
        <h1 class="text-lg font-medium text-white">Xác nhận đơn hàng số</h1>
    </div>
</header>

<!-- Main Content -->
<main class="flex-grow pb-24 px-4 py-6">
    <!-- Order Info -->
    <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <div class="text-gray-400 text-sm">MÃ ĐƠN HÀNG</div>
                <div class="text-white font-bold text-lg">#{{ $orderCode }}</div>
            </div>
            <div class="text-right">
                <div class="text-gray-400 text-sm">Chi tiết thanh toán</div>
                <div class="text-primary font-bold text-xl">{{ number_format($product->price) }}đ</div>
            </div>
        </div>
    </div>

    <!-- Product Info -->
    <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
        <h2 class="text-white font-semibold mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">inventory_2</span>
            Thông tin sản phẩm
        </h2>
        
        <div class="flex gap-3">
            <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 bg-gray-700">
                @if($product->thumbnail_url)
                    <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover"/>
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-xl">description</span>
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <h3 class="text-white font-medium mb-1">{{ $product->name }}</h3>
                <p class="text-gray-400 text-sm mb-2 line-clamp-2">{{ Str::limit($product->description, 80) }}</p>
                <div class="flex items-center gap-4 text-xs text-gray-400">
                    <span>{{ $product->download_limit }} lần tải</span>
                    <span>{{ $product->access_days }} ngày truy cập</span>
                    <span class="px-2 py-1 bg-primary/20 text-primary rounded">
                        @switch($product->type)
                            @case('course') VIDEO @break
                            @case('file') PDF @break
                            @default E-BOOK
                        @endswitch
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Info -->
    <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-white font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">person</span>
                Thông tin nhận file
            </h2>
            
            @auth
            <!-- Toggle Switch -->
            <div class="flex items-center gap-2">
                <span class="text-gray-400 text-sm">Dùng thông tin đăng nhập</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="useLoginInfo" class="sr-only peer" onchange="toggleLoginInfo()">
                    <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
            </div>
            @endauth
        </div>
        
        <form id="customerForm" class="space-y-4">
            <div>
                <label class="block text-gray-300 text-sm mb-2">
                    Tên của bạn <span class="text-red-400">*</span>
                </label>
                <input type="text" id="customerName" name="customer_name" required
                       class="w-full bg-black/30 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:border-primary focus:outline-none"
                       placeholder="Nhập tên của bạn">
            </div>
            
            <div>
                <label class="block text-gray-300 text-sm mb-2">
                    Email nhận file <span class="text-red-400">*</span>
                </label>
                <input type="email" id="customerEmail" name="customer_email" required
                       class="w-full bg-black/30 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:border-primary focus:outline-none"
                       placeholder="example@gmail.com">
                <p class="text-gray-400 text-xs mt-1">File sản phẩm sẽ được gửi đến email này sau khi thanh toán thành công.</p>
            </div>
        </form>
    </div>

    <!-- Payment Method -->
    <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
        <h2 class="text-white font-semibold mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">payment</span>
            Thanh toán chuyển khoản
        </h2>
        
        <!-- QR Code -->
        <div class="bg-primary/10 rounded-2xl p-6 text-center mb-4">
            <div class="w-48 h-48 bg-white rounded-xl mx-auto mb-4 flex items-center justify-center overflow-hidden">
                <img src="{{ asset('QR nhận tiền.jpg') }}" alt="QR Code thanh toán" class="w-full h-full object-contain"/>
            </div>
            <p class="text-white text-sm mb-2">Quét mã QR để thanh toán nhanh</p>
            <p class="text-gray-400 text-xs">Chuyển khoản với nội dung bên dưới</p>
        </div>

        <!-- Bank Info -->
        <div class="space-y-3">
            <div class="flex items-center gap-3 p-3 bg-black/20 rounded-xl">
                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold text-sm">VCB</span>
                </div>
                <div class="flex-1">
                    <div class="text-white font-medium">NGUYỄN THÀNH LONG</div>
                    <div class="text-gray-400 text-sm">Vietcombank</div>
                </div>
            </div>
            
            <div class="bg-black/20 rounded-xl p-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-400 text-sm">SỐ TÀI KHOẢN</span>
                    <button onclick="copyToClipboard('1040814582')" class="text-primary text-sm">
                        <span class="material-symbols-outlined text-lg">content_copy</span>
                    </button>
                </div>
                <div class="text-white font-mono text-lg">1040814582</div>
            </div>
            
            <div class="bg-black/20 rounded-xl p-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-400 text-sm">NỘI DUNG CHUYỂN KHOẢN</span>
                    <button onclick="copyToClipboard('{{ $orderCode }}')" class="text-primary text-sm">
                        <span class="material-symbols-outlined text-lg">content_copy</span>
                    </button>
                </div>
                <div class="text-white font-mono text-lg">{{ $orderCode }}</div>
            </div>
        </div>
    </div>

    <!-- Order Confirmation -->
    <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
        <h2 class="text-white font-semibold mb-4">
            Xác nhận giao dịch <span class="text-red-400">*</span>
        </h2>
        <div class="bg-black/20 rounded-xl p-4 text-center">
            <div id="imagePreviewContainer" class="hidden mb-4">
                <img id="imagePreview" src="" alt="Ảnh xác nhận chuyển khoản" class="w-full max-w-xs mx-auto rounded-lg border border-white/20"/>
                <p class="text-green-400 text-sm mt-2 flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-lg">check_circle</span>
                    Đã chọn ảnh xác nhận
                </p>
            </div>
            <div id="uploadPrompt">
                <span class="material-symbols-outlined text-primary text-4xl mb-2">upload</span>
                <p class="text-white font-medium mb-2">Tải lên ảnh xác nhận chuyển khoản</p>
                <p class="text-gray-400 text-sm mb-4">Gửi ảnh chụp màn hình giao dịch để xác nhận</p>
            </div>
            <input type="file" id="transferImage" accept="image/*" class="hidden" onchange="handleImageUpload(event)" required>
            <button id="selectImageBtn" onclick="document.getElementById('transferImage').click()" 
                    class="bg-primary text-background-dark px-6 py-2 rounded-full font-medium">
                Chọn ảnh
            </button>
            <p class="text-gray-400 text-xs mt-2">
                <span class="text-red-400">*</span> Bắt buộc - Chấp nhận JPG, PNG, GIF (tối đa 2MB)
            </p>
        </div>
    </div>
</main>

<!-- Fixed Bottom Bar -->
<div class="fixed bottom-0 left-0 right-0 bg-background-dark/95 backdrop-blur-md border-t border-white/10 p-4 z-40">
    <button onclick="completeOrder()" 
            class="w-full bg-primary hover:bg-primary/90 text-background-dark py-4 rounded-full font-bold text-lg transition-colors flex items-center justify-center gap-2">
        <span class="material-symbols-outlined">check_circle</span>
        Hoàn tất thanh toán
    </button>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function toggleLoginInfo() {
    const toggle = document.getElementById('useLoginInfo');
    const nameInput = document.getElementById('customerName');
    const emailInput = document.getElementById('customerEmail');
    
    if (toggle.checked) {
        // Fill with user info
        @auth
        nameInput.value = '{{ auth()->user()->name ?? "" }}';
        emailInput.value = '{{ auth()->user()->email ?? "" }}';
        @endauth
        
        // Make fields readonly
        nameInput.readOnly = true;
        emailInput.readOnly = true;
        nameInput.classList.add('bg-gray-700/50', 'cursor-not-allowed');
        emailInput.classList.add('bg-gray-700/50', 'cursor-not-allowed');
    } else {
        // Clear fields and make editable
        nameInput.value = '';
        emailInput.value = '';
        nameInput.readOnly = false;
        emailInput.readOnly = false;
        nameInput.classList.remove('bg-gray-700/50', 'cursor-not-allowed');
        emailInput.classList.remove('bg-gray-700/50', 'cursor-not-allowed');
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show temporary success message
        const button = event.target.closest('button');
        const originalIcon = button.innerHTML;
        button.innerHTML = '<span class="material-symbols-outlined text-lg text-green-500">check</span>';
        setTimeout(() => {
            button.innerHTML = originalIcon;
        }, 1000);
    });
}

function handleImageUpload(event) {
    const file = event.target.files[0];
    if (file) {
        // Show image preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const imagePreview = document.getElementById('imagePreview');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            const uploadPrompt = document.getElementById('uploadPrompt');
            const selectImageBtn = document.getElementById('selectImageBtn');
            
            imagePreview.src = e.target.result;
            imagePreviewContainer.classList.remove('hidden');
            uploadPrompt.classList.add('hidden');
            selectImageBtn.innerHTML = 'Thay đổi ảnh';
            selectImageBtn.classList.remove('bg-primary');
            selectImageBtn.classList.add('bg-gray-600');
        };
        reader.readAsDataURL(file);
    }
}

function completeOrder() {
    const customerName = document.getElementById('customerName').value.trim();
    const customerEmail = document.getElementById('customerEmail').value.trim();
    const transferImage = document.getElementById('transferImage').files[0];
    
    // Validate customer information
    if (!customerName) {
        alert('Vui lòng nhập tên của bạn!');
        document.getElementById('customerName').focus();
        return;
    }
    
    if (!customerEmail) {
        alert('Vui lòng nhập email nhận file!');
        document.getElementById('customerEmail').focus();
        return;
    }
    
    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(customerEmail)) {
        alert('Vui lòng nhập email hợp lệ!');
        document.getElementById('customerEmail').focus();
        return;
    }
    
    // Validate transfer image
    if (!transferImage) {
        alert('Vui lòng tải lên ảnh xác nhận chuyển khoản!');
        document.getElementById('transferImage').click();
        return;
    }
    
    // Validate image file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(transferImage.type)) {
        alert('Vui lòng chọn file ảnh hợp lệ (JPG, PNG, GIF)!');
        return;
    }
    
    // Validate image file size (max 2MB)
    const maxSize = 2 * 1024 * 1024; // 2MB
    if (transferImage.size > maxSize) {
        alert('Kích thước ảnh không được vượt quá 2MB!');
        return;
    }
    
    // Create FormData for file upload
    const formData = new FormData();
    formData.append('digital_product_id', {{ $product->id }});
    formData.append('customer_name', customerName);
    formData.append('customer_email', customerEmail);
    formData.append('order_code', '{{ $orderCode }}');
    formData.append('amount_paid', {{ $product->price }});
    formData.append('transfer_image', transferImage);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="material-symbols-outlined animate-spin">sync</span> Đang xử lý...';
    button.disabled = true;
    
    // Submit order
    fetch('/api/digital-orders', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Redirect to success page
            window.location.href = `/don-hang-so-thanh-cong/${data.order_id}`;
        } else {
            alert('Có lỗi xảy ra: ' + (data.message || 'Lỗi không xác định'));
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi xử lý đơn hàng: ' + error.message);
        button.innerHTML = originalText;
        button.disabled = false;
    });
}
</script>

</body>
</html>