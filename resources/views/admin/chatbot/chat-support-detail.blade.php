@extends('admin.layout')

@section('title', 'Chat Support - Yêu cầu ' . $request->order_id)

@section('content')
<div class="bg-white dark:bg-surface-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark">
    <div class="p-6 border-b border-border-light dark:border-border-dark">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chat Support - Yêu cầu {{ $request->order_id }}</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Trao đổi trực tiếp với khách hàng: {{ $request->customer_name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.chatbot.custom-requests') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <span class="material-icons-round text-sm">arrow_back</span>
                    Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="flex h-[600px]">
        <!-- Request Info Sidebar -->
        <div class="w-1/3 border-r border-border-light dark:border-border-dark p-6 overflow-y-auto">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Thông tin yêu cầu</h3>
            
            <!-- Status -->
            <div class="mb-4">
                @php
                    $statusColors = [
                        'pending_admin_response' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                        'in_discussion' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
                        'awaiting_payment' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
                        'payment_submitted' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
                        'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                        'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                        'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                    ];
                    $colorClass = $statusColors[$request->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
                @endphp
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $colorClass }}">
                    {{ $request->status_text }}
                </span>
            </div>

            <!-- Customer Info -->
            <div class="mb-6">
                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Khách hàng</h4>
                <div class="space-y-1 text-sm">
                    <div><strong>Tên:</strong> {{ $request->customer_name }}</div>
                    @if($request->customer_phone)
                        <div><strong>SĐT:</strong> {{ $request->customer_phone }}</div>
                    @endif
                    @if($request->customer_email)
                        <div><strong>Email:</strong> {{ $request->customer_email }}</div>
                    @endif
                </div>
            </div>

            <!-- Product Info -->
            <div class="mb-6">
                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Sản phẩm</h4>
                <div class="space-y-1 text-sm">
                    <div><strong>Loại:</strong> {{ $request->product_type }}</div>
                    <div><strong>Kích thước:</strong> {{ $request->size }}</div>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Mô tả</h4>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 text-sm">
                    {{ $request->description }}
                </div>
            </div>

            <!-- Reference Images -->
            @if($request->reference_images && count($request->reference_images) > 0)
                <div class="mb-6">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Ảnh tham khảo</h4>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($request->reference_images as $image)
                            <img src="{{ asset('storage/' . $image) }}" alt="Ảnh tham khảo" 
                                 class="w-full h-20 object-cover rounded border cursor-pointer hover:opacity-80"
                                 onclick="openImageModal('{{ asset('storage/' . $image) }}')">
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Pricing Info -->
            @if($request->final_price || $request->estimated_price)
                <div class="mb-6">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Giá cả</h4>
                    <div class="space-y-1 text-sm">
                        @if($request->final_price)
                            <div class="text-green-600 dark:text-green-400 font-semibold">
                                Giá cuối: {{ number_format($request->final_price) }}đ
                            </div>
                        @elseif($request->estimated_price)
                            <div>Ước tính: {{ number_format($request->estimated_price) }}đ</div>
                        @endif
                        @if($request->estimated_completion_days)
                            <div>Thời gian: {{ $request->estimated_completion_days }} ngày</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col">
            <!-- Chat Messages -->
            <div class="flex-1 p-6 overflow-y-auto" id="chatMessages">
                @foreach($chatHistory as $message)
                    <div class="mb-4 {{ $message->sender_type === 'admin' ? 'flex justify-end' : 'flex justify-start' }}">
                        <div class="max-w-xs lg:max-w-md">
                            <div class="flex items-center gap-2 mb-1">
                                @if($message->sender_type === 'admin')
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Admin</span>
                                    <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-xs">A</span>
                                    </div>
                                @else
                                    <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center">
                                        <span class="text-white text-xs">{{ substr($request->customer_name, 0, 1) }}</span>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $request->customer_name }}</span>
                                @endif
                            </div>
                            <div class="p-3 rounded-lg {{ $message->sender_type === 'admin' ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white' }}">
                                {{ $message->message }}
                            </div>
                            <div class="text-xs text-gray-400 mt-1">
                                {{ $message->created_at->format('H:i d/m/Y') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Message Input -->
            <div class="p-6 border-t border-border-light dark:border-border-dark">
                <form id="messageForm" class="flex gap-3">
                    <input type="hidden" name="custom_request_id" value="{{ $request->id }}">
                    <div class="flex-1">
                        <textarea name="message" id="messageInput" 
                                  placeholder="Nhập tin nhắn cho khách hàng..." 
                                  class="w-full px-4 py-2 border border-border-light dark:border-border-dark rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-primary/50 dark:bg-gray-800 dark:text-white"
                                  rows="2"></textarea>
                    </div>
                    <button type="submit" 
                            class="bg-primary hover:bg-primary-hover text-white px-6 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                        <span class="material-icons-round text-sm">send</span>
                        Gửi
                    </button>
                </form>
            </div>

            <!-- Action Buttons -->
            <div class="p-6 border-t border-border-light dark:border-border-dark bg-gray-50 dark:bg-gray-800">
                <div class="flex gap-3">
                    @if($request->status === 'in_discussion')
                        <button onclick="showFinalizeModal()" 
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                            <span class="material-icons-round text-sm">check_circle</span>
                            Chốt yêu cầu & báo giá
                        </button>
                    @endif
                    
                    @if($request->status === 'payment_submitted')
                        <button onclick="confirmPayment({{ $request->id }})" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                            <span class="material-icons-round text-sm">payment</span>
                            Xác nhận thanh toán
                        </button>
                    @elseif($request->status === 'paid')
                        <div class="bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400 px-4 py-2 rounded-lg text-sm">
                            <span class="material-icons-round text-sm mr-2">check_circle</span>
                            Thanh toán đã được xác nhận - Đang sản xuất
                        </div>
                    @elseif($request->status === 'completed')
                        <div class="bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400 px-4 py-2 rounded-lg text-sm">
                            <span class="material-icons-round text-sm mr-2">task_alt</span>
                            Đơn hàng đã hoàn thành
                        </div>
                    @elseif($request->status === 'awaiting_payment')
                        <div class="bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-400 px-4 py-2 rounded-lg text-sm">
                            <span class="material-icons-round text-sm mr-2">schedule</span>
                            Chờ khách hàng thanh toán
                        </div>
                    @endif
                    
                    @if(in_array($request->status, ['pending_admin_response', 'in_discussion']))
                        <button onclick="showCancelModal()" 
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                            <span class="material-icons-round text-sm">cancel</span>
                            Kết thúc hội thoại
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Finalize Request Modal -->
<div id="finalizeModal" class="fixed inset-0 z-[9999] hidden">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-md rounded-2xl bg-white dark:bg-surface-dark shadow-xl border border-border-light dark:border-border-dark">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Chốt yêu cầu & báo giá</h3>
                <form id="finalizeForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Giá cuối cùng (VNĐ)
                        </label>
                        <input type="number" name="final_price" required min="0" step="1000"
                               class="w-full px-3 py-2 border border-border-light dark:border-border-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Thời gian hoàn thành (ngày)
                        </label>
                        <input type="number" name="estimated_completion_days" required min="1" max="365"
                               class="w-full px-3 py-2 border border-border-light dark:border-border-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeFinalizeModal()" 
                                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Hủy
                        </button>
                        <button type="submit" 
                                class="flex-1 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Chốt yêu cầu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Request Modal -->
<div id="cancelModal" class="fixed inset-0 z-[9999] hidden">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-md rounded-2xl bg-white dark:bg-surface-dark shadow-xl border border-border-light dark:border-border-dark">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Kết thúc hội thoại</h3>
                <form id="cancelForm">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Lý do kết thúc
                        </label>
                        <textarea name="reason" required rows="3"
                                  class="w-full px-3 py-2 border border-border-light dark:border-border-dark rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/50 dark:bg-gray-800 dark:text-white"
                                  placeholder="Nhập lý do kết thúc hội thoại..."></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeCancelModal()" 
                                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Hủy
                        </button>
                        <button type="submit" 
                                class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Kết thúc
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Send message
document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    // Add message to chat immediately
    addMessageToChat('admin', message);
    messageInput.value = '';
    
    // Send to server
    fetch('/admin/api/chatbot/send-message', {
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

// Finalize request
document.getElementById('finalizeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/admin/chatbot/custom-requests/{{ $request->id }}/finalize`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            final_price: formData.get('final_price'),
            estimated_completion_days: formData.get('estimated_completion_days')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeFinalizeModal();
            location.reload();
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra');
    });
});

// Cancel request
document.getElementById('cancelForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/admin/chatbot/custom-requests/{{ $request->id }}/end-conversation`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            reason: formData.get('reason')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCancelModal();
            location.reload();
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra');
    });
});

