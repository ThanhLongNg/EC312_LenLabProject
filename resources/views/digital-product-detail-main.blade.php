@extends('layouts.main')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="/" class="text-gray-700 hover:text-primary">Trang chủ</a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="{{ route('digital-products') }}" class="text-gray-700 hover:text-primary">Sản phẩm số</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500">{{ $product->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <!-- Product Image -->
        <div class="aspect-video bg-gray-100 rounded-xl overflow-hidden">
            @if($product->thumbnail_url)
                <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" 
                     class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <i class="fas fa-file-alt text-6xl text-gray-400"></i>
                </div>
            @endif
        </div>

        <!-- Product Info -->
        <div class="space-y-6">
            <!-- Type Badge -->
            <div>
                <span class="px-3 py-1 text-sm font-medium rounded-full
                    @switch($product->type)
                        @case('course') bg-blue-100 text-blue-800 @break
                        @case('file') bg-green-100 text-green-800 @break
                        @default bg-purple-100 text-purple-800
                    @endswitch
                ">
                    @switch($product->type)
                        @case('course') Khóa học @break
                        @case('file') Tài liệu @break
                        @default Link
                    @endswitch
                </span>
            </div>

            <!-- Title -->
            <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>

            <!-- Stats -->
            <div class="flex items-center gap-6 text-sm text-gray-600">
                <span><i class="fas fa-download mr-2"></i>{{ $product->purchases->count() }} lượt mua</span>
                @if($product->review_count > 0)
                    <span><i class="fas fa-star mr-2 text-yellow-400"></i>{{ number_format($product->average_rating, 1) }} ({{ $product->review_count }} đánh giá)</span>
                @endif
                <span><i class="fas fa-clock mr-2"></i>{{ $product->access_days }} ngày truy cập</span>
            </div>

            <!-- Description -->
            @if($product->description)
                <div class="prose max-w-none">
                    <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>
                </div>
            @endif

            <!-- Instructions -->
            @if($product->instructions)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-900 mb-2">Hướng dẫn sử dụng:</h3>
                    <p class="text-blue-800 text-sm">{{ $product->instructions }}</p>
                </div>
            @endif

            <!-- Features -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-3">Bạn sẽ nhận được:</h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    @if($product->files && count($product->files) > 0)
                        <li><i class="fas fa-check text-green-500 mr-2"></i>{{ count($product->files) }} file tài liệu</li>
                    @endif
                    @if($product->links && count($product->links) > 0)
                        <li><i class="fas fa-check text-green-500 mr-2"></i>{{ count($product->links) }} link truy cập</li>
                    @endif
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Tải xuống tối đa {{ $product->download_limit }} lần</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Truy cập trong {{ $product->access_days }} ngày</li>
                    @if($product->auto_send_email)
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Gửi email tự động sau khi thanh toán</li>
                    @endif
                </ul>
            </div>

            <!-- Price & Purchase -->
            <div class="border-t pt-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-3xl font-bold text-primary">{{ $product->formatted_price }}</div>
                </div>
                
                <button onclick="openPurchaseModal()" 
                        class="w-full bg-primary text-white py-3 px-6 rounded-lg font-semibold hover:bg-primary/90 transition-colors">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Mua ngay
                </button>
                
                <p class="text-xs text-gray-500 mt-2 text-center">
                    Thanh toán an toàn qua chuyển khoản ngân hàng
                </p>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    @if($product->review_count > 0)
        <div class="border-t pt-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Đánh giá từ khách hàng</h2>
                <a href="{{ route('digital-products.reviews', $product->id) }}" 
                   class="text-primary hover:text-primary/80 font-medium">
                    Xem tất cả →
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($product->verifiedComments->take(4) as $comment)
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400 mr-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $comment->rating ? '' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                            <span class="text-sm text-gray-600">{{ $comment->user->name ?? 'Khách hàng' }}</span>
                        </div>
                        <p class="text-gray-700 text-sm">{{ $comment->comment }}</p>
                        <div class="text-xs text-gray-500 mt-2">{{ $comment->created_at->format('d/m/Y') }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="border-t pt-8 mt-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Sản phẩm liên quan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <div class="aspect-video bg-gray-100">
                            @if($related->thumbnail_url)
                                <img src="{{ $related->thumbnail_url }}" alt="{{ $related->name }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-file-alt text-2xl text-gray-400"></i>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $related->name }}</h3>
                            <div class="flex items-center justify-between">
                                <div class="text-lg font-bold text-primary">{{ $related->formatted_price }}</div>
                                <a href="{{ route('digital-product.detail', $related->id) }}" 
                                   class="text-primary hover:text-primary/80 text-sm font-medium">
                                    Xem →
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Purchase Modal -->
<div id="purchaseModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Mua sản phẩm số</h3>
                <button onclick="closePurchaseModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <form id="purchaseForm" class="p-6 space-y-4">
            <input type="hidden" name="digital_product_id" value="{{ $product->id }}">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Họ tên *</label>
                <input type="text" name="customer_name" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary"
                       value="{{ auth()->user()->name ?? '' }}">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                <input type="email" name="customer_email" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary"
                       value="{{ auth()->user()->email ?? '' }}">
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-medium">{{ $product->name }}</span>
                    <span class="font-bold text-primary">{{ $product->formatted_price }}</span>
                </div>
                <p class="text-sm text-gray-600">Mã đơn hàng: <span id="orderCode"></span></p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh chuyển khoản *</label>
                <input type="file" name="transfer_image" accept="image/*" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary">
                <p class="text-xs text-gray-500 mt-1">Vui lòng chụp ảnh biên lai chuyển khoản</p>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-blue-900 mb-2">Thông tin chuyển khoản:</h4>
                <div class="text-sm text-blue-800 space-y-1">
                    <p><strong>Ngân hàng:</strong> Vietcombank</p>
                    <p><strong>Số tài khoản:</strong> 1234567890</p>
                    <p><strong>Chủ tài khoản:</strong> LENLAB OFFICIAL</p>
                    <p><strong>Nội dung:</strong> <span id="transferContent"></span></p>
                </div>
            </div>
        </form>
        
        <div class="p-6 border-t border-gray-200 flex gap-3">
            <button type="button" onclick="closePurchaseModal()" 
                    class="flex-1 px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Hủy
            </button>
            <button type="submit" form="purchaseForm" 
                    class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                Xác nhận mua
            </button>
        </div>
    </div>
