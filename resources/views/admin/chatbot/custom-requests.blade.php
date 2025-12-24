@extends('admin.layout')

@section('title', 'Quản lý yêu cầu sản phẩm cá nhân hóa')

@section('content')
<div class="bg-white dark:bg-surface-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark">
    <div class="p-6 border-b border-border-light dark:border-border-dark">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Yêu cầu sản phẩm cá nhân hóa</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Quản lý kho hàng và danh mục sản phẩm của bạn.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-primary/10 text-primary px-3 py-2 rounded-lg text-sm font-medium">
                    Tổng: {{ $requests->total() }} yêu cầu
                </div>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border-light dark:border-border-dark">
                        <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">ID</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Khách hàng</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Sản phẩm</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Kích thước</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Trạng thái</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Giá ước tính</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Ngày tạo</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $request)
                    <tr class="border-b border-border-light dark:border-border-dark hover:bg-gray-50 dark:hover:bg-white/5">
                        <td class="py-4 px-4">
                            <span class="font-mono text-sm bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">
                                {{ $request->order_id }}
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-primary/20 rounded-full flex items-center justify-center">
                                    <span class="text-primary text-sm font-semibold">
                                        {{ substr($request->customer_name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        {{ $request->customer_name }}
                                    </div>
                                    @if($request->customer_phone)
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $request->customer_phone }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-medium text-gray-900 dark:text-white">
                                {{ $request->product_type }}
                            </div>
                            @if($request->reference_images && count($request->reference_images) > 0)
                                <div class="text-sm text-blue-600 dark:text-blue-400 mt-1">
                                    <i class="fas fa-images"></i> {{ count($request->reference_images) }} ảnh
                                </div>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <span class="text-gray-900 dark:text-white">{{ $request->size }}</span>
                        </td>
                        <td class="py-4 px-4">
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
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $colorClass }}">
                                {{ $request->status_text }}
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            @if($request->final_price)
                                <div class="font-semibold text-green-600 dark:text-green-400">
                                    {{ number_format($request->final_price) }}đ
                                </div>
                            @elseif($request->estimated_price)
                                <div class="text-gray-600 dark:text-gray-400">
                                    ~{{ number_format($request->estimated_price) }}đ
                                </div>
                            @else
                                <span class="text-gray-400 dark:text-gray-500 italic">Chưa báo giá</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <div class="text-gray-900 dark:text-white">
                                {{ $request->created_at->format('d/m/Y') }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $request->created_at->format('H:i') }}
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-2">
                                <button onclick="viewRequest({{ $request->id }})" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-1">
                                    <span class="material-icons-round text-sm">visibility</span>
                                    Xem
                                </button>
                                @if($request->status === 'pending_admin_response')
                                    <a href="{{ route('admin.chatbot.custom-requests.respond', $request->id) }}" 
                                       class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-1">
                                        <span class="material-icons-round text-sm">reply</span>
                                        Cập nhật
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $requests->links() }}
        </div>
    </div>
</div>

