@extends('admin.layout')

@section('title', 'Chat Support - Hỗ trợ khách hàng')

@section('content')
<div class="bg-white dark:bg-surface-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark">
    <div class="p-6 border-b border-border-light dark:border-border-dark">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chat Support</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Hỗ trợ khách hàng trực tiếp</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-primary/10 text-primary px-3 py-2 rounded-lg text-sm font-medium">
                    {{ $activeRequests->count() }} yêu cầu đang hoạt động
                </div>
            </div>
        </div>
    </div>

    <div class="p-6">
        @if($activeRequests->count() > 0)
            <div class="grid gap-4">
                @foreach($activeRequests as $request)
                    <div class="border border-border-light dark:border-border-dark rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-primary/20 rounded-full flex items-center justify-center">
                                    <span class="text-primary text-lg font-semibold">
                                        {{ substr($request->customer_name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">
                                        Yêu cầu {{ $request->order_id }} - {{ $request->customer_name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $request->product_type }} - {{ $request->size }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1">
                                        @php
                                            $statusColors = [
                                                'pending_admin_response' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                                                'in_discussion' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
                                                'awaiting_payment' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
                                            ];
                                            $colorClass = $statusColors[$request->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $colorClass }}">
                                            {{ $request->status_text }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $request->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($request->reference_images && count($request->reference_images) > 0)
                                    <div class="text-sm text-blue-600 dark:text-blue-400 flex items-center gap-1">
                                        <span class="material-icons-round text-sm">photo_library</span>
                                        {{ count($request->reference_images) }}
                                    </div>
                                @endif
                                <a href="{{ route('admin.chatbot.chat-support.detail', $request->id) }}" 
                                   class="bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                                    <span class="material-icons-round text-sm">chat</span>
                                    Mở chat
                                </a>
                            </div>
                        </div>
                        
                        @if($request->description)
                            <div class="mt-3 pt-3 border-t border-border-light dark:border-border-dark">
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                    {{ Str::limit(strip_tags($request->description), 150) }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-icons-round text-gray-400 text-2xl">chat_bubble_outline</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Không có yêu cầu nào</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Hiện tại không có yêu cầu nào cần hỗ trợ. Các yêu cầu mới sẽ xuất hiện ở đây.
                </p>
            </div>
        @endif
    </div>
</div>

<!-- Quick Stats -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white dark:bg-surface-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                <span class="material-icons-round text-yellow-600 dark:text-yellow-400">pending</span>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $activeRequests->where('status', 'pending_admin_response')->count() }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400">Chờ phản hồi</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-surface-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                <span class="material-icons-round text-blue-600 dark:text-blue-400">chat</span>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $activeRequests->where('status', 'in_discussion')->count() }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400">Đang trao đổi</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-surface-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                <span class="material-icons-round text-green-600 dark:text-green-400">check_circle</span>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ \App\Models\CustomProductRequest::where('status', 'completed')->count() }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400">Đã hoàn thành</p>
            </div>
        </div>
    </div>
</div>
@endsection