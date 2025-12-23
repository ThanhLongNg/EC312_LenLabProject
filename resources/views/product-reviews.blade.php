<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đánh giá & Nhận xét - {{ $product->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&family=Noto+Sans:wght@400;500;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#FAC638",
                        "background-dark": "#1a1a1a",
                        "surface-dark": "#2d2d2d",
                        "card-dark": "#333333"
                    },
                    fontFamily: {
                        "display": ["Spline Sans", "sans-serif"],
                        "body": ["Noto Sans", "sans-serif"]
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Spline Sans', sans-serif;
            background: #1a1a1a;
            min-height: 100vh;
        }
        
        .review-container {
            background: #1a1a1a;
            max-width: 400px;
            margin: 0 auto;
            min-height: 100vh;
        }
        
        .rating-bar {
            background: rgba(255, 255, 255, 0.1);
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .rating-fill {
            background: #FAC638;
            height: 100%;
            transition: width 0.3s ease;
        }
        
        .comment-card {
            background: rgba(45, 45, 45, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .comment-card:hover {
            background: rgba(60, 60, 60, 0.8);
        }
        
        .star-rating {
            color: #FAC638;
        }
        
        .write-review-btn {
            background: linear-gradient(135deg, #FAC638, #f59e0b);
            transition: all 0.3s ease;
        }
        
        .write-review-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(250, 198, 56, 0.4);
        }
        
        .filter-btn {
            background: rgba(45, 45, 45, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .filter-btn.active {
            background: #FAC638;
            color: #1a1a1a;
            border-color: #FAC638;
        }
        
        .comment-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .comment-image:hover {
            transform: scale(1.05);
        }
    </style>
</head>

<body class="bg-background-dark">
    <div class="review-container">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-700">
            <button onclick="history.back()" class="text-white hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </button>
            <h1 class="text-white font-semibold text-lg">Đánh giá & Nhận xét</h1>
            <div class="w-8"></div>
        </div>

        <!-- Rating Summary -->
        <div class="p-6 border-b border-gray-700">
            <div class="flex items-center gap-6 mb-6">
                <div class="text-center">
                    <div class="text-4xl font-bold text-white mb-1">{{ number_format($averageRating, 1) }}</div>
                    <div class="flex items-center justify-center mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="material-symbols-outlined text-primary text-sm">
                                {{ $i <= floor($averageRating) ? 'star' : ($i == ceil($averageRating) && $averageRating - floor($averageRating) >= 0.5 ? 'star_half' : 'star_border') }}
                            </span>
                        @endfor
                    </div>
                    <div class="text-gray-400 text-sm">{{ $totalComments }} đánh giá</div>
                </div>
                
                <div class="flex-1">
                    @foreach($ratingStats as $rating => $stats)
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-white text-sm w-4">{{ $rating }}</span>
                        <span class="material-symbols-outlined text-primary text-sm">star</span>
                        <div class="flex-1 rating-bar">
                            <div class="rating-fill" style="width: {{ $stats['percentage'] }}%"></div>
                        </div>
                        <span class="text-gray-400 text-sm w-8">{{ $stats['count'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Filter Buttons -->
            <div class="flex gap-2 mb-4">
                <button class="filter-btn active px-4 py-2 rounded-full text-sm font-medium" data-filter="all">
                    Tất cả
                </button>
                <button class="filter-btn px-4 py-2 rounded-full text-sm font-medium text-white" data-filter="images">
                    Có hình ảnh                 
                </button>
                @for($i = 5; $i >= 1; $i--)
                    @if($ratingStats[$i]['count'] > 0)
                    <button class="filter-btn px-4 py-2 rounded-full text-sm font-medium text-white" data-filter="{{ $i }}">
                        {{ $i }} <span class="material-symbols-outlined text-sm">star</span>
                    </button>
                    @endif
                @endfor
            </div>
        </div>

        <!-- Write Review Button -->
        @if($canReview)
        <div class="p-4">
            <button onclick="showReviewModal()" class="write-review-btn w-full py-3 rounded-2xl text-background-dark font-bold text-lg flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">edit</span>
                Viết đánh giá của bạn
            </button>
        </div>
        @else
        <div class="p-4">
            <div class="bg-surface-dark rounded-xl p-4 text-center">
                <span class="material-symbols-outlined text-gray-400 text-3xl mb-2 block">lock</span>
                <p class="text-gray-400 text-sm">
                    @auth
                        Bạn cần mua và nhận sản phẩm này để có thể đánh giá.
                    @else
                        Vui lòng <a href="{{ route('login') }}" class="text-primary hover:underline">đăng nhập</a> để đánh giá sản phẩm.
                    @endauth
                </p>
            </div>
        </div>
        @endif

        <!-- Comments List -->
        <div class="px-4 pb-20" id="commentsList">
            @forelse($comments as $comment)
            <div class="comment-card rounded-xl p-4 mb-4" data-rating="{{ $comment->rating }}" data-has-images="{{ $comment->images->count() > 0 ? 'true' : 'false' }}">
                <!-- User Info -->
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                        <span class="text-background-dark font-bold text-sm">
                            {{ strtoupper(substr($comment->user->name ?? 'U', 0, 2)) }}
                        </span>
                    </div>
                    <div class="flex-1">
                        <div class="text-white font-medium text-sm">{{ $comment->user->name ?? 'Người dùng' }}</div>
                        <div class="flex items-center gap-2">
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="material-symbols-outlined star-rating text-sm">
                                        {{ $i <= $comment->rating ? 'star' : 'star_border' }}
                                    </span>
                                @endfor
                            </div>
                            <span class="text-gray-400 text-xs">{{ $comment->created_at ? \Carbon\Carbon::parse($comment->created_at)->format('d/m/Y') : '' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Comment Text -->
                <p class="text-gray-300 text-sm leading-relaxed mb-3">{{ $comment->comment }}</p>

                <!-- Comment Images -->
                @if($comment->images->count() > 0)
                <div class="flex gap-2 mb-3 overflow-x-auto">
                    @foreach($comment->images as $image)
                        <img src="/comment-images/{{ $image->image_path }}" 
                             alt="Hình ảnh đánh giá" 
                             class="comment-image"
                             onclick="showImageModal('{{ $image->image_path }}')">
                    @endforeach
                </div>
                @endif

                <!-- Helpful Actions -->
                <!-- Removed helpful actions as requested -->

                <!-- Admin Replies -->
                @foreach($comment->replies as $reply)
                <div class="mt-3 ml-4 p-3 bg-surface-dark/50 rounded-lg border-l-2 border-primary">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-primary text-sm font-medium">{{ $reply->admin->name ?? 'Admin' }}</span>
                        <span class="text-gray-400 text-xs">{{ $reply->created_at ? \Carbon\Carbon::parse($reply->created_at)->format('d/m/Y') : '' }}</span>
                    </div>
                    <p class="text-gray-300 text-sm">{{ $reply->reply }}</p>
                </div>
                @endforeach
            </div>
            @empty
            <div class="text-center py-12">
                <span class="material-symbols-outlined text-gray-500 text-6xl mb-4">rate_review</span>
                <p class="text-gray-400 text-lg mb-2">Chưa có đánh giá nào</p>
                <p class="text-gray-500 text-sm">Hãy là người đầu tiên đánh giá sản phẩm này!</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Review Modal -->
    @if($canReview)
    <div id="reviewModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-end justify-center min-h-screen">
            <div class="bg-background-dark w-full max-w-md rounded-t-3xl p-6 transform transition-transform duration-300 translate-y-full" id="reviewModalContent">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-white text-xl font-bold">Đánh giá sản phẩm</h3>
                    <button onclick="hideReviewModal()" class="text-gray-400 hover:text-white">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="reviewForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    
                    <!-- Order Selection -->
                    <div class="mb-6">
                        <label class="text-white text-sm font-medium mb-3 block">Chọn đơn hàng</label>
                        <select name="order_id" required class="w-full bg-surface-dark text-white border border-gray-600 rounded-lg p-3">
                            <option value="">Chọn đơn hàng...</option>
                            @foreach($eligibleOrders as $order)
                                <option value="{{ $order->order_id }}">
                                    {{ $order->order_id }} - {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Rating -->
                    <div class="mb-6">
                        <label class="text-white text-sm font-medium mb-3 block">Đánh giá của bạn</label>
                        <div class="flex gap-2" id="ratingStars">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" class="rating-star text-3xl text-gray-400 hover:text-primary transition-colors" data-rating="{{ $i }}">
                                    <span class="material-symbols-outlined">star_border</span>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="selectedRating" required>
                    </div>

                    <!-- Comment -->
                    <div class="mb-6">
                        <label class="text-white text-sm font-medium mb-3 block">Nhận xét</label>
                        <textarea name="comment" required 
                                  class="w-full bg-surface-dark text-white border border-gray-600 rounded-lg p-3 h-24 resize-none"
                                  placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."></textarea>
                    </div>

                    <!-- Images -->
                    <div class="mb-6">
                        <label class="text-white text-sm font-medium mb-3 block">Hình ảnh (tùy chọn)</label>
                        <div class="flex gap-2 mb-3" id="imagePreview"></div>
                        <input type="file" name="images[]" multiple accept="image/*" id="imageInput" class="hidden">
                        <button type="button" onclick="document.getElementById('imageInput').click()" 
                                class="w-full border-2 border-dashed border-gray-600 rounded-lg p-4 text-gray-400 hover:border-primary hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-2xl mb-2">add_photo_alternate</span>
                            <div class="text-sm">Thêm hình ảnh</div>
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="write-review-btn w-full py-3 rounded-2xl text-background-dark font-bold text-lg">
                        Gửi đánh giá
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center">
        <div class="relative max-w-full max-h-full p-4">
            <button onclick="hideImageModal()" class="absolute top-4 right-4 text-white text-2xl z-10">
                <span class="material-symbols-outlined">close</span>
            </button>
            <img id="modalImage" src="" alt="Hình ảnh đánh giá" class="max-w-full max-h-full object-contain">
        </div>
    </div>

    <script>
        let selectedRating = 0;

        $(document).ready(function() {
            // Rating star selection
            $('.rating-star').click(function() {
                selectedRating = $(this).data('rating');
                $('#selectedRating').val(selectedRating);
                
                $('.rating-star').each(function(index) {
                    const star = $(this).find('span');
                    if (index < selectedRating) {
                        star.text('star').removeClass('text-gray-400').addClass('text-primary');
                    } else {
                        star.text('star_border').removeClass('text-primary').addClass('text-gray-400');
                    }
                });
            });

            // Image preview
            $('#imageInput').change(function() {
                const files = this.files;
                const preview = $('#imagePreview');
                preview.empty();

                for (let i = 0; i < Math.min(files.length, 5); i++) {
                    const file = files[i];
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        preview.append(`
                            <div class="relative">
                                <img src="${e.target.result}" class="w-16 h-16 object-cover rounded-lg">
                                <button type="button" onclick="removeImage(${i})" 
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">
                                    ×
                                </button>
                            </div>
                        `);
                    };
                    
                    reader.readAsDataURL(file);
                }
            });

            // Filter comments
            $('.filter-btn').click(function() {
                $('.filter-btn').removeClass('active').addClass('text-white');
                $(this).addClass('active').removeClass('text-white');
                
                const filter = $(this).data('filter');
                filterComments(filter);
            });

            // Submit review form
            $('#reviewForm').submit(function(e) {
                e.preventDefault();
                
                if (selectedRating === 0) {
                    alert('Vui lòng chọn số sao đánh giá');
                    return;
                }
                
                const formData = new FormData(this);
                
                $.ajax({
                    url: '/api/comments',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            window.location.reload();
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        alert(response.message || 'Có lỗi xảy ra');
                    }
                });
            });
        });

        function showReviewModal() {
            $('#reviewModal').removeClass('hidden');
            setTimeout(() => {
                $('#reviewModalContent').removeClass('translate-y-full');
            }, 10);
        }

        function hideReviewModal() {
            $('#reviewModalContent').addClass('translate-y-full');
            setTimeout(() => {
                $('#reviewModal').addClass('hidden');
            }, 300);
        }

        function showImageModal(imagePath) {
            $('#modalImage').attr('src', '/comment-images/' + imagePath);
            $('#imageModal').removeClass('hidden');
        }

        function hideImageModal() {
            $('#imageModal').addClass('hidden');
        }

        function filterComments(filter) {
            $('.comment-card').each(function() {
                const $card = $(this);
                const rating = $card.data('rating');
                const hasImages = $card.data('has-images');
                
                let show = true;
                
                if (filter === 'images') {
                    show = hasImages === true;
                } else if (filter !== 'all') {
                    show = rating == filter;
                }
                
                if (show) {
                    $card.show();
                } else {
                    $card.hide();
                }
            });
        }

        function removeImage(index) {
            // Reset file input to remove selected images
            $('#imageInput').val('');
            $('#imagePreview').empty();
        }
    </script>
</body>
</html>