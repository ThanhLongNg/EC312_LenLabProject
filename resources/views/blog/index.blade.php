@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Tin tức & Blog</h1>
        <a href="/" class="text-sm font-semibold text-primary hover:underline">Về trang chủ</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($posts as $post)
            <a href="{{ route('blog.show', $post->slug) }}"
               class="block rounded-2xl overflow-hidden bg-surface-dark border border-gray-700 hover:shadow-md transition">
                <div class="aspect-video bg-gray-700">
                    <img class="w-full h-full object-cover"
                         src="{{ $post->thumbnail ? asset('storage/'.$post->thumbnail) : asset('blog1.jpg') }}"
                         alt="{{ $post->title }}">
                </div>
                <div class="p-4">
                    <div class="text-xs text-gray-400 font-bold uppercase flex items-center gap-2">
                        <span class="text-primary">{{ $post->category ?? 'Blog' }}</span>
                        <span>•</span>
                        <span>{{ optional($post->published_at)->format('d/m/Y') }}</span>
                    </div>
                    <div class="mt-2 font-bold text-lg line-clamp-2 text-white">{{ $post->title }}</div>
                    <div class="mt-1 text-sm text-gray-300 line-clamp-2">{{ $post->excerpt }}</div>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $posts->links() }}
    </div>
</div>
@endsection
