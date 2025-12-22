@extends('admin.layout')
@section('title', ($siteName ?? 'Lenlab Official') . ' - Cấu hình Banner')

@php
    $pageTitle = 'Marketing';
    $pageHeading = 'Cấu hình Banner';
    $pageDescription = 'Quản lý banner ngang cho trang chủ và banner campaign trong landing.';
@endphp

@section('content')

<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm p-6">
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold">{{ $pageHeading }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $pageDescription }}</p>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Banner 1: Trang chủ (Hero) --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-semibold text-lg">Banner Trang chủ (Hero)</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Vị trí: home</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.banners.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <input type="hidden" name="position" value="home">

                <div>
                    <label class="text-sm font-semibold">Link (tùy chọn)</label>
                    <input name="link" value="{{ old('link', $home->link) }}"
                           class="mt-2 w-full px-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700"
                           placeholder="https://lenlab.vn/...">
                </div>

                <div class="flex items-center gap-3">
                    <input id="home_active" type="checkbox" name="is_active" value="1"
                           {{ $home->is_active ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300">
                    <label for="home_active" class="text-sm">Kích hoạt</label>
                </div>

                <div>
                    <label class="text-sm font-semibold">Ảnh banner ngang</label>
                    <input type="file" name="image"
                           class="mt-2 w-full px-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 mt-2">Gợi ý: 1920x600</p>
                </div>

                @if($home->image)
                    <div class="mt-3 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800">
                        <img src="{{ asset('storage/'.$home->image) }}" class="w-full object-cover" alt="home banner">
                    </div>
                @endif

                <button class="w-full mt-2 px-4 py-2 rounded-xl bg-[#c9a36a] text-white font-semibold hover:opacity-90">
                    Lưu Banner Trang chủ
                </button>
            </form>
        </div>

        {{-- Banner 2: Campaign (giữa landing) --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-semibold text-lg">Banner Campaign (Landing)</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Vị trí: campaign</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.banners.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <input type="hidden" name="position" value="campaign">

                <div>
                    <label class="text-sm font-semibold">Link (tùy chọn)</label>
                    <input name="link" value="{{ old('link', $campaign->link) }}"
                           class="mt-2 w-full px-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700"
                           placeholder="/san-pham?...">
                </div>

                <div class="flex items-center gap-3">
                    <input id="campaign_active" type="checkbox" name="is_active" value="1"
                           {{ $campaign->is_active ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300">
                    <label for="campaign_active" class="text-sm">Kích hoạt</label>
                </div>

                <div>
                    <label class="text-sm font-semibold">Ảnh banner ngang</label>
                    <input type="file" name="image"
                           class="mt-2 w-full px-4 py-2 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 mt-2">Gợi ý: 1200x240 (tùy layout)</p>
                </div>

                @if($campaign->image)
                    <div class="mt-3 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800">
                        <img src="{{ asset('storage/'.$campaign->image) }}" class="w-full object-cover" alt="campaign banner">
                    </div>
                @endif

                <button class="w-full mt-2 px-4 py-2 rounded-xl bg-[#c9a36a] text-white font-semibold hover:opacity-90">
                    Lưu Banner Campaign
                </button>
            </form>
        </div>

    </div>
</div>

@endsection