<!-- Modal for viewing request details -->
<div id="requestModal" class="fixed inset-0 z-[9999] hidden">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-4xl rounded-2xl bg-white dark:bg-surface-dark shadow-xl border border-border-light dark:border-border-dark max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border-light dark:border-border-dark">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Chi tiết yêu cầu</h3>
                <button onclick="closeModal()" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5">
                    <span class="material-icons-round text-gray-500">close</span>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]" id="requestModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewRequest(id) {
    document.getElementById('requestModal').classList.remove('hidden');
    document.getElementById('requestModalBody').innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            <span class="ml-2 text-gray-600 dark:text-gray-300">Đang tải...</span>
        </div>
    `;
    
    fetch(`/admin/chatbot/custom-requests/${id}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayRequestDetails(data.request);
            } else {
                document.getElementById('requestModalBody').innerHTML = `
                    <div class="text-center py-8">
                        <div class="text-red-500 mb-2">
                            <span class="material-icons-round text-4xl">error</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300">${data.message}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('requestModalBody').innerHTML = `
                <div class="text-center py-8">
                    <div class="text-red-500 mb-2">
                        <span class="material-icons-round text-4xl">error</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">Lỗi tải dữ liệu</p>
                </div>
            `;
        });
}

function displayRequestDetails(request) {
    let imagesHtml = '';
    if (request.reference_images && request.reference_images.length > 0) {
        imagesHtml = `
            <div class="mb-6">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <span class="material-icons-round text-blue-500">photo_library</span>
                    Ảnh tham khảo (${request.reference_images.length})
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    ${request.reference_images.map((image, index) => `
                        <div class="relative group">
                            <img src="/storage/${image}" alt="Ảnh tham khảo ${index + 1}" 
                                 class="w-full h-32 object-cover rounded-lg border border-border-light dark:border-border-dark cursor-pointer hover:opacity-80 transition-opacity"
                                 onclick="openImageModal('/storage/${image}')">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors rounded-lg flex items-center justify-center">
                                <span class="material-icons-round text-white opacity-0 group-hover:opacity-100 transition-opacity">zoom_in</span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    let paymentBillHtml = '';
    if (request.payment_bill_image) {
        paymentBillHtml = `
            <div class="mb-6">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <span class="material-icons-round text-green-500">receipt</span>
                    Minh chứng thanh toán
                </h4>
                <div class="max-w-xs">
                    <img src="/storage/${request.payment_bill_image}" alt="Minh chứng thanh toán" 
                         class="w-full h-auto rounded-lg border border-border-light dark:border-border-dark cursor-pointer hover:opacity-80 transition-opacity"
                         onclick="openImageModal('/storage/${request.payment_bill_image}')">
                </div>
            </div>
        `;
    }

    let shippingAddressHtml = '';
    if (request.shipping_address) {
        const address = request.shipping_address;
        shippingAddressHtml = `
            <div class="mb-6">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <span class="material-icons-round text-purple-500">local_shipping</span>
                    Địa chỉ giao hàng
                </h4>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <p class="text-gray-900 dark:text-white">${address.full_address || 'Chưa có thông tin'}</p>
                </div>
            </div>
        `;
    }

    document.getElementById('requestModalBody').innerHTML = `
        <div class="space-y-6">
            <!-- Header Info -->
            <div class="bg-gradient-to-r from-primary/10 to-primary/5 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Yêu cầu #${request.id}</h3>
                        <p class="text-gray-600 dark:text-gray-300">Tạo lúc: ${request.created_at}</p>
                    </div>
                    <div class="text-right">
                        <div class="px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(request.status)}">
                            ${request.status_text}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                        <span class="material-icons-round text-blue-500">person</span>
                        Thông tin khách hàng
                    </h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Tên:</span>
                            <span class="text-gray-900 dark:text-white font-medium">${request.customer_name}</span>
                        </div>
                        ${request.customer_phone ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">SĐT:</span>
                                <span class="text-gray-900 dark:text-white">${request.customer_phone}</span>
                            </div>
                        ` : ''}
                        ${request.customer_email ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Email:</span>
                                <span class="text-gray-900 dark:text-white">${request.customer_email}</span>
                            </div>
                        ` : ''}
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                        <span class="material-icons-round text-green-500">inventory_2</span>
                        Thông tin sản phẩm
                    </h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Loại:</span>
                            <span class="text-gray-900 dark:text-white font-medium">${request.product_type}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Kích thước:</span>
                            <span class="text-gray-900 dark:text-white">${request.size}</span>
                        </div>
                        ${request.final_price ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Giá cuối:</span>
                                <span class="text-green-600 dark:text-green-400 font-semibold">${new Intl.NumberFormat('vi-VN').format(request.final_price)}đ</span>
                            </div>
                        ` : request.estimated_price ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Giá ước tính:</span>
                                <span class="text-gray-600 dark:text-gray-400">~${new Intl.NumberFormat('vi-VN').format(request.estimated_price)}đ</span>
                            </div>
                        ` : ''}
                        ${request.estimated_completion_days ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Thời gian:</span>
                                <span class="text-gray-900 dark:text-white">${request.estimated_completion_days} ngày</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <span class="material-icons-round text-orange-500">description</span>
                    Mô tả chi tiết
                </h4>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <p class="text-gray-900 dark:text-white whitespace-pre-line">${request.description}</p>
                </div>
            </div>

            ${imagesHtml}
            ${paymentBillHtml}
            ${shippingAddressHtml}

            <!-- Admin Notes -->
            ${request.admin_response || request.admin_notes ? `
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                        <span class="material-icons-round text-purple-500">admin_panel_settings</span>
                        Ghi chú admin
                    </h4>
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        ${request.admin_response ? `<p class="text-gray-900 dark:text-white mb-2"><strong>Phản hồi:</strong> ${request.admin_response}</p>` : ''}
                        ${request.admin_notes ? `<p class="text-gray-900 dark:text-white"><strong>Ghi chú:</strong> ${request.admin_notes}</p>` : ''}
                        ${request.admin_responded_at ? `<p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Cập nhật: ${request.admin_responded_at}</p>` : ''}
                    </div>
                </div>
            ` : ''}

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4 border-t border-border-light dark:border-border-dark">
                ${getActionButtons(request)}
            </div>
        </div>
    `;
}

function getStatusColor(status) {
    const colors = {
        'pending_admin_response': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
        'in_discussion': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
        'awaiting_payment': 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
        'payment_submitted': 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
        'paid': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
        'completed': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
        'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    };
    return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
}

function getActionButtons(request) {
    let buttons = '';
    
    if (request.status === 'pending_admin_response') {
        buttons += `
            <a href="/admin/chatbot/custom-requests/${request.id}/respond" 
               class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                <span class="material-icons-round text-sm">reply</span>
                Phản hồi
            </a>
        `;
    }
    
    if (request.status === 'in_discussion') {
        buttons += `
            <button onclick="finalizeRequest(${request.id})" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                <span class="material-icons-round text-sm">check_circle</span>
                Chốt yêu cầu
            </button>
        `;
    }
    
    if (request.status === 'payment_submitted') {
        buttons += `
            <button onclick="confirmPayment(${request.id})" 
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                <span class="material-icons-round text-sm">payment</span>
                Xác nhận thanh toán
            </button>
        `;
    }
    
    if (['pending_admin_response', 'in_discussion'].includes(request.status)) {
        buttons += `
            <button onclick="cancelRequest(${request.id})" 
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                <span class="material-icons-round text-sm">cancel</span>
                Hủy yêu cầu
            </button>
        `;
    }
    
    return buttons;
}

function openImageModal(imageUrl) {
    // Create image modal
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

function closeModal() {
    document.getElementById('requestModal').classList.add('hidden');
}

function finalizeRequest(id) {
    // Implementation for finalizing request
    alert('Chức năng chốt yêu cầu sẽ được triển khai');
}

function confirmPayment(id) {
    // Implementation for confirming payment
    alert('Chức năng xác nhận thanh toán sẽ được triển khai');
}

function cancelRequest(id) {
    // Implementation for canceling request
    alert('Chức năng hủy yêu cầu sẽ được triển khai');
}

// Close modal when clicking outside
document.getElementById('requestModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection