@extends('admin.layout')

@section('title', ($siteName ?? 'Lenlab Official') . ' - Quản lý người dùng')

@php
    $pageTitle = 'Quản lý người dùng';
    $pageHeading = 'Danh sách người dùng';
    $pageDescription = 'Quản lý tài khoản người dùng, xem đơn hàng và khóa/mở khóa tài khoản.';
    $disableCreate = true; // Disable the "Thêm mới" button for users
@endphp

@section('content')

<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm">
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold">Danh sách người dùng</h3>
            <div class="text-sm text-gray-500">
                Tổng: {{ $users->total() }} người dùng
            </div>
        </div>
        
        @if(isset($users) && $users->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4">ID</th>
                            <th class="text-left py-3 px-4">Tên</th>
                            <th class="text-left py-3 px-4">Email</th>
                            <th class="text-left py-3 px-4">Số điện thoại</th>
                            <th class="text-left py-3 px-4">Trạng thái</th>
                            <th class="text-left py-3 px-4">Số đơn hàng</th>
                            <th class="text-left py-3 px-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-3 px-4">#{{ $user->id }}</td>
                            <td class="py-3 px-4">{{ $user->name }}</td>
                            <td class="py-3 px-4">{{ $user->email }}</td>
                            <td class="py-3 px-4">{{ $user->phone ?: ($user->defaultAddress->phone ?? '-') }}</td>
                            <td class="py-3 px-4">
                                @if($user->locked_at)
                                    <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">Bị khóa</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Hoạt động</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $user->orders_count ?? 0 }} đơn
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                        Chi tiết
                                    </a>
                                    
                                    @if($user->locked_at)
                                        <form method="POST" action="{{ route('admin.users.unlock', $user->id) }}" class="inline">
                                            @csrf 
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-800 text-sm">
                                                Mở khóa
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.lock', $user->id) }}" class="inline">
                                            @csrf 
                                            @method('PATCH')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                Khóa
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6">
                {{ $users->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <span class="material-icons-round text-gray-400 text-4xl mb-2">people</span>
                <p class="text-gray-500">Chưa có người dùng nào</p>
            </div>
        @endif
    </div>
</div>

@endsection
