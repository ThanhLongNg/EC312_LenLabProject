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
        <button class="flex items-center justify-center size-10 rounded-full hover:bg-white/10 transition-colors" onclick="history.back()">
            <span class="material-symbols-outlined text-white">arrow_back</span>
        </button>
        <h1 class="text-lg font-medium text-white">Đánh giá sản phẩm</h1>
    </div>
</header>

<!-- Main Content -->
<main class="flex-grow px-4 py-6">
    <form action="{{ route('digital-products.store-review') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="digital_product_id" value="{{ $digitalProduct->id }}">
        <input type="hidden" name="digital_purchase_id" value="{{ $purchase->id }}">
        
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
                    <div class="text-primary text-sm mt-1">Đã mua: {{ $purchase->purchased_at->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Rating Section -->
        <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
            <h3 class="text-white font-semibold mb-4">Đánh giá của bạn</h3>
            
            <div class="mb-4">
                <label class="block text-gray-300 text-sm mb-2">Số sao <span class="text-red-400">*</span></label>
                <div class="flex items-center gap-2">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" onclick="setRating({{ $i }})" class="rating-star text-3xl text-gray-600 hover:text-yellow-400 transition-colors">
                            <span class="material-symbols-outlined">star</span>
                        </button>
                    @endfor
                    <span id="ratingText" class="text-gray-400 ml-2">Chọn số sao</span>
                </div>
                <input type="hidden" name="rating" id="ratingInput" required>
                @error('rating')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Comment Section -->
        <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
            <h3 class="text-white font-semibold mb-4">Nhận xét chi tiết</h3>
            
            <div class="mb-4">
                <label class="block text-gray-300 text-sm mb-2">Chia sẻ trải nghiệm của bạn <span class="text-red-400">*</span></label>
                <textarea name="comment" rows="4" required
                          class="w-full bg-black/30 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:border-primary focus:outline-none resize-none"
                          placeholder="Hãy chia sẻ cảm nhận của bạn về sản phẩm này... (tối thiểu 10 ký tự)">{{ old('comment') }}</textarea>
                @error('comment')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Images Section -->
        <div class="bg-surface-dark rounded-2xl p-4 border border-white/10 mb-6">
            <h3 class="text-white font-semibold mb-4">Thêm hình ảnh (tùy chọn)</h3>
            
            <div class="mb-4">
                <input type="file" name="images[]" multiple accept="image/*" id="imageInput" class="hidden" onchange="previewImages(event)">
                <button type="button" onclick="document.getElementById('imageInput').click()" 
                        class="w-full border-2 border-dashed border-gray-600 rounded-xl p-6 text-center hover:border-primary transition-colors">
                    <span class="material-symbols-outlined text-gray-400 text-3xl mb-2">add_photo_alternate</span>
                    <p class="text-gray-400">Chọn ảnh để tải lên</p>
                    <p class="text-gray-500 text-sm mt-1">Tối đa 5 ảnh, mỗi ảnh không quá 2MB</p>
                </button>
                
                <div id="imagePreview" class="mt-4 grid grid-cols-3 gap-2 hidden"></div>
                
                @error('images.*')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                class="w-full bg-primary hover:bg-primary/90 text-background-dark py-4 rounded-full font-bold text-lg transition-colors">
            Gửi đánh giá
        </button>
    </form>
</main>

<script>
let currentRating = 0;
const ratingTexts = {
    1: 'Rất không hài lòng',
    2: 'Không hài lòng', 
    3: 'Bình thường',
    4: 'Hài lòng',
    5: 'Rất hài lòng'
};

function setRating(rating) {
    currentRating = rating;
    document.getElementById('ratingInput').value = rating;
    document.getElementById('ratingText').textContent = ratingTexts[rating];
    
    // Update star display
    const stars = document.querySelectorAll('.rating-star');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('text-gray-600');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-600');
        }
    });
}

function previewImages(event) {
    const files = event.target.files;
    const preview = document.getElementById('imagePreview');
    
    if (files.length > 0) {
        preview.classList.remove('hidden');
        preview.innerHTML = '';
        
        Array.from(files).slice(0, 5).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-20 object-cover rounded-lg">
                    <button type="button" onclick="removeImage(${index})" 
                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-sm">
                        ×
                    </button>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    } else {
        preview.classList.add('hidden');
    }
}

function removeImage(index) {
    // This is a simplified version - in a real app you'd need to manage the file list
    document.getElementById('imageInput').value = '';
    document.getElementById('imagePreview').classList.add('hidden');
}
</script>

</body>
</html>