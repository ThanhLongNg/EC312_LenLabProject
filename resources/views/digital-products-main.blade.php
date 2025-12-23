@extends('layouts.main')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Sản phẩm số</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Khám phá bộ sưu tập tài liệu, video hướng dẫn và khóa học trực tuyến về đan len
        </p>
    </div>

    <!-- Filter & Search -->
    <div class="flex flex-col md:flex-row gap-4 mb-8">
        <div class="flex-1">
            <form method="GET" action="{{ route('digital-products') }}" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Tìm kiếm sản phẩm số..." 
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary">
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        
        <div class="flex gap-2">
            <select onchange="filterByType(this.value)" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary">
                <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Tất cả loại</option>
                <option value="file" {{ request('type') == 'file' ? 'selected' : '' }}>Tài liệu</option>
                <option value="course" {{ request('type') == 'course' ? 'selected' : '' }}>Khóa học</option>
                <option value="link" {{ request('type') == 'link' ? 'selected' : '' }}>Link</option>
            </select>
            
            <select onchange="sortProducts(this.value)" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary">
                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Mới nhất</option>
                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Giá thấp</option>
                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Giá cao</option>
                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Phổ biến</option>
            </select>
        </div>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            @foreach($products as $product)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <!-- Product Image -->
                    <div class="aspect-video bg-gray-100 relative">
                        @if($product->thumbnail_url)
                            <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-file-alt text-4xl text-gray-400"></i>
                            </div>
                        @endif
                        
                        <!-- Type Badge -->
                        <div class="absolute top-3 left-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
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
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h3>
                        
                        @if($product->description)
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $product->description }}</p>
                        @endif

                        <!-- Stats -->
                        <div class="flex items-center gap-4 text-xs text-gray-500 mb-3">
                            <span><i class="fas fa-download mr-1"></i>{{ $product->purchases->count() }} lượt mua</span>
                            @if($product->review_count > 0)
                                <span><i class="fas fa-star mr-1 text-yellow-400"></i>{{ number_format($product->average_rating, 1) }}</span>
                            @endif
                        </div>

                        <!-- Price & Action -->
                        <div class="flex items-center justify-between">
                            <div class="text-lg font-bold text-primary">
                                {{ $product->formatted_price }}
                            </div>
                            <a href="{{ route('digital-product.detail', $product->id) }}" 
                               class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary/90 transition-colors">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $products->appends(request()->query())->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-file-alt text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Không tìm thấy sản phẩm số</h3>
            <p class="text-gray-600 mb-6">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
            <a href="{{ route('digital-products') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                <i class="fas fa-refresh mr-2"></i>
                Xem tất cả
            </a>
        </div>
    @endif
</div>

<script>
function filterByType(type) {
    const url = new URL(window.location);
    if (type === 'all') {
        url.searchParams.delete('type');
    } else {
        url.searchParams.set('type', type);
    }
    window.location.href = url.toString();
}

function sortProducts(sort) {
    const url = new URL(window.location);
    if (sort === 'created_at') {
        url.searchParams.delete('sort');
    } else {
        url.searchParams.set('sort', sort);
    }
    window.location.href = url.toString();
}
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