</div>

<script>
function openPurchaseModal() {
    // Generate order code
    const orderCode = 'DP' + Date.now().toString().slice(-6);
    document.getElementById('orderCode').textContent = orderCode;
    document.getElementById('transferContent').textContent = orderCode + ' {{ $product->name }}';
    
    // Set hidden field
    const form = document.getElementById('purchaseForm');
    let orderCodeInput = form.querySelector('input[name="order_code"]');
    if (!orderCodeInput) {
        orderCodeInput = document.createElement('input');
        orderCodeInput.type = 'hidden';
        orderCodeInput.name = 'order_code';
        form.appendChild(orderCodeInput);
    }
    orderCodeInput.value = orderCode;
    
    // Set amount
    let amountInput = form.querySelector('input[name="amount_paid"]');
    if (!amountInput) {
        amountInput = document.createElement('input');
        amountInput.type = 'hidden';
        amountInput.name = 'amount_paid';
        form.appendChild(amountInput);
    }
    amountInput.value = {{ $product->price }};
    
    document.getElementById('purchaseModal').classList.remove('hidden');
    document.getElementById('purchaseModal').classList.add('flex');
}

function closePurchaseModal() {
    document.getElementById('purchaseModal').classList.add('hidden');
    document.getElementById('purchaseModal').classList.remove('flex');
}

// Handle form submission
document.getElementById('purchaseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/api/digital-products/order', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Đơn hàng đã được tạo thành công! Chúng tôi sẽ xử lý và gửi sản phẩm qua email trong vòng 24h.');
            closePurchaseModal();
            this.reset();
        } else {
            alert('Có lỗi xảy ra: ' + data.message);
        }
    })
    .catch(error => {
        alert('Có lỗi xảy ra khi tạo đơn hàng');
        console.error('Error:', error);
    });
});

// Close modal on backdrop click
document.getElementById('purchaseModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePurchaseModal();
    }
});
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection