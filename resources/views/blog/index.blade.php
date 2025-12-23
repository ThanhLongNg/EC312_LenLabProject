<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tin tức & Blog - LENLAB</title>
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
        
        .blog-container {
            background: #1a1a1a;
            max-width: 400px;
            margin: 0 auto;
            min-height: 100vh;
        }
        
        .blog-header {
            background: rgba(45, 45, 45, 0.9);
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .post-item {
            background: rgba(45, 45, 45, 0.8);
            border-radius: 20px;
            padding: 0;
            margin: 12px 16px;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .post-item:hover {
            background: rgba(55, 55, 55, 0.9);
            transform: translateY(-2px);
        }
        
        .post-thumbnail {
            width: 100%;
            height: 200px;
            border-radius: 20px 20px 0 0;
            overflow: hidden;
            background: linear-gradient(135deg, #2d5a5a, #1a4040);
            position: relative;
        }
        
        .post-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .post-item:hover .post-thumbnail img {
            transform: scale(1.05);
        }
        
        .post-content {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        

        .post-category {
            font-size: 12px;
            color: #FAC638;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .post-category::before {
            content: '•';
            color: #FAC638;
        }
        
        .post-date {
            color: #888;
            font-size: 12px;
            font-weight: 500;
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(0, 0, 0, 0.6);
            padding: 4px 8px;
            border-radius: 8px;
            backdrop-filter: blur(4px);
        }
        
        .post-title {
            color: white;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.3;
            margin: 8px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .post-excerpt {
            color: #aaa;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .view-detail {
            color: #FAC638;
            font-size: 13px;
            font-weight: 600;
            margin-top: auto;
        }
        
        .section-header {
            padding: 20px 16px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .section-title {
            color: white;
            font-size: 18px;
            font-weight: 600;
        }
        
        .back-btn {
            color: white;
            font-size: 20px;
            cursor: pointer;
        }
        
        .search-btn {
            color: white;
            font-size: 20px;
            cursor: pointer;
        }
        
        /* Chatbot styles */
        .bg-primary {
            background-color: #FAC638 !important;
        }
        
        .text-primary {
            color: #FAC638 !important;
        }
        
        .text-background-dark {
            color: #1a1a1a !important;
        }
        
        .hover\:bg-primary\/90:hover {
            background-color: rgba(250, 198, 56, 0.9) !important;
        }
    </style>
</head>
<body>
    <div class="blog-container">
        <!-- Header -->
        <div class="blog-header">
            <span class="material-symbols-outlined back-btn" onclick="window.location.href='/'">arrow_back</span>
            <h1 style="color: white; font-size: 16px; font-weight: 600; margin: 0;">Tin tức & Blog</h1>
            <span class="material-symbols-outlined search-btn">search</span>
        </div>

        <!-- Latest Posts Section -->
        <div class="section-header">
            <h2 class="section-title">Mới nhất</h2>
        </div>

        @foreach($posts as $post)
        <a href="{{ route('blog.show', $post->slug) }}" class="post-item block">
            <div class="post-thumbnail">
                <img src="{{ $post->thumbnail ? asset('storage/'.$post->thumbnail) : asset('blog1.jpg') }}" 
                     alt="{{ $post->title }}">
                <!-- Date overlay on image -->
                <div class="post-date">{{ optional($post->published_at)->format('d/m/Y') ?? date('d/m/Y') }}</div>
            </div>
            
            <div class="post-content">
                <div class="post-category">{{ strtoupper($post->category ?? 'XU HƯỚNG') }}</div>
                
                <h3 class="post-title">{{ $post->title }}</h3>
                
                @if($post->excerpt)
                <p class="post-excerpt">{{ Str::limit($post->excerpt, 120) }}</p>
                @endif
                
                <div class="view-detail">Xem chi tiết</div>
            </div>
        </a>
        @endforeach

        @if($posts->hasPages())
        <div style="padding: 20px;">
            {{ $posts->links() }}
        </div>
        @endif
    </div>

    <!-- Chatbot Widget -->
    @include('components.chatbot')
</body>
</html>
