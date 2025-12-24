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
    <title>Chat Support - Yêu cầu {{ $request->order_id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#FF8C42", 
                        "chat-bg": "#1a1d29",
                        "chat-surface": "#252836", 
                        "chat-card": "#2f3349",
                        "chat-input": "#363a4f"
                    },
                    fontFamily: {
                        "body": ["Inter", "sans-serif"]
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .chat-container { background: linear-gradient(135deg, #1a1d29 0%, #252836 100%); }
        .info-card { background: rgba(47, 51, 73, 0.8); backdrop-filter: blur(10px); }
        .message-bubble { box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15); }
        .status-badge { backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-chat-bg text-white font-body min-h-screen chat-container">
    <!-- Mobile-First Layout -->
    <div class="flex flex-col h-screen max-w-md mx-auto bg-chat-surface">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 bg-chat-card border-b border-gray-600">
            <div class="flex items-center gap-3">
                <button onclick="window.history.back()" class="p-2 hover:bg-gray-600 rounded-full transition-colors">
                    <span class="material-symbols-outlined text-white">arrow_back</span>
                </button>
                <div>
                    <h1 class="font-semibold text-lg">Yêu cầu {{ $request->order_id }}</h1>
                    <p class="text-sm text-gray-400">{{ $request->user->name ?? 'Khách hàng' }}</p>
                </div>
            </div>
            <button class="p-2 hover:bg-gray-600 rounded-full transition-colors">
                <span class="material-symbols-outlined text-white">more_vert</span>
            </button>
        </div>

        <!-- Tab Navigation -->
        <div class="flex bg-chat-card">
            <button id="infoTab" class="flex-1 py-3 px-4 text-center font-medium bg-chat-input text-white rounded-none">
                Thông tin
            </button>
            <button id="chatTab" class="flex-1 py-3 px-4 text-center font-medium text-gray-400 hover:text-white transition-colors">
                Trao đổi
            </button>
        </div>

        <!-- Content Area -->
        <div class="flex-1 overflow-hidden">
            <!-- Info Panel -->
            <div id="infoPanel" class="h-full overflow-y-auto p-4 space-y-4">
                <!-- Status Card -->
                <div class="info-card rounded-xl p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-300">TRẠNG THÁI</h3>
                        @php
                            $statusColors = [
                                'pending_admin_response' => 'bg-yellow-500',
                                'in_discussion' => 'bg-blue-500',
                                'awaiting_payment' => 'bg-orange-500',
                                'payment_submitted' => 'bg-purple-500',
                                'paid' => 'bg-green-500',
                                'completed' => 'bg-green-500',
                                'cancelled' => 'bg-red-500',
                            ];
                            $statusColor = $statusColors[$request->status] ?? 'bg-gray-500';
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-medium text-white {{ $statusColor }}">
                            {{ $request->status === 'pending_admin_response' ? 'Chờ admin phản hồi' : $request->status_text }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-400">Cập nhật {{ $request->updated_at->diffForHumans() }}</p>
                </div>

                <!-- Customer Info -->
                <div class="info-card rounded-xl p-4">
                    <h3 class="font-semibold text-gray-300 mb-3">KHÁCH HÀNG</h3>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-sm">{{ substr($request->user->name ?? 'NA', 0, 2) }}</span>
                        </div>
                        <div>
                            <p class="font-medium">{{ $request->user->name ?? 'Khách hàng thân thiết' }}</p>
                            <p class="text-sm text-gray-400">Khách hàng thân thiết</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2 text-gray-400">
                            <span class="material-symbols-outlined text-xs">email</span>
                            <span>{{ $request->user->email ?? 'user@example.com' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-400">
                            <span class="material-symbols-outlined text-xs">phone</span>
                            <span>+84 901 234 567</span>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="info-card rounded-xl p-4">
                    <h3 class="font-semibold text-gray-300 mb-3">SẢN PHẨM</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-400 uppercase">LOẠI</p>
                            <p class="font-medium">{{ $request->product_type }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase">KÍCH THƯỚC</p>
                            <p class="font-medium">{{ $request->size }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-chat-input rounded-lg p-3 mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="material-symbols-outlined text-primary text-sm">info</span>
                            <span class="font-medium text-sm">THÔNG TIN SẢN PHẨM</span>
                        </div>
                        <ul class="text-sm text-gray-300 space-y-1">
                            <li>• Loại sản phẩm: {{ $request->product_type }}</li>
                            <li>• Kích thước: {{ $request->size }}</li>
                            <li>• Mô tả chi tiết: {{ Str::limit(strip_tags($request->description), 100) }}</li>
                        </ul>
                    </div>

                    @if($request->final_price)
                        <div class="flex justify-between items-center p-3 bg-green-500/20 rounded-lg">
                            <span class="text-sm font-medium">Giá cuối cùng</span>
                            <span class="text-green-400 font-bold">{{ number_format($request->final_price) }}đ</span>
                        </div>
                    @elseif($request->estimated_price)
                        <div class="flex justify-between items-center p-3 bg-blue-500/20 rounded-lg">
                            <span class="text-sm font-medium">Giá ước tính</span>
                            <span class="text-blue-400 font-bold">~{{ number_format($request->estimated_price) }}đ</span>
                        </div>
                    @endif
                </div>

                <!-- Reference Images -->
                @if($request->reference_images && count($request->reference_images) > 0)
                    <div class="info-card rounded-xl p-4">
                        <h3 class="font-semibold text-gray-300 mb-3">ẢNH THAM KHẢO</h3>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($request->reference_images as $index => $image)
                                <div class="relative group">
                                    <img src="{{ asset('storage/' . $image) }}" alt="Ảnh tham khảo {{ $index + 1 }}" 
                                         class="w-full h-24 object-cover rounded-lg border border-gray-600 cursor-pointer group-hover:opacity-80 transition-opacity"
                                         onclick="openImageModal('{{ asset('storage/' . $image) }}')">
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 rounded-lg transition-colors flex items-center justify-center">
                                        <span class="material-symbols-outlined text-white opacity-0 group-hover:opacity-100 transition-opacity">zoom_in</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Payment Action -->
                @if($request->status === 'awaiting_payment')
                    <div class="p-4">
                        <button onclick="showPaymentModal()" 
                                class="w-full bg-primary hover:bg-primary/90 text-white px-4 py-3 rounded-xl font-semibold transition-colors flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">payment</span>
                            Tiến hành thanh toán
                        </button>
                    </div>
                @endif
            </div>

            <!-- Chat Panel -->
            <div id="chatPanel" class="h-full flex-col hidden">
                <!-- Chat Messages -->
                <div class="flex-1 p-4 overflow-y-auto" id="chatMessages">
                    @foreach($chatHistory as $message)
                        <div class="mb-4 {{ $message->sender_type === 'customer' ? 'flex justify-end' : 'flex justify-start' }}">
                            <div class="max-w-[80%]">
                                <div class="flex items-center gap-2 mb-1 {{ $message->sender_type === 'customer' ? 'justify-end' : 'justify-start' }}">
                                    @if($message->sender_type === 'customer')
                                        <span class="text-xs text-gray-400">{{ $message->created_at->format('H:i') }}</span>
                                        <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                                        </div>
                                    @else
                                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">A</span>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $message->created_at->format('H:i') }}</span>
                                    @endif
                                </div>
                                <div class="p-3 rounded-2xl message-bubble {{ $message->sender_type === 'customer' ? 'bg-primary text-white ml-8' : 'bg-chat-card text-white mr-8' }}">
                                    {{ $message->message }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Message Input -->
                <div class="p-4 bg-chat-card border-t border-gray-600">
                    <form id="messageForm" class="flex gap-3 items-end">
                        <input type="hidden" name="custom_request_id" value="{{ $request->id }}">
                        <div class="flex-1">
                            <textarea name="message" id="messageInput" 
                                      placeholder="Nhập tin nhắn..." 
                                      class="w-full px-4 py-3 bg-chat-input border border-gray-600 rounded-2xl resize-none focus:outline-none focus:ring-2 focus:ring-primary/50 text-white placeholder-gray-400"
                                      rows="1"></textarea>
                        </div>
                        <button type="submit" 
                                class="bg-primary hover:bg-primary/90 text-white p-3 rounded-full transition-colors flex items-center justify-center">
                            <span class="material-symbols-outlined text-lg">send</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    @if($request->status === 'awaiting_payment')
        <div id="paymentModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-chat-surface rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-gray-600">
                <!-- Modal Header -->
                <div class="sticky top-0 bg-chat-surface border-b border-gray-600 p-4 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold">Thanh toán</h3>
                        <button onclick="closePaymentModal()" class="p-2 hover:bg-gray-600 rounded-full transition-colors">
                            <span class="material-symbols-outlined text-white">close</span>
                        </button>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-gray-400 text-sm">Mã đơn hàng</span>
                        <span class="text-white font-mono">{{ $request->order_id }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-sm">Tổng thanh toán</span>
                        <span class="text-primary font-bold text-lg">{{ number_format($request->final_price) }}đ</span>
                    </div>
                </div>

                <div class="p-4 space-y-6">
                    <form id="paymentForm">
                        <!-- Customer Information -->
                        <div>
                            <h4 class="text-white font-semibold mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-sm">person</span>
                                Thông tin khách hàng
                            </h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium mb-2 text-gray-300">Họ tên</label>
                                    <input type="text" name="customer_name" required 
                                           class="w-full px-4 py-3 bg-chat-input border border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 text-white"
                                           value="{{ auth()->user()->name ?? '' }}">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2 text-gray-300">Số điện thoại</label>
                                    <input type="tel" name="customer_phone" required 
                                           class="w-full px-4 py-3 bg-chat-input border border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2 text-gray-300">Email</label>
                                    <input type="email" name="customer_email" required 
                                           class="w-full px-4 py-3 bg-chat-input border border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 text-white"
                                           value="{{ auth()->user()->email ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        <div>
                            <h4 class="text-white font-semibold mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-sm">location_on</span>
                                Địa chỉ giao hàng
                            </h4>
                            
                            <!-- Address Selection Toggle -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-gray-300 text-sm">Sử dụng địa chỉ đã lưu</span>
                                    <button type="button" onclick="toggleAddressMode()" 
                                            class="text-primary hover:text-primary/80 transition-colors text-sm font-medium">
                                        Chọn →
                                    </button>
                                </div>
                            </div>

                            <!-- New Address Form -->
                            <div id="newAddressForm" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2 text-gray-300">Họ và tên</label>
                                    <input type="text" name="recipient_name" required 
                                           class="w-full px-4 py-3 bg-chat-input border border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 text-white"
                                           placeholder="Nguyen Van A"
                                           value="{{ auth()->user()->name ?? '' }}">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-2 text-gray-300">Số điện thoại</label>
                                    <input type="tel" name="recipient_phone" required 
                                           class="w-full px-4 py-3 bg-chat-input border border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 text-white"
                                           placeholder="Nhập số điện thoại">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-2 text-gray-300">Tỉnh / Thành phố</label>
                                    <div class="relative">
                                        <button type="button" class="w-full px-4 py-3 bg-chat-input border border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 text-white text-left flex items-center justify-between" 
                                                id="provinceButton" onclick="toggleProvinceDropdown()">
                                            <span id="provinceText" class="text-gray-400">Chọn Tỉnh/Thành phố</span>
                                            <span class="material-symbols-outlined text-primary">expand_more</span>
                                        </button>
                                        <div id="provinceDropdown" class="absolute top-full left-0 right-0 bg-chat-surface border border-gray-600 rounded-xl mt-1 max-h-48 overflow-y-auto z-10 hidden">
                                            <!-- Provinces will be loaded here -->
                                        </div>
                                        <input type="hidden" name="province_id" id="provinceValue" required>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-2 text-gray-300">Xã / Phường</label>
                                    <div class="relative">
                                        <button type="button" class="w-full px-4 py-3 bg-chat-input border border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 text-white text-left flex items-center justify-between" 
                                                id="wardButton" onclick="toggleWardDropdown()" disabled>
                                            <span id="wardText" class="text-gray-400">Chọn Xã/Phường</span>
                                            <span class="material-symbols-outlined text-primary">expand_more</span>
                                        </button>
                                        <div id="wardDropdown" class="absolute top-full left-0 right-0 bg-chat-surface border border-gray-600 rounded-xl mt-1 max-h-48 overflow-y-auto z-10 hidden">
                                            <!-- Wards will be loaded here -->
                                        </div>
                                        <input type="hidden" name="ward_id" id="wardValue" required>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-2 text-gray-300">Địa chỉ cụ thể</label>
                                    <textarea name="specific_address" required rows="3"
                                              class="w-full px-4 py-3 bg-chat-input border border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 text-white"
                                              placeholder="Số nhà, tên đường, tòa nhà"></textarea>
                                </div>

                                <div class="flex items-center justify-between p-3 bg-chat-input rounded-xl">
                                    <div>
                                        <p class="text-white font-medium text-sm">Lưu địa chỉ này</p>
                                        <p class="text-gray-400 text-xs">Lưu trong sổ địa chỉ của bạn</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="save_address" class="sr-only peer" checked>
                                        <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                    </label>
                                </div>
                            </div>

                            <!-- Selected Address Display -->
                            <div id="selectedAddressDisplay" class="hidden">
                                <div class="bg-chat-input rounded-xl p-4 mb-3">
                                    <div id="selectedAddressInfo" class="text-white">
                                        <!-- Selected address info will be displayed here -->
                                    </div>
                                </div>
                                <button type="button" onclick="toggleAddressMode()" 
                                        class="text-primary hover:text-primary/80 transition-colors text-sm font-medium">
                                    Thay đổi địa chỉ
                                </button>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div>
                            <h4 class="text-white font-semibold mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-sm">payment</span>
                                Thông tin thanh toán
                            </h4>
                            
                            <!-- QR Code -->
                            <div class="bg-primary/10 rounded-xl p-4 text-center mb-4">
                                <div class="w-32 h-32 bg-white rounded-lg mx-auto mb-3 flex items-center justify-center overflow-hidden">
                                    <img src="{{ asset('QR nhận tiền.jpg') }}" alt="QR Code thanh toán" class="w-full h-full object-contain"/>
                                </div>
                                <p class="text-white text-sm mb-1">Quét mã QR để thanh toán nhanh</p>
                                <p class="text-gray-400 text-xs">Chuyển khoản với thông tin bên dưới</p>
                            </div>

                            <!-- Bank Details -->
                            <div class="space-y-3">
                                <div class="flex items-center gap-3 p-3 bg-chat-input rounded-xl">
                                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">VCB</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-white font-medium">NGUYỄN THÀNH LONG</div>
                                        <div class="text-gray-400 text-sm">Vietcombank</div>
                                    </div>
                                </div>
                                
                                <div class="bg-chat-input rounded-xl p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-gray-400 text-sm">SỐ TÀI KHOẢN</span>
                                        <button type="button" onclick="copyToClipboard('1040814582')" class="text-primary text-sm">
                                            <span class="material-symbols-outlined text-lg">content_copy</span>
                                        </button>
                                    </div>
                                    <div class="text-white font-mono text-lg">1040814582</div>
                                </div>
                                
                                <div class="bg-chat-input rounded-xl p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-gray-400 text-sm">SỐ TIỀN</span>
                                        <button type="button" onclick="copyToClipboard('{{ $request->final_price }}')" class="text-primary text-sm">
                                            <span class="material-symbols-outlined text-lg">content_copy</span>
                                        </button>
                                    </div>
                                    <div class="text-white font-mono text-lg">{{ number_format($request->final_price) }}đ</div>
                                </div>
                                
                                <div class="bg-chat-input rounded-xl p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-gray-400 text-sm">NỘI DUNG CHUYỂN KHOẢN</span>
                                        <button type="button" onclick="copyToClipboard('LENLAB {{ $request->order_id }}')" class="text-primary text-sm">
                                            <span class="material-symbols-outlined text-lg">content_copy</span>
                                        </button>
                                    </div>
                                    <div class="text-white font-mono text-lg">LENLAB {{ $request->order_id }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Confirmation -->
                        <div>
                            <h4 class="text-white font-semibold mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-sm">receipt</span>
                                Xác nhận chuyển khoản
                            </h4>
                            
                            <div class="bg-chat-input rounded-xl p-4 text-center">
                                <div id="imagePreviewContainer" class="hidden mb-4">
                                    <img id="imagePreview" src="" alt="Ảnh xác nhận chuyển khoản" class="w-full max-w-xs mx-auto rounded-lg border border-gray-600"/>
                                    <p class="text-green-400 text-sm mt-2 flex items-center justify-center gap-1">
                                        <span class="material-symbols-outlined text-lg">check_circle</span>
                                        Đã chọn ảnh xác nhận
                                    </p>
                                </div>
                                <div id="uploadPrompt">
                                    <span class="material-symbols-outlined text-primary text-3xl mb-2">upload</span>
                                    <p class="text-white font-medium mb-2">Tải lên ảnh xác nhận</p>
                                    <p class="text-gray-400 text-sm mb-4">Chụp ảnh màn hình giao dịch để xác nhận</p>
                                </div>
                                <input type="file" id="paymentBillImage" name="payment_bill_image" accept="image/*" class="hidden" onchange="handlePaymentImageUpload(event)" required>
                                <button type="button" id="selectImageBtn" onclick="document.getElementById('paymentBillImage').click()" 
                                        class="bg-primary text-white px-6 py-2 rounded-full font-medium hover:bg-primary/90 transition-colors">
                                    Chọn ảnh
                                </button>
                                <p class="text-gray-400 text-xs mt-2">
                                    <span class="text-red-400">*</span> Bắt buộc - JPG, PNG (tối đa 5MB)
                                </p>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="sticky bottom-0 bg-chat-surface border-t border-gray-600 p-4 rounded-b-2xl">
                    <div class="flex gap-3">
                        <button type="button" onclick="closePaymentModal()" 
                                class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-xl font-medium transition-colors">
                            Hủy
                        </button>
                        <button type="submit" form="paymentForm"
                                class="flex-1 bg-primary hover:bg-primary/90 text-white px-4 py-3 rounded-xl font-semibold transition-colors">
                            Xác nhận thanh toán
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Selection Modal -->
        <div id="addressModal" class="fixed inset-0 z-[60] hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-chat-surface rounded-2xl w-full max-w-md max-h-[70vh] overflow-y-auto border border-gray-600">
                <div class="sticky top-0 bg-chat-surface border-b border-gray-600 p-4 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold">Chọn địa chỉ</h3>
                        <button onclick="closeAddressModal()" class="p-2 hover:bg-gray-600 rounded-full transition-colors">
                            <span class="material-symbols-outlined text-white">close</span>
                        </button>
                    </div>
                </div>
                <div id="addressList" class="p-4">
                    <!-- Address list will be populated here -->
                </div>
            </div>
        </div>
    @endif

    <script>
    // Tab switching
    document.getElementById('infoTab').addEventListener('click', function() {
        showInfoPanel();
    });

    document.getElementById('chatTab').addEventListener('click', function() {
        showChatPanel();
    });

    function showInfoPanel() {
        document.getElementById('infoPanel').classList.remove('hidden');
        document.getElementById('chatPanel').classList.add('hidden');
        document.getElementById('infoTab').classList.add('bg-chat-input', 'text-white');
        document.getElementById('infoTab').classList.remove('text-gray-400');
        document.getElementById('chatTab').classList.remove('bg-chat-input', 'text-white');
        document.getElementById('chatTab').classList.add('text-gray-400');
    }

    function showChatPanel() {
        document.getElementById('infoPanel').classList.add('hidden');
        document.getElementById('chatPanel').classList.remove('hidden');
        document.getElementById('chatPanel').classList.add('flex');
        document.getElementById('chatTab').classList.add('bg-chat-input', 'text-white');
        document.getElementById('chatTab').classList.remove('text-gray-400');
        document.getElementById('infoTab').classList.remove('bg-chat-input', 'text-white');
        document.getElementById('infoTab').classList.add('text-gray-400');
        
        // Auto scroll to bottom when switching to chat
        setTimeout(() => {
            const chatMessages = document.getElementById('chatMessages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 100);
    }

    // Auto-resize textarea
    document.getElementById('messageInput').addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    // Send message
    document.getElementById('messageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const messageInput = document.getElementById('messageInput');
        const message = messageInput.value.trim();
        
        if (!message) return;
        
        // Add message to chat immediately
        addMessageToChat('customer', message);
        messageInput.value = '';
        messageInput.style.height = 'auto';
        
        // Send to server
        fetch('/api/chat-support/send-message', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                custom_request_id: formData.get('custom_request_id'),
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Error sending message:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    // Payment form
    @if($request->status === 'awaiting_payment')
    let userAddresses = [];
    let selectedAddressId = null;
    let provinces = [];
    let wards = [];
    let isUsingNewAddress = true;

    // Load provinces and user addresses
    function loadProvinces() {
        fetch('/api/provinces', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                provinces = data.provinces || [];
                displayProvinces();
            }
        })
        .catch(error => {
            console.error('Error loading provinces:', error);
        });
    }

    function displayProvinces() {
        const dropdown = document.getElementById('provinceDropdown');
        let html = '';
        provinces.forEach(province => {
            html += `<div class="px-4 py-2 hover:bg-gray-600 cursor-pointer text-white" onclick="selectProvince(${province.id}, '${province.name}')">${province.name}</div>`;
        });
        dropdown.innerHTML = html;
    }

    function selectProvince(provinceId, provinceName) {
        document.getElementById('provinceValue').value = provinceId;
        document.getElementById('provinceText').textContent = provinceName;
        document.getElementById('provinceText').classList.remove('text-gray-400');
        document.getElementById('provinceText').classList.add('text-white');
        document.getElementById('provinceDropdown').classList.add('hidden');
        
        // Enable ward selection and load wards
        document.getElementById('wardButton').disabled = false;
        document.getElementById('wardButton').classList.remove('opacity-50');
        loadWards(provinceId);
        
        // Reset ward selection
        document.getElementById('wardValue').value = '';
        document.getElementById('wardText').textContent = 'Chọn Xã/Phường';
        document.getElementById('wardText').classList.remove('text-white');
        document.getElementById('wardText').classList.add('text-gray-400');
    }

    function loadWards(provinceId) {
        fetch(`/api/provinces/${provinceId}/wards`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                wards = data.wards || [];
                displayWards();
            }
        })
        .catch(error => {
            console.error('Error loading wards:', error);
        });
    }

    function displayWards() {
        const dropdown = document.getElementById('wardDropdown');
        let html = '';
        wards.forEach(ward => {
            html += `<div class="px-4 py-2 hover:bg-gray-600 cursor-pointer text-white" onclick="selectWard(${ward.id}, '${ward.name}')">${ward.name}</div>`;
        });
        dropdown.innerHTML = html;
    }

    function selectWard(wardId, wardName) {
        document.getElementById('wardValue').value = wardId;
        document.getElementById('wardText').textContent = wardName;
        document.getElementById('wardText').classList.remove('text-gray-400');
        document.getElementById('wardText').classList.add('text-white');
        document.getElementById('wardDropdown').classList.add('hidden');
    }

    function toggleProvinceDropdown() {
        const dropdown = document.getElementById('provinceDropdown');
        dropdown.classList.toggle('hidden');
        
        // Close ward dropdown if open
        document.getElementById('wardDropdown').classList.add('hidden');
    }

    function toggleWardDropdown() {
        if (document.getElementById('wardButton').disabled) return;
        
        const dropdown = document.getElementById('wardDropdown');
        dropdown.classList.toggle('hidden');
        
        // Close province dropdown if open
        document.getElementById('provinceDropdown').classList.add('hidden');
    }

    // Load user addresses
    function loadUserAddresses() {
        fetch('/api/user/addresses', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                userAddresses = data.addresses || [];
            }
        })
        .catch(error => {
            console.error('Error loading addresses:', error);
        });
    }

    function toggleAddressMode() {
        if (isUsingNewAddress) {
            // Switch to saved addresses
            if (userAddresses.length === 0) {
                alert('Chưa có địa chỉ đã lưu. Vui lòng nhập địa chỉ mới.');
                return;
            }
            showAddressSelection();
        } else {
            // Switch to new address
            isUsingNewAddress = true;
            document.getElementById('newAddressForm').classList.remove('hidden');
            document.getElementById('selectedAddressDisplay').classList.add('hidden');
            selectedAddressId = null;
        }
    }

    function showAddressSelection() {
        if (userAddresses.length === 0) {
            loadUserAddresses();
            setTimeout(() => {
                if (userAddresses.length === 0) {
                    alert('Chưa có địa chỉ đã lưu. Vui lòng nhập địa chỉ mới.');
                    return;
                }
                displayAddressList();
            }, 500);
        } else {
            displayAddressList();
        }
    }

    function displayAddressList() {
        const addressList = document.getElementById('addressList');
        let html = '';
        
        if (userAddresses.length === 0) {
            html = '<p class="text-gray-400 text-center py-4">Chưa có địa chỉ đã lưu</p>';
        } else {
            userAddresses.forEach(address => {
                html += `
                    <div class="address-card cursor-pointer p-3 bg-chat-input rounded-xl mb-3 hover:bg-gray-600 transition-colors" onclick="selectAddress(${address.id})">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="text-white font-medium mb-1">${address.recipient_name || address.full_name}</div>
                                <div class="text-gray-300 text-sm mb-1">${address.phone}</div>
                                <div class="text-gray-400 text-sm">${address.full_address}</div>
                            </div>
                            ${address.is_default ? '<span class="bg-primary text-white text-xs px-2 py-1 rounded-full">Mặc định</span>' : ''}
                        </div>
                    </div>
                `;
            });
        }
        
        addressList.innerHTML = html;
        document.getElementById('addressModal').classList.remove('hidden');
    }

    function selectAddress(addressId) {
        const address = userAddresses.find(addr => addr.id === addressId);
        if (address) {
            selectedAddressId = addressId;
            isUsingNewAddress = false;
            
            // Hide new address form and show selected address
            document.getElementById('newAddressForm').classList.add('hidden');
            document.getElementById('selectedAddressDisplay').classList.remove('hidden');
            
            // Display selected address info
            const addressInfo = document.getElementById('selectedAddressInfo');
            addressInfo.innerHTML = `
                <div class="font-medium mb-1">${address.recipient_name || address.full_name}</div>
                <div class="text-sm text-gray-300 mb-1">${address.phone}</div>
                <div class="text-sm text-gray-400">${address.full_address}</div>
            `;
            
            closeAddressModal();
        }
    }

    function closeAddressModal() {
        document.getElementById('addressModal').classList.add('hidden');
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

    function handlePaymentImageUpload(event) {
        const file = event.target.files[0];
        if (file) {
            // Validate file size (max 5MB)
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                alert('Kích thước ảnh không được vượt quá 5MB!');
                event.target.value = '';
                return;
            }

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

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('#provinceButton') && !event.target.closest('#provinceDropdown')) {
            document.getElementById('provinceDropdown').classList.add('hidden');
        }
        if (!event.target.closest('#wardButton') && !event.target.closest('#wardDropdown')) {
            document.getElementById('wardDropdown').classList.add('hidden');
        }
    });

    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('request_id', {{ $request->id }});
        
        // Validate required fields
        const customerName = formData.get('customer_name').trim();
        const customerPhone = formData.get('customer_phone').trim();
        const customerEmail = formData.get('customer_email').trim();
        const paymentBillImage = formData.get('payment_bill_image');
        
        if (!customerName) {
            alert('Vui lòng nhập họ tên!');
            return;
        }
        
        if (!customerPhone) {
            alert('Vui lòng nhập số điện thoại!');
            return;
        }
        
        if (!customerEmail) {
            alert('Vui lòng nhập email!');
            return;
        }
        
        // Validate address
        let shippingAddress = '';
        if (isUsingNewAddress) {
            const recipientName = formData.get('recipient_name').trim();
            const recipientPhone = formData.get('recipient_phone').trim();
            const provinceId = formData.get('province_id');
            const wardId = formData.get('ward_id');
            const specificAddress = formData.get('specific_address').trim();
            
            if (!recipientName || !recipientPhone || !provinceId || !wardId || !specificAddress) {
                alert('Vui lòng điền đầy đủ thông tin địa chỉ giao hàng!');
                return;
            }
            
            const provinceName = provinces.find(p => p.id == provinceId)?.name || '';
            const wardName = wards.find(w => w.id == wardId)?.name || '';
            shippingAddress = `${recipientName}, ${recipientPhone}, ${specificAddress}, ${wardName}, ${provinceName}`;
            
            // Add address data to form
            formData.set('shipping_address', shippingAddress);
            formData.set('address_data', JSON.stringify({
                recipient_name: recipientName,
                phone: recipientPhone,
                province_id: provinceId,
                ward_id: wardId,
                specific_address: specificAddress,
                save_address: formData.get('save_address') === 'on'
            }));
        } else {
            if (!selectedAddressId) {
                alert('Vui lòng chọn địa chỉ giao hàng!');
                return;
            }
            
            const selectedAddress = userAddresses.find(addr => addr.id === selectedAddressId);
            if (selectedAddress) {
                shippingAddress = selectedAddress.full_address;
                formData.set('shipping_address', shippingAddress);
                formData.set('selected_address_id', selectedAddressId);
            }
        }
        
        if (!paymentBillImage || paymentBillImage.size === 0) {
            alert('Vui lòng tải lên ảnh xác nhận chuyển khoản!');
            return;
        }
        
        // Show loading state
        const submitBtn = document.querySelector('button[type="submit"][form="paymentForm"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin">sync</span> Đang xử lý...';
        submitBtn.disabled = true;
        
        // Debug: Log form data
        console.log('Sending payment data:');
        for (let [key, value] of formData.entries()) {
            if (key === 'payment_bill_image') {
                console.log(key + ':', value.name, value.size + ' bytes');
            } else {
                console.log(key + ':', value);
            }
        }
        
        fetch('/api/chatbot/process-payment', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => {
            // Log response for debugging
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                alert('Thông tin thanh toán đã được gửi thành công!');
                location.reload();
            } else {
                alert('Lỗi: ' + (data.message || 'Có lỗi xảy ra'));
                console.error('Payment error:', data);
            }
        })
        .catch(error => {
            console.error('Payment request error:', error);
            alert('Có lỗi xảy ra khi gửi yêu cầu thanh toán');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Load data when modal opens
    document.addEventListener('DOMContentLoaded', function() {
        loadProvinces();
        loadUserAddresses();
    });
    @endif

    function addMessageToChat(sender, message) {
        const chatMessages = document.getElementById('chatMessages');
        const isCustomer = sender === 'customer';
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `mb-4 ${isCustomer ? 'flex justify-end' : 'flex justify-start'}`;
        
        const now = new Date();
        const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
        
        messageDiv.innerHTML = `
            <div class="max-w-[80%]">
                <div class="flex items-center gap-2 mb-1 ${isCustomer ? 'justify-end' : 'justify-start'}">
                    ${isCustomer ? `
                        <span class="text-xs text-gray-400">${timeStr}</span>
                        <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center">
                            <span class="text-white text-xs font-bold">${'{{ substr(auth()->user()->name ?? "U", 0, 1) }}'}</span>
                        </div>
                    ` : `
                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-xs font-bold">A</span>
                        </div>
                        <span class="text-xs text-gray-400">${timeStr}</span>
                    `}
                </div>
                <div class="p-3 rounded-2xl message-bubble ${isCustomer ? 'bg-primary text-white ml-8' : 'bg-chat-card text-white mr-8'}">
                    ${message}
                </div>
            </div>
        `;
        
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function showPaymentModal() {
        document.getElementById('paymentModal').classList.remove('hidden');
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
    }

    function openImageModal(imageUrl) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-[99999] bg-black/90 flex items-center justify-center p-4';
        modal.onclick = () => modal.remove();
        
        modal.innerHTML = `
            <div class="relative max-w-4xl max-h-full">
                <img src="${imageUrl}" alt="Ảnh phóng to" class="max-w-full max-h-full object-contain rounded-lg">
                <button onclick="event.stopPropagation(); this.parentElement.parentElement.remove()" 
                        class="absolute top-4 right-4 bg-black/50 text-white p-2 rounded-full hover:bg-black/70 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        `;
        
        document.body.appendChild(modal);
    }

    // Auto scroll to bottom on page load
    document.addEventListener('DOMContentLoaded', function() {
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });

    // Poll for new messages every 3 seconds
    setInterval(function() {
        fetch(`/api/chat-support/check-messages/{{ $request->id }}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.has_new_messages) {
                    data.messages.forEach(msg => {
                        addMessageToChat('admin', msg.message);
                    });
                }
            })
            .catch(error => console.error('Error checking messages:', error));
    }, 3000);
    </script>
</body>
</html>