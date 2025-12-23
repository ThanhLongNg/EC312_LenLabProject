<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đánh giá {{ $digitalProduct->name }} - LENLAB</title>
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
</head>
<body class="bg-background-dark font-display min-h-screen flex flex-col antialiased">

<!-- Header -->
<header class="sticky top-0 z-50 w-full bg-background-dark/95 backdrop-blur-md border-b border-white/10">
    <div class="px-4 py-3 flex items-center gap-3">
        <button class="flex items-center justify-center size-10 rounded-full hover:bg-white/10 transition-colors" onclick="goBack()">
            <span class="material-symbols-outlined text-white">arrow_back</span>
        </button>
        <h1 class="text-lg font-medium text-white">Đánh giá sản phẩm</h1>
    </div>
</header>

<!-- Main Content -->
<main class="flex-grow px-4 py-6">
    <!-- Product Info -->
    <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
        <div class="flex gap-3">
            <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 bg-gray-700">
                @if($digitalProduct->thumbnail_url)
                    <img src="{{ $digitalProduct->thumbnail_url }}" alt="{{ $digitalProduct->name }}" class="w-full h-full object-cover"/>
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-xl">description</span>
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <h2 class="text-white font-medium mb-1">{{ $digitalProduct->name }}</h2>
                <p class="text-gray-400 text-sm">{{ Str::limit($digitalProduct->description, 80) }}</p>
            </div>
        </div>
    </div>

    <!-- Rating Summary -->
    <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
        <div class="flex items-center gap-4 mb-4">
            <div class="text-center">
                <div class="text-3xl font-bold text-primary">{{ number_format($averageRating, 1) }}</div>
                <div class="flex items-center justify-center gap-1 mb-1">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="material-symbols-outlined text-lg {{ $i <= $averageRating ? 'text-yellow-400' : 'text-gray-600' }}">star</span>
                    @endfor
                </div>
                <div class="text-gray-400 text-sm">{{ $reviewCount }} đánh giá</div>
            </div>
            
            <div class="flex-1">
                @foreach($ratingDistribution as $rating => $data)
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-white text-sm w-6">{{ $rating }}⭐</span>
                        <div class="flex-1 bg-gray-700 rounded-full h-2">
                            <div class="bg-primary h-2 rounded-full" style="width: {{ $data['percentage'] }}%"></div>
                        </div>
                        <span class="text-gray-400 text-sm w-8">{{ $data['count'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Write Review Button -->
    @if($canReview)
    <div class="mb-6">
        <button onclick="showReviewModal()" class="w-full bg-primary hover:bg-primary/90 text-background-dark py-3 rounded-2xl font-bold text-lg flex items-center justify-center gap-2 transition-colors">
            <span class="material-symbols-outlined">edit</span>
            Viết đánh giá của bạn
        </button>
    </div>
    @else
    <div class="mb-6">
        <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 text-center">
            <span class="material-symbols-outlined text-gray-400 text-3xl mb-2 block">lock</span>
            <p class="text-gray-400 text-sm">
                @auth
                    Bạn cần mua sản phẩm số này để có thể đánh giá.
                @else
                    Vui lòng <a href="{{ route('login') }}" class="text-primary hover:underline">đăng nhập</a> để đánh giá sản phẩm.
                @endauth
            </p>
        </div>
    </div>
    @endif

    <!-- Reviews List -->
    <div class="space-y-4">
        @forelse($comments as $comment)
            <div class="bg-surface-dark rounded-2xl p-4 border border-white/10">
                <!-- User Info -->
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                        <span class="text-background-dark font-bold text-sm">{{ strtoupper(substr($comment->user->name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="text-white font-medium">{{ $comment->user->name }}</div>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="material-symbols-outlined text-sm {{ $i <= $comment->rating ? 'text-yellow-400' : 'text-gray-600' }}">star</span>
                                @endfor
                            </div>
                            <span class="text-gray-400 text-sm">{{ $comment->created_at ? \Carbon\Carbon::parse($comment->created_at)->format('d/m/Y') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Comment Text -->
                <p class="text-gray-300 mb-3 leading-relaxed">{{ $comment->comment }}</p>
                
                <!-- Comment Images -->
                @if($comment->images->count() > 0)
                    <div class="flex gap-2 overflow-x-auto">
                        @foreach($comment->images as $image)
                            <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0">
                                <img src="{{ asset($image->image_path) }}" alt="Ảnh đánh giá" class="w-full h-full object-cover cursor-pointer" onclick="showImageModal('{{ asset($image->image_path) }}')">
                            </div>
                        @endforeach
                    </div>
                @endif
                
                <!-- Admin Reply -->
                @if($comment->replies->count() > 0)
                    <div class="mt-4 pl-4 border-l-2 border-primary/30">
                        @foreach($comment->replies as $reply)
                            <div class="bg-black/20 rounded-xl p-3">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-primary font-medium text-sm">LENLAB</span>
                                    <span class="text-gray-400 text-xs">{{ $reply->created_at ? \Carbon\Carbon::parse($reply->created_at)->format('d/m/Y') : 'N/A' }}</span>
                                </div>
                                <p class="text-gray-300 text-sm">{{ $reply->reply }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-surface-dark rounded-2xl p-8 border border-white/10 text-center">
                <span class="material-symbols-outlined text-gray-400 text-4xl mb-4">rate_review</span>
                <h3 class="text-white font-semibold mb-2">Chưa có đánh giá nào</h3>
                <p class="text-gray-400">Hãy là người đầu tiên đánh giá sản phẩm này!</p>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($comments->hasPages())
        <div class="mt-8">
            {{ $comments->links() }}
        </div>
    @endif
</main>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4" onclick="hideImageModal()">
    <img id="modalImage" src="" alt="Ảnh đánh giá" class="max-w-full max-h-full rounded-lg">
</div>

<!-- Review Modal -->
@if($canReview)
<div id="reviewModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden">
    <div class="flex items-end justify-center min-h-screen">
        <div class="bg-background-dark w-full max-w-md rounded-t-3xl p-6 transform transition-transform duration-300 translate-y-full" id="reviewModalContent">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-white text-xl font-bold">Đánh giá sản phẩm số</h3>
                <button onclick="hideReviewModal()" class="text-gray-400 hover:text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="digitalReviewForm">
                @csrf
                <input type="hidden" name="digital_product_id" value="{{ $digitalProduct->id }}">
                
                <!-- Purchase Selection -->
                <div class="mb-6">
                    <label class="text-white text-sm font-medium mb-3 block">Chọn giao dịch mua</label>
                    <select name="digital_purchase_id" required class="w-full bg-surface-dark text-white border border-gray-600 rounded-lg p-3">
                        <option value="">Chọn giao dịch...</option>
                        @foreach($eligiblePurchases as $purchase)
                            <option value="{{ $purchase->id }}">
                                {{ $purchase->order_code }} - {{ $purchase->purchased_at ? \Carbon\Carbon::parse($purchase->purchased_at)->format('d/m/Y') : 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Rating -->
                <div class="mb-6">
                    <label class="text-white text-sm font-medium mb-3 block">Đánh giá của bạn</label>
                    <div class="flex gap-2" id="digitalRatingStars">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" class="rating-star text-3xl text-gray-400 hover:text-primary transition-colors" data-rating="{{ $i }}">
                                <span class="material-symbols-outlined">star_border</span>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="digitalSelectedRating" required>
                </div>

                <!-- Comment -->
                <div class="mb-6">
                    <label class="text-white text-sm font-medium mb-3 block">Nhận xét</label>
                    <textarea name="comment" required 
                              class="w-full bg-surface-dark text-white border border-gray-600 rounded-lg p-3 h-24 resize-none"
                              placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm số này..."></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-background-dark py-3 rounded-lg font-bold transition-colors">
                    Gửi đánh giá
                </button>
            </form>
        </div>
    </div>
</div>
@endif

<script>
function goBack() {
    history.back();
}

function showImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
}

function hideImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

// Digital Review Modal Functions
function showReviewModal() {
    const modal = document.getElementById('reviewModal');
    const content = document.getElementById('reviewModalContent');
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('translate-y-full');
    }, 10);
}

function hideReviewModal() {
    const modal = document.getElementById('reviewModal');
    const content = document.getElementById('reviewModalContent');
    
    content.classList.add('translate-y-full');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Rating Stars for Digital Products
document.addEventListener('DOMContentLoaded', function() {
    const digitalStars = document.querySelectorAll('#digitalRatingStars .rating-star');
    const digitalRatingInput = document.getElementById('digitalSelectedRating');
    
    digitalStars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            digitalRatingInput.value = rating;
            
            digitalStars.forEach((s, index) => {
                const icon = s.querySelector('.material-symbols-outlined');
                if (index < rating) {
                    icon.textContent = 'star';
                    s.classList.add('text-primary');
                    s.classList.remove('text-gray-400');
                } else {
                    icon.textContent = 'star_border';
                    s.classList.remove('text-primary');
                    s.classList.add('text-gray-400');
                }
            });
        });
    });
    
    // Digital Review Form Submission
    const digitalForm = document.getElementById('digitalReviewForm');
    if (digitalForm) {
        digitalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/api/digital-reviews', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Đánh giá của bạn đã được gửi thành công!');
                    hideReviewModal();
                    location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra, vui lòng thử lại.');
            });
        });
    }
});
</script>

</body>
</html>