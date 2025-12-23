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
                @foreach($ratingDistribution as $rating => $count)
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-white text-sm w-6">{{ $rating }}⭐</span>
                        <div class="flex-1 bg-gray-700 rounded-full h-2">
                            <div class="bg-primary h-2 rounded-full" style="width: {{ $reviewCount > 0 ? ($count / $reviewCount) * 100 : 0 }}%"></div>
                        </div>
                        <span class="text-gray-400 text-sm w-8">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

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
                            <span class="text-gray-400 text-sm">{{ $comment->created_at->format('d/m/Y') }}</span>
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
                                    <span class="text-gray-400 text-xs">{{ $reply->created_at->format('d/m/Y') }}</span>
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
</script>

</body>
</html>