<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đánh giá sản phẩm - {{ $product->name }}</title>
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
        
        .submit-btn {
            background: linear-gradient(135deg, #FAC638, #f59e0b);
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(250, 198, 56, 0.4);
        }
        
        .submit-btn:disabled {
            opacity: 0.5;
            transform: none;
            box-shadow: none;
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
            <h1 class="text-white font-semibold text-lg">Đánh giá sản phẩm</h1>
            <div class="w-8"></div>
        </div>

        <!-- Product Info -->
        <div class="p-4 border-b border-gray-700">
            <div class="flex items-center gap-3">
                <img src="/storage/products/{{ $product->image ?? 'default.jpg' }}?v={{ $product->updated_at?->timestamp ?? time() }}" 
                     alt="{{ $product->name }}" 
                     class="w-16 h-16 object-cover rounded-lg"
                     onerror="this.src='https://via.placeholder.com/64x64/FAC638/FFFFFF?text={{ urlencode(substr($product->name, 0, 2)) }}'">
                <div class="flex-1">
                    <h3 class="text-white font-semibold text-sm mb-1">{{ $product->name }}</h3>
                    <p class="text-gray-400 text-xs">Đơn hàng: {{ $order->order_id }}</p>
                    <p class="text-primary text-sm font-bold">{{ number_format($product->price) }}đ</p>
                </div>
            </div>
        </div>

        <!-- Review Form -->
        <div class="p-6">
            <form id="reviewForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                
                <!-- Rating -->
                <div class="mb-6">
                    <label class="text-white text-sm font-medium mb-3 block">Đánh giá của bạn</label>
                    <div class="flex gap-2 justify-center" id="ratingStars">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" class="rating-star text-4xl text-gray-400 hover:text-primary transition-colors" data-rating="{{ $i }}">
                                <span class="material-symbols-outlined">star_border</span>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="selectedRating" required>
                    <p class="text-center text-gray-400 text-sm mt-2" id="ratingText">Chọn số sao</p>
                </div>

                <!-- Comment -->
                <div class="mb-6">
                    <label class="text-white text-sm font-medium mb-3 block">Nhận xét chi tiết</label>
                    <textarea name="comment" required 
                              class="w-full bg-surface-dark text-white border border-gray-600 rounded-lg p-4 h-32 resize-none focus:border-primary focus:outline-none"
                              placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."></textarea>
                </div>

                <!-- Images -->
                <div class="mb-8">
                    <label class="text-white text-sm font-medium mb-3 block">Hình ảnh (tùy chọn)</label>
                    <div class="flex gap-2 mb-3 overflow-x-auto" id="imagePreview"></div>
                    <input type="file" name="images[]" multiple accept="image/*" id="imageInput" class="hidden">
                    <button type="button" onclick="document.getElementById('imageInput').click()" 
                            class="w-full border-2 border-dashed border-gray-600 rounded-lg p-6 text-gray-400 hover:border-primary hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-3xl mb-2">add_photo_alternate</span>
                        <div class="text-sm">Thêm hình ảnh</div>
                        <div class="text-xs text-gray-500 mt-1">Tối đa 5 hình ảnh</div>
                    </button>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submitBtn" class="submit-btn w-full py-4 rounded-2xl text-background-dark font-bold text-lg" disabled>
                    Gửi đánh giá
                </button>
            </form>
        </div>
    </div>

    <script>
        let selectedRating = 0;
        const ratingTexts = {
            1: 'Rất không hài lòng',
            2: 'Không hài lòng', 
            3: 'Bình thường',
            4: 'Hài lòng',
            5: 'Rất hài lòng'
        };

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
                
                $('#ratingText').text(ratingTexts[selectedRating]);
                checkFormValid();
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
                            <div class="relative flex-shrink-0">
                                <img src="${e.target.result}" class="w-20 h-20 object-cover rounded-lg">
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

            // Form validation
            $('textarea[name="comment"]').on('input', checkFormValid);

            // Submit review form
            $('#reviewForm').submit(function(e) {
                e.preventDefault();
                
                if (selectedRating === 0) {
                    alert('Vui lòng chọn số sao đánh giá');
                    return;
                }
                
                const formData = new FormData(this);
                const submitBtn = $('#submitBtn');
                
                submitBtn.prop('disabled', true).text('Đang gửi...');
                
                $.ajax({
                    url: '/api/comments',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                history.back();
                            }
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        alert(response.message || 'Có lỗi xảy ra');
                        submitBtn.prop('disabled', false).text('Gửi đánh giá');
                    }
                });
            });
        });

        function checkFormValid() {
            const rating = selectedRating > 0;
            const comment = $('textarea[name="comment"]').val().trim().length > 0;
            
            if (rating && comment) {
                $('#submitBtn').prop('disabled', false);
            } else {
                $('#submitBtn').prop('disabled', true);
            }
        }

        function removeImage(index) {
            // Reset file input to remove selected images
            $('#imageInput').val('');
            $('#imagePreview').empty();
        }
    </script>
</body>
</html>