@extends('admin.layouts.app')

@section('title', 'Chat Hỗ Trợ Khách Hàng')

@section('content')
<div class="container-fluid h-100">
    <div class="row h-100">
        <!-- Sidebar - Danh sách khách hàng -->
        <div class="col-md-4 col-lg-3 p-0">
            <div class="card h-100 rounded-0">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-comments mr-2"></i>
                        <h5 class="mb-0">Chat hỗ trợ</h5>
                    </div>
                    <div class="input-group mt-2">
                        <input type="text" class="form-control form-control-sm" id="searchCustomer" placeholder="Tìm khách hàng...">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow-y: auto; max-height: calc(100vh - 200px);">
                    <div class="list-group list-group-flush" id="customerList">
                        @forelse($conversations as $conversation)
                        <div class="list-group-item list-group-item-action customer-item {{ isset($customRequest) && $customRequest->session_id === $conversation->session_id ? 'active' : '' }}" 
                             data-session-id="{{ $conversation->session_id }}"
                             data-customer-name="{{ $conversation->customer_name }}"
                             data-customer-email="{{ $conversation->customer_email }}">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle mr-3">
                                    {{ strtoupper(substr($conversation->customer_name, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="mb-1 font-weight-bold">{{ $conversation->customer_name }}</h6>
                                        <small class="text-muted">
                                            @if($conversation->last_message_at)
                                                {{ $conversation->last_message_at->format('H:i') }}
                                            @else
                                                --:--
                                            @endif
                                        </small>
                                    </div>
                                    <p class="mb-1 text-muted small">{{ Str::limit($conversation->last_message, 50) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">{{ $conversation->message_count }} tin nhắn</small>
                                        @if($conversation->last_message_at && $conversation->last_message_at->diffInHours(now()) < 1)
                                            <span class="badge badge-success badge-sm">Hoạt động</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center p-4">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có cuộc trò chuyện nào</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="col-md-8 col-lg-9 p-0">
            <div class="card h-100 rounded-0">
                <!-- Custom Request Info Panel (if exists) -->
                @if(isset($customRequest))
                <div class="custom-request-panel border-bottom">
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="request-icon mr-3">
                                <i class="fas fa-paint-brush text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Yêu cầu sản phẩm cá nhân hóa #{{ $customRequest->id }}</h6>
                                <small class="text-muted">{{ $customRequest->product_type }} - {{ $customRequest->size }}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @php
                                $statusConfig = match($customRequest->status) {
                                    'pending_admin_response' => ['class' => 'warning', 'text' => 'Chờ phản hồi'],
                                    'in_discussion' => ['class' => 'info', 'text' => 'Đang trao đổi'],
                                    'awaiting_deposit' => ['class' => 'primary', 'text' => 'Chờ đặt cọc'],
                                    'deposit_paid' => ['class' => 'success', 'text' => 'Đã đặt cọc'],
                                    'ready_for_final_payment' => ['class' => 'warning', 'text' => 'Chờ thanh toán cuối'],
                                    'completed' => ['class' => 'success', 'text' => 'Hoàn thành'],
                                    'cancelled' => ['class' => 'danger', 'text' => 'Đã hủy'],
                                    default => ['class' => 'secondary', 'text' => 'Không xác định']
                                };
                            @endphp
                            <span class="badge badge-{{ $statusConfig['class'] }}">{{ $statusConfig['text'] }}</span>
                            <button class="btn btn-sm btn-outline-secondary" data-toggle="collapse" data-target="#requestDetails">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Collapsible Request Details -->
                    <div class="collapse" id="requestDetails">
                        <div class="p-3 bg-white border-top">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="request-details">
                                        <p class="mb-2"><strong>Mô tả:</strong> {{ $customRequest->description }}</p>
                                        @if($customRequest->estimated_price)
                                            <p class="mb-2"><strong>Giá ước tính:</strong> {{ number_format($customRequest->estimated_price) }}đ</p>
                                        @endif
                                        @if($customRequest->deposit_amount)
                                            <p class="mb-2"><strong>Tiền đặt cọc:</strong> {{ number_format($customRequest->deposit_amount) }}đ</p>
                                        @endif
                                        @if($customRequest->estimated_completion_days)
                                            <p class="mb-0"><strong>Thời gian hoàn thành:</strong> {{ $customRequest->estimated_completion_days }} ngày</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    @if($customRequest->reference_images && count($customRequest->reference_images) > 0)
                                        <div class="reference-images">
                                            <small class="text-muted d-block mb-2">Ảnh tham khảo:</small>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach(array_slice($customRequest->reference_images, 0, 3) as $image)
                                                    <img src="/storage/{{ $image }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                @endforeach
                                                @if(count($customRequest->reference_images) > 3)
                                                    <div class="img-thumbnail d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                        <small>+{{ count($customRequest->reference_images) - 3 }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons for Admin -->
                    @if($customRequest->canFinalize())
                    <div class="action-buttons-panel p-3 bg-light border-top">
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-danger btn-sm" onclick="cancelRequest()">
                                <i class="fas fa-times mr-1"></i> Kết thúc hội thoại
                            </button>
                            <button class="btn btn-success btn-sm" onclick="finalizeRequest()">
                                <i class="fas fa-check mr-1"></i> Chốt yêu cầu & báo giá
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Chat Header -->
                <div class="card-header bg-light" id="chatHeader" style="{{ isset($customRequest) ? '' : 'display: none;' }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle mr-3" id="customerAvatar">
                                @if(isset($customRequest) && $customRequest->user)
                                    {{ strtoupper(substr($customRequest->user->name, 0, 1)) }}
                                @else
                                    N
                                @endif
                            </div>
                            <div>
                                <h6 class="mb-0" id="customerName">
                                    @if(isset($customRequest) && $customRequest->user)
                                        {{ $customRequest->user->name }}
                                    @else
                                        Nguyễn Thùy Linh
                                    @endif
                                </h6>
                                <small class="text-muted" id="customerStatus">
                                    @if(isset($customRequest) && $customRequest->user)
                                        {{ $customRequest->user->email }}
                                    @else
                                        Khách hàng thân thiết
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-primary mr-2" id="customerInfoBtn">
                                <i class="fas fa-user"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" id="chatOptionsBtn">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div class="card-body p-0 d-flex flex-column" style="height: calc(100vh - 200px);">
                    <!-- Welcome Screen -->
                    <div id="welcomeScreen" class="flex-grow-1 d-flex align-items-center justify-content-center" style="{{ isset($customRequest) ? 'display: none !important;' : '' }}">
                        <div class="text-center">
                            <i class="fas fa-comments fa-4x text-primary mb-3"></i>
                            <h4 class="text-muted">Chọn khách hàng để bắt đầu chat</h4>
                            <p class="text-muted">Hỗ trợ khách hàng trực tiếp qua chat real-time</p>
                        </div>
                    </div>

                    <!-- Chat Messages Container -->
                    <div id="chatMessages" class="flex-grow-1 p-3" style="overflow-y: auto; {{ isset($customRequest) ? '' : 'display: none;' }}">
                        <!-- Messages will be loaded here -->
                    </div>

                    <!-- Chat Input -->
                    <div id="chatInput" class="border-top p-3" style="{{ isset($customRequest) ? '' : 'display: none;' }}">
                        <!-- Quick Replies -->
                        <div class="quick-replies mb-2">
                            <button class="quick-reply-btn" data-message="Xin chào! Tôi có thể giúp gì cho bạn?">
                                👋 Chào hỏi
                            </button>
                            <button class="quick-reply-btn" data-message="Cảm ơn bạn đã liên hệ. Chúng tôi sẽ xử lý yêu cầu của bạn sớm nhất.">
                                🙏 Cảm ơn
                            </button>
                            <button class="quick-reply-btn" data-message="Bạn có thể cung cấp thêm thông tin chi tiết không?">
                                ❓ Hỏi thêm
                            </button>
                            <button class="quick-reply-btn" data-message="Chúng tôi sẽ kiểm tra và phản hồi lại bạn trong 24h.">
                                ⏰ Hẹn phản hồi
                            </button>
                        </div>
                        
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button class="btn btn-outline-secondary" type="button" id="attachBtn" title="Đính kèm file">
                                    <i class="fas fa-paperclip"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control" id="messageInput" placeholder="Nhập tin nhắn..." maxlength="1000">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="sendBtn" title="Gửi tin nhắn">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Typing indicator for admin -->
                        <div class="mt-2 text-muted small" id="typingStatus" style="display: none;">
                            <i class="fas fa-keyboard"></i> Đang soạn tin nhắn...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Info Modal -->
<div class="modal fade" id="customerInfoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thông tin khách hàng</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="customerDetails">
                    <!-- Customer details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Finalize Request Modal -->
<div class="modal fade" id="finalizeRequestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check mr-2"></i>
                    Chốt yêu cầu & báo giá
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="finalizeForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Sau khi chốt yêu cầu, chatbot sẽ tự động gửi thông báo đặt cọc cho khách hàng.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tổng giá sản phẩm (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="estimated_price" required min="1000" step="1000" placeholder="Ví dụ: 500000">
                                <small class="text-muted">Giá cuối cùng cho sản phẩm</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phần trăm đặt cọc (%) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="deposit_percentage" required min="10" max="100" value="30" placeholder="30">
                                <small class="text-muted">Khuyến nghị: 30-50%</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Thời gian hoàn thành (ngày) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="estimated_completion_days" required min="1" max="365" placeholder="7">
                                <small class="text-muted">Số ngày để hoàn thành sản phẩm</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Số tiền đặt cọc (tự động tính)</label>
                                <input type="text" class="form-control" id="calculatedDeposit" readonly placeholder="Sẽ tự động tính">
                                <small class="text-muted">Được tính từ tổng giá × % đặt cọc</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check mr-1"></i> Xác nhận chốt
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Request Modal -->
<div class="modal fade" id="cancelRequestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-times mr-2"></i>
                    Kết thúc hội thoại
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="cancelForm">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Hành động này sẽ kết thúc hội thoại và thông báo cho khách hàng. Không thể hoàn tác!
                    </div>
                    
                    <div class="form-group">
                        <label>Lý do kết thúc (tùy chọn)</label>
                        <textarea class="form-control" name="reason" rows="4" placeholder="Ví dụ: Yêu cầu không phù hợp với khả năng sản xuất hiện tại..."></textarea>
                        <small class="text-muted">Lý do này sẽ được gửi cho khách hàng</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-arrow-left mr-1"></i> Quay lại
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times mr-1"></i> Xác nhận kết thúc
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 16px;
}

.customer-item {
    cursor: pointer;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}

.customer-item:hover {
    background-color: #f8f9fa;
    border-left-color: #e3f2fd;
}

.customer-item.active {
    background-color: #e3f2fd;
    border-left-color: #2196f3;
}

.message-bubble {
    max-width: 70%;
    padding: 12px 16px;
    border-radius: 18px;
    margin-bottom: 8px;
    word-wrap: break-word;
    position: relative;
}

.message-customer {
    background: linear-gradient(135deg, #ff9a56 0%, #ff6b35 100%);
    color: white;
    margin-left: auto;
    border-bottom-right-radius: 5px;
}

.message-admin {
    background-color: #f1f3f4;
    color: #333;
    margin-right: auto;
    border-bottom-left-radius: 5px;
    border: 1px solid #e0e0e0;
}

.message-bot {
    background-color: #fff3e0;
    color: #f57c00;
    margin-right: auto;
    border-bottom-left-radius: 5px;
    border: 1px solid #ffcc02;
}

.message-time {
    font-size: 11px;
    color: rgba(255,255,255,0.7);
    margin-top: 4px;
    text-align: right;
}

.message-admin .message-time,
.message-bot .message-time {
    color: #999;
    text-align: left;
}

.chat-date-divider {
    text-align: center;
    margin: 20px 0;
    position: relative;
}

.chat-date-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #ddd;
}

.chat-date-divider span {
    background: white;
    padding: 0 15px;
    color: #666;
    font-size: 12px;
    font-weight: 500;
}

#chatMessages {
    background: linear-gradient(to bottom, #fafafa 0%, #ffffff 100%);
}

.typing-indicator {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    background: #f5f5f5;
    border-radius: 18px;
    margin-right: auto;
    max-width: 70px;
}

.typing-indicator span {
    height: 8px;
    width: 8px;
    border-radius: 50%;
    background: #999;
    display: inline-block;
    margin-right: 3px;
    animation: typing 1.4s infinite ease-in-out;
}

.typing-indicator span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-indicator span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-10px);
    }
}

.h-100 {
    height: 100vh !important;
}

.card.h-100 {
    height: calc(100vh - 100px) !important;
}

/* Custom scrollbar */
.customer-list-scroll::-webkit-scrollbar {
    width: 4px;
}

.customer-list-scroll::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.customer-list-scroll::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.customer-list-scroll::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Online status indicator */
.status-online {
    width: 12px;
    height: 12px;
    background: #4caf50;
    border-radius: 50%;
    position: absolute;
    bottom: 2px;
    right: 2px;
    border: 2px solid white;
}

.status-away {
    background: #ff9800;
}

.status-offline {
    background: #9e9e9e;
}

/* Message status indicators */
.message-status {
    font-size: 10px;
    margin-left: 5px;
    opacity: 0.7;
}

/* Quick reply buttons */
.quick-replies {
    display: flex;
    gap: 8px;
    margin-top: 10px;
    flex-wrap: wrap;
}

.quick-reply-btn {
    background: #e3f2fd;
    color: #1976d2;
    border: 1px solid #bbdefb;
    padding: 6px 12px;
    border-radius: 16px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.quick-reply-btn:hover {
    background: #1976d2;
    color: white;
}

/* Customer info sidebar */
.customer-info-panel {
    background: #f8f9fa;
    border-left: 1px solid #dee2e6;
    padding: 20px;
    width: 300px;
    overflow-y: auto;
}

.customer-avatar-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 32px;
    margin: 0 auto 15px;
}

.order-item {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 10px;
}

.order-item img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .customer-info-panel {
        position: fixed;
        top: 0;
        right: -300px;
        height: 100vh;
        z-index: 1000;
        transition: right 0.3s ease;
    }
    
    .customer-info-panel.show {
        right: 0;
    }
    
    .message-bubble {
        max-width: 85%;
    }
}
</style>
@endsection

@section('scripts')
<script>
let currentSessionId = null;
let currentCustomerName = '';
let currentRequestId = @json(isset($customRequest) ? $customRequest->id : null);

$(document).ready(function() {
    @if(isset($customRequest))
        // Auto-load conversation for custom request
        currentSessionId = '{{ $customRequest->session_id }}';
        currentCustomerName = '{{ $customRequest->user ? $customRequest->user->name : "Khách vãng lai" }}';
        
        // Update chat header
        $('#customerName').text(currentCustomerName);
        $('#customerAvatar').text(currentCustomerName.charAt(0).toUpperCase());
        $('#customerStatus').text('{{ $customRequest->user ? $customRequest->user->email : "Khách vãng lai" }}');
        
        // Load chat history
        loadChatHistory(currentSessionId);
    @endif

    // Customer selection
    $('.customer-item').on('click', function() {
        $('.customer-item').removeClass('active');
        $(this).addClass('active');
        
        currentSessionId = $(this).data('session-id');
        currentCustomerName = $(this).data('customer-name');
        const customerEmail = $(this).data('customer-email');
        
        // Update chat header
        $('#customerName').text(currentCustomerName);
        $('#customerAvatar').text(currentCustomerName.charAt(0).toUpperCase());
        $('#customerStatus').text(customerEmail || 'Khách vãng lai');
        
        // Show chat interface
        $('#welcomeScreen').hide();
        $('#chatHeader').show();
        $('#chatMessages').show();
        $('#chatInput').show();
        
        // Load chat history
        loadChatHistory(currentSessionId);
    });

    // Send message
    $('#sendBtn, #messageInput').on('click keypress', function(e) {
        if (e.type === 'click' || (e.type === 'keypress' && e.which === 13)) {
            sendMessage();
        }
    });

    // Customer info
    $('#customerInfoBtn').on('click', function() {
        showCustomerInfo();
    });

    // Search customers
    $('#searchCustomer').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.customer-item').each(function() {
            const customerName = $(this).data('customer-name').toLowerCase();
            const customerEmail = $(this).data('customer-email').toLowerCase();
            
            if (customerName.includes(searchTerm) || customerEmail.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Auto calculate deposit amount
    $('input[name="estimated_price"], input[name="deposit_percentage"]').on('input', function() {
        calculateDepositAmount();
    });

    // Finalize request form
    $('#finalizeForm').on('submit', function(e) {
        e.preventDefault();
        submitFinalizeRequest();
    });

    // Cancel request form
    $('#cancelForm').on('submit', function(e) {
        e.preventDefault();
        submitCancelRequest();
    });
});

function finalizeRequest() {
    if (!currentRequestId) {
        alert('Không tìm thấy yêu cầu để chốt');
        return;
    }
    $('#finalizeRequestModal').modal('show');
}

function cancelRequest() {
    if (!currentRequestId) {
        alert('Không tìm thấy yêu cầu để hủy');
        return;
    }
    $('#cancelRequestModal').modal('show');
}

function calculateDepositAmount() {
    const price = parseFloat($('input[name="estimated_price"]').val()) || 0;
    const percentage = parseFloat($('input[name="deposit_percentage"]').val()) || 30;
    const depositAmount = Math.round((price * percentage) / 100);
    
    $('#calculatedDeposit').val(depositAmount > 0 ? new Intl.NumberFormat('vi-VN').format(depositAmount) + 'đ' : '');
}

function submitFinalizeRequest() {
    const formData = new FormData(document.getElementById('finalizeForm'));
    const data = Object.fromEntries(formData);
    
    $.ajax({
        url: `/admin/chatbot/finalize-request/${currentRequestId}`,
        method: 'POST',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#finalizeRequestModal').modal('hide');
                alert('Đã chốt yêu cầu thành công! Chatbot đã gửi thông báo đặt cọc cho khách hàng.');
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr) {
            const error = xhr.responseJSON?.message || 'Có lỗi xảy ra';
            alert(error);
        }
    });
}

function submitCancelRequest() {
    const formData = new FormData(document.getElementById('cancelForm'));
    const data = Object.fromEntries(formData);
    
    $.ajax({
        url: `/admin/chatbot/cancel-request/${currentRequestId}`,
        method: 'POST',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#cancelRequestModal').modal('hide');
                alert('Đã kết thúc hội thoại và thông báo cho khách hàng.');
                window.location.href = '/admin/chatbot/custom-requests';
            } else {
                alert('Có lỗi xảy ra: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr) {
            const error = xhr.responseJSON?.message || 'Có lỗi xảy ra';
            alert(error);
        }
    });
}

function loadChatHistory(sessionId) {
    $('#chatMessages').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>');
    
    $.get('/admin/chatbot/chat-history', { session_id: sessionId })
        .done(function(response) {
            if (response.success) {
                displayMessages(response.messages);
                
                // Show customer request info if exists
                if (response.custom_request) {
                    showCustomRequestInfo(response.custom_request);
                }
            }
        })
        .fail(function() {
            $('#chatMessages').html('<div class="text-center p-4 text-danger"><i class="fas fa-exclamation-triangle"></i> Lỗi tải tin nhắn</div>');
        });
}

function displayMessages(messages) {
    let html = '';
    let currentDate = '';
    
    messages.forEach(function(message) {
        // Date divider
        if (message.date !== currentDate) {
            html += `<div class="chat-date-divider"><span>${message.date}</span></div>`;
            currentDate = message.date;
        }
        
        // Determine message type and style
        let messageClass = 'message-bot';
        let alignment = 'text-left';
        let content = '';
        
        if (message.user_message.startsWith('[ADMIN]')) {
            messageClass = 'message-admin';
            content = message.user_message.replace('[ADMIN] ', '');
        } else {
            messageClass = 'message-customer';
            alignment = 'text-right';
            content = message.user_message;
        }
        
        html += `
            <div class="${alignment}">
                <div class="message-bubble ${messageClass}">
                    ${content}
                    <div class="message-time">${message.created_at}</div>
                </div>
            </div>
        `;
        
        // Bot reply if exists
        if (message.bot_reply && !message.user_message.startsWith('[ADMIN]')) {
            html += `
                <div class="text-left">
                    <div class="message-bubble message-bot">
                        ${message.bot_reply.replace(/\n/g, '<br>')}
                        <div class="message-time">${message.created_at}</div>
                    </div>
                </div>
            `;
        }
    });
    
    $('#chatMessages').html(html);
    scrollToBottom();
}

function sendMessage() {
    const message = $('#messageInput').val().trim();
    if (!message || !currentSessionId) return;
    
    // Show typing indicator
    showTypingIndicator();
    
    // Add message to chat immediately
    const now = new Date();
    const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
    
    $('#chatMessages').append(`
        <div class="text-right">
            <div class="message-bubble message-admin">
                ${message}
                <div class="message-time">${timeStr}</div>
            </div>
        </div>
    `);
    
    $('#messageInput').val('');
    scrollToBottom();
    
    // Send to server
    $.post('/admin/chatbot/send-message', {
        session_id: currentSessionId,
        message: message,
        _token: $('meta[name="csrf-token"]').attr('content')
    })
    .done(function(response) {
        hideTypingIndicator();
        if (response.success) {
            // Message sent successfully
            console.log('Message sent');
        }
    })
    .fail(function() {
        hideTypingIndicator();
        alert('Lỗi gửi tin nhắn');
    });
}

function showTypingIndicator() {
    $('#chatMessages').append(`
        <div class="text-left typing-indicator-container">
            <div class="typing-indicator">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    `);
    scrollToBottom();
}

function hideTypingIndicator() {
    $('.typing-indicator-container').remove();
}

function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function showCustomerInfo() {
    if (!currentSessionId) return;
    
    const customerDetails = `
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="customer-avatar-large">
                    ${currentCustomerName.charAt(0).toUpperCase()}
                </div>
                <div class="position-relative d-inline-block">
                    <div class="status-online"></div>
                </div>
            </div>
            <div class="col-md-8">
                <h5>${currentCustomerName}</h5>
                <p class="text-muted">Khách hàng thân thiết</p>
                
                <div class="mt-3">
                    <h6><i class="fas fa-info-circle mr-2"></i>Thông tin liên hệ</h6>
                    <p class="mb-1"><i class="fas fa-envelope mr-2 text-primary"></i> linh.nguyen@example.com</p>
                    <p class="mb-1"><i class="fas fa-phone mr-2 text-success"></i> 090 123 4567</p>
                    <p class="mb-1"><i class="fas fa-map-marker-alt mr-2 text-danger"></i> 123 Nguyễn Huệ, Quận 1, TP.HCM</p>
                </div>
                
                <div class="mt-3">
                    <h6><i class="fas fa-shopping-bag mr-2"></i>Đơn hàng gần nhất</h6>
                    <div class="order-item">
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/50x50/ff9a56/ffffff?text=K" alt="Product" class="mr-3">
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">Khăn Len Merino Thú Cưng</div>
                                <small class="text-muted">Màu Kem - Size M</small>
                            </div>
                            <div class="text-right">
                                <div class="text-success font-weight-bold">550.000đ</div>
                                <small class="text-muted">Đã giao</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <h6><i class="fas fa-chart-line mr-2"></i>Thống kê</h6>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="font-weight-bold text-primary">12</div>
                            <small class="text-muted">Đơn hàng</small>
                        </div>
                        <div class="col-4">
                            <div class="font-weight-bold text-success">8.5M</div>
                            <small class="text-muted">Tổng chi</small>
                        </div>
                        <div class="col-4">
                            <div class="font-weight-bold text-warning">VIP</div>
                            <small class="text-muted">Hạng</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#customerDetails').html(customerDetails);
    $('#customerInfoModal').modal('show');
}

function showCustomRequestInfo(customRequest) {
    const requestInfo = `
        <div class="alert alert-info mt-3">
            <h6><i class="fas fa-paint-brush mr-2"></i>Yêu cầu sản phẩm cá nhân hóa #${customRequest.id}</h6>
            <p class="mb-1"><strong>Sản phẩm:</strong> ${customRequest.product_type}</p>
            <p class="mb-1"><strong>Kích thước:</strong> ${customRequest.size}</p>
            <p class="mb-1"><strong>Trạng thái:</strong> <span class="badge badge-warning">${customRequest.status}</span></p>
            ${customRequest.estimated_price ? `<p class="mb-0"><strong>Giá ước tính:</strong> ${new Intl.NumberFormat('vi-VN').format(customRequest.estimated_price)}đ</p>` : ''}
        </div>
    `;
    
    $('#chatMessages').prepend(requestInfo);
}

// Auto refresh customer list every 30 seconds
setInterval(function() {
    // You can implement auto-refresh logic here
}, 30000);

// Quick reply buttons
$(document).on('click', '.quick-reply-btn', function() {
    const message = $(this).data('message');
    $('#messageInput').val(message);
    sendMessage();
});

// Show typing indicator when typing
let typingTimer;
$('#messageInput').on('input', function() {
    if (currentSessionId) {
        $('#typingStatus').show();
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function() {
            $('#typingStatus').hide();
        }, 1000);
    }
});

// Auto refresh conversations every 30 seconds
setInterval(function() {
    if (currentSessionId) {
        loadChatHistory(currentSessionId);
    }
}, 30000);
</script>
@endsection