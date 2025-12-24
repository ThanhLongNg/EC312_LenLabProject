<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="user-name" content="{{ auth()->user()->name }}">
        <meta name="user-email" content="{{ auth()->user()->email }}">
    @endauth
    <title>Yêu cầu của tôi - LENLAB</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#FAC638", 
                        "background-dark": "#0f0f0f", 
                        "surface-dark": "#1a1a1a", 
                        "card-dark": "#262626"
                    },
                    fontFamily: {
                        "sans": ["Inter", "sans-serif"]
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-background-dark text-white font-sans min-h-screen">
    <!-- Header -->
    <div class="bg-surface-dark border-b border-gray-800">
        <div class="max-w-md mx-auto px-4">
            <div class="flex items-center justify-between h-14">
                <div class="flex items-center gap-3">
                    <a href="/" class="text-gray-400 hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-xl">arrow_back</span>
                    </a>
                    <h1 class="text-lg font-semibold">Yêu cầu của tôi</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-surface-dark border-b border-gray-800">
        <div class="max-w-md mx-auto px-4">
            <div class="flex">
                <button class="tab-btn active flex-1 py-3 text-center text-sm font-medium border-b-2 border-primary text-primary" data-tab="all">
                    Tất cả
                </button>
                <button class="tab-btn flex-1 py-3 text-center text-sm font-medium border-b-2 border-transparent text-gray-400" data-tab="pending">
                    Chờ xác nhận
                </button>
                <button class="tab-btn flex-1 py-3 text-center text-sm font-medium border-b-2 border-transparent text-gray-400" data-tab="processing">
                    Đang giao
                </button>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-md mx-auto">
        @if($requests->isEmpty())
            <!-- Empty State -->
            <div class="text-center py-16 px-4">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-800 rounded-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl text-gray-500">inbox</span>
                </div>
                <h3 class="text-lg font-medium mb-2 text-gray-300">Chưa có yêu cầu nào</h3>
                <p class="text-gray-500 mb-6 text-sm">Bạn chưa tạo yêu cầu sản phẩm tùy chỉnh nào.</p>
                <a href="/" 
                   class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-black px-6 py-3 rounded-xl font-medium transition-colors">
                    <span class="material-symbols-outlined text-sm">add</span>
                    Tạo yêu cầu mới
                </a>
            </div>
        @else
            <!-- Requests List -->
            <div class="p-4 space-y-4">
                @foreach($requests as $request)
                    <div class="request-card bg-card-dark rounded-2xl p-4 border border-gray-800 
                        @switch($request->status)
                            @case('pending_admin_response')
                                status-pending
                                @break
                            @case('in_discussion')
                            @case('awaiting_payment')
                            @case('payment_submitted')
                            @case('paid')
                                status-processing
                                @break
                            @case('completed')
                                status-completed
                                @break
                            @case('cancelled')
                                status-cancelled
                                @break
                        @endswitch
                    ">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-start gap-3 flex-1">
                                <!-- Product Image -->
                                <div class="w-12 h-12 rounded-xl bg-gray-700 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    @if($request->reference_images && count($request->reference_images) > 0)
                                        <img src="{{ asset('storage/' . $request->reference_images[0]) }}" 
                                             alt="Product" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <span class="material-symbols-outlined text-gray-400 text-lg">handyman</span>
                                    @endif
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs text-gray-400 font-mono">{{ $request->order_id }}</span>
                                        <span class="text-xs text-gray-500">{{ $request->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <h3 class="font-medium text-white text-sm mb-1">{{ $request->product_type }}</h3>
                                    <p class="text-xs text-gray-400 mb-2">{{ $request->size }}</p>
                                    
                                    <!-- Status Badge -->
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium flex items-center gap-1
                                            @switch($request->status)
                                                @case('pending_admin_response')
                                                    bg-yellow-500/20 text-yellow-400
                                                    @break
                                                @case('in_discussion')
                                                    bg-blue-500/20 text-blue-400
                                                    @break
                                                @case('awaiting_payment')
                                                    bg-orange-500/20 text-orange-400
                                                    @break
                                                @case('payment_submitted')
                                                    bg-purple-500/20 text-purple-400
                                                    @break
                                                @case('paid')
                                                    bg-green-500/20 text-green-400
                                                    @break
                                                @case('completed')
                                                    bg-emerald-500/20 text-emerald-400
                                                    @break
                                                @case('cancelled')
                                                    bg-red-500/20 text-red-400
                                                    @break
                                                @default
                                                    bg-gray-500/20 text-gray-400
                                            @endswitch
                                        ">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                            @switch($request->status)
                                                @case('pending_admin_response')
                                                    Đang giao hàng
                                                    @break
                                                @case('in_discussion')
                                                    Đang trao đổi
                                                    @break
                                                @case('awaiting_payment')
                                                    Chờ thanh toán
                                                    @break
                                                @case('payment_submitted')
                                                    Đã gửi thanh toán
                                                    @break
                                                @case('paid')
                                                    Đang sản xuất
                                                    @break
                                                @case('completed')
                                                    Hoàn thành
                                                    @break
                                                @case('cancelled')
                                                    Đã hủy
                                                    @break
                                                @default
                                                    Chờ xác nhận
                                            @endswitch
                                        </span>
                                        
                                        @php
                                            $unreadCount = \App\Models\ChatSupportLog::where('custom_request_id', $request->id)
                                                ->where('sender_type', 'admin')
                                                ->where('is_read', false)
                                                ->count();
                                        @endphp
                                        
                                        @if($unreadCount > 0)
                                            <span class="bg-red-500 text-white px-1.5 py-0.5 rounded-full text-xs font-medium">
                                                {{ $unreadCount }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="mb-3">
                            <p class="text-sm text-gray-300 line-clamp-2">
                                {{ Str::limit($request->description, 80) }}
                            </p>
                        </div>
                        
                        <!-- Price and Actions -->
                        <div class="flex items-center justify-between">
                            <div>
                                @if($request->final_price)
                                    <div class="text-lg font-bold text-primary">{{ number_format($request->final_price) }}đ</div>
                                    <div class="text-xs text-gray-400">Tổng tiền sản phẩm</div>
                                @elseif($request->estimated_price)
                                    <div class="text-lg font-semibold text-yellow-400">{{ number_format($request->estimated_price) }}đ</div>
                                    <div class="text-xs text-gray-400">Giá ước tính</div>
                                @else
                                    <div class="text-sm text-gray-400">Chưa có báo giá</div>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-2">
                                @if($request->status === 'awaiting_payment')
                                    <button onclick="showPaymentModal({{ $request->id }}, {{ $request->final_price }})" 
                                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-lg text-xs font-medium transition-colors">
                                        Mua lại
                                    </button>
                                @endif
                                
                                <a href="{{ route('chat-support.show', $request->id) }}" 
                                   class="bg-primary hover:bg-primary/90 text-black px-4 py-2 rounded-lg text-xs font-medium transition-colors flex items-center gap-1">
                                    Chi tiết
                                    @if($unreadCount > 0)
                                        <span class="bg-red-500 text-white px-1 py-0.5 rounded-full text-xs ml-1">
                                            {{ $unreadCount }}
                                        </span>
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-surface-dark rounded-2xl max-w-sm w-full p-6 border border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold">Thanh toán</h3>
                    <button onclick="closePaymentModal()" class="text-gray-400 hover:text-white">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div class="text-center">
                        <p class="text-gray-400 mb-2 text-sm">Tổng số tiền</p>
                        <p id="paymentAmount" class="text-2xl font-bold text-primary"></p>
                    </div>
                    
                    <div class="bg-gray-800 rounded-lg p-4">
                        <p class="text-sm text-gray-400 mb-2">Thông tin chuyển khoản</p>
                        <p class="font-medium text-sm">Ngân hàng: Vietcombank</p>
                        <p class="font-medium text-sm">STK: 1234567890</p>
                        <p class="font-medium text-sm">Chủ TK: LENLAB COMPANY</p>
                    </div>
                    
                    <button onclick="proceedToPayment()" 
                            class="w-full bg-primary hover:bg-primary/90 text-black py-3 rounded-lg font-medium transition-colors">
                        Tiến hành thanh toán
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    let currentRequestId = null;

    // Tab functionality
    document.addEventListener('DOMContentLoaded', function() {
        const tabBtns = document.querySelectorAll('.tab-btn');
        const requestCards = document.querySelectorAll('.request-card');
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const tab = this.dataset.tab;
                
                // Update active tab
                tabBtns.forEach(b => {
                    b.classList.remove('active', 'border-primary', 'text-primary');
                    b.classList.add('border-transparent', 'text-gray-400');
                });
                this.classList.add('active', 'border-primary', 'text-primary');
                this.classList.remove('border-transparent', 'text-gray-400');
                
                // Filter cards
                requestCards.forEach(card => {
                    if (tab === 'all') {
                        card.style.display = 'block';
                    } else if (tab === 'pending' && card.classList.contains('status-pending')) {
                        card.style.display = 'block';
                    } else if (tab === 'processing' && card.classList.contains('status-processing')) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    });

    function showPaymentModal(requestId, amount) {
        currentRequestId = requestId;
        document.getElementById('paymentAmount').textContent = new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
        document.getElementById('paymentModal').classList.remove('hidden');
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
        currentRequestId = null;
    }

    function proceedToPayment() {
        if (currentRequestId) {
            window.location.href = `/chat-support/${currentRequestId}`;
        }
    }

    // Auto-refresh for new messages
    setInterval(function() {
        fetch('/api/my-requests/check-messages')
            .then(response => response.json())
            .then(data => {
                if (data.hasNewMessages) {
                    location.reload();
                }
            })
            .catch(error => console.log('Error checking messages:', error));
    }, 30000);
    </script>
</body>
</html>