function addMessageToChat(sender, message) {
    const chatMessages = document.getElementById('chatMessages');
    const isAdmin = sender === 'admin';
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `mb-4 ${isAdmin ? 'flex justify-end' : 'flex justify-start'}`;
    
    messageDiv.innerHTML = `
        <div class="max-w-xs lg:max-w-md">
            <div class="flex items-center gap-2 mb-1">
                ${isAdmin ? `
                    <span class="text-xs text-gray-500 dark:text-gray-400">Admin</span>
                    <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-xs">A</span>
                    </div>
                ` : `
                    <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center">
                        <span class="text-white text-xs">{{ substr($request->customer_name, 0, 1) }}</span>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $request->customer_name }}</span>
                `}
            </div>
            <div class="p-3 rounded-lg ${isAdmin ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white'}">
                ${message}
            </div>
            <div class="text-xs text-gray-400 mt-1">
                Vừa xong
            </div>
        </div>
    `;
    
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function showFinalizeModal() {
    document.getElementById('finalizeModal').classList.remove('hidden');
}

function closeFinalizeModal() {
    document.getElementById('finalizeModal').classList.add('hidden');
}

function showCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}

function confirmPayment(id) {
    if (confirm('Xác nhận thanh toán cho yêu cầu này?')) {
        fetch(`/admin/chatbot/custom-requests/${id}/confirm-payment`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra');
        });
    }
}

function openImageModal(imageUrl) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-[99999] bg-black/80 flex items-center justify-center p-4';
    modal.onclick = () => modal.remove();
    
    modal.innerHTML = `
        <div class="relative max-w-4xl max-h-full">
            <img src="${imageUrl}" alt="Ảnh phóng to" class="max-w-full max-h-full object-contain rounded-lg">
            <button onclick="event.stopPropagation(); this.parentElement.parentElement.remove()" 
                    class="absolute top-4 right-4 bg-black/50 text-white p-2 rounded-full hover:bg-black/70 transition-colors">
                <span class="material-icons-round">close</span>
            </button>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Auto scroll to bottom on page load
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
});
</script>
@endsection