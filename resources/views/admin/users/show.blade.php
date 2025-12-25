@extends('admin.layout')

@section('title', ($siteName ?? 'Lenlab Official') . ' - Chi tiết người dùng')

@php
    $pageTitle = 'Quản lý người dùng';
    $pageHeading = 'Chi tiết người dùng';
    $pageDescription = 'Thông tin chi tiết và lịch sử mua hàng của người dùng.';
@endphp

@section('content')
<div class="p-6">
  {{-- Breadcrumb + title --}}
  <div class="flex items-start justify-between mb-5">
    <div>
      <div class="text-sm text-gray-500">
        Khách hàng <span class="mx-2">›</span> <span class="font-semibold text-gray-800">{{ $user->name }}</span>
      </div>
      <h1 class="text-2xl font-bold text-gray-900 mt-1">Chi tiết & Lịch sử mua hàng</h1>
    </div>

    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700">
      ← Quay lại
    </a>
  </div>

  {{-- Top section --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left: personal info --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2 text-gray-900 font-semibold">
          <span class="material-icons-round text-yellow-600">person</span>
          Thông tin cá nhân
        </div>

        @if($user->locked_at)
          <span class="px-3 py-1 rounded-full text-sm bg-gray-200 text-gray-700">
            Bị khóa {{ optional($user->locked_at)->format('d/m/Y H:i') }}
            @if($user->lock_reason)
              <br><small>Lý do: {{ $user->lock_reason }}</small>
            @endif
          </span>
        @else
          <span class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-700">Hoạt động</span>
        @endif
      </div>

      <div class="flex gap-5 items-center">
        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center">
          <span class="material-icons-round text-gray-400 text-4xl">person</span>
        </div>

        <div class="flex-1">
          <div class="text-xl font-bold text-gray-900">{{ $user->name }}</div>
          <div class="text-gray-500">{{ $user->email }}</div>
          <div class="text-gray-500">{{ $user->phone ?? '-' }}</div>
        </div>
      </div>

      {{-- mini cards --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
        <div class="rounded-xl border border-gray-200 p-4">
          <div class="text-xs text-gray-500 font-semibold">TỔNG CHI TIÊU</div>
          <div class="mt-1 font-bold text-yellow-700">
            {{ number_format($totalSpent ?? 0, 0, ',', '.') }}đ
          </div>
        </div>

        <div class="rounded-xl border border-gray-200 p-4">
          <div class="text-xs text-gray-500 font-semibold">HẠNG THÀNH VIÊN</div>
          <div class="mt-1 font-bold text-gray-900">Gold Member</div>
        </div>

        <div class="rounded-xl border border-gray-200 p-4">
          <div class="text-xs text-gray-500 font-semibold">ĐỊA CHỈ MẶC ĐỊNH</div>
          <div class="mt-1 text-sm text-gray-700 line-clamp-2">
            @if($defaultAddress)
              {{ $defaultAddress->specific_address ?? '' }}
              @if($defaultAddress->ward_name), {{ $defaultAddress->ward_name }}@endif
              @if($defaultAddress->province_name), {{ $defaultAddress->province_name }}@endif
            @else
              -
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Right: account management --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
      <div class="flex items-center gap-2 font-semibold text-gray-900 mb-4">
        <span class="material-icons-round text-red-500">lock</span>
        Quản lý tài khoản
      </div>

      <div class="mb-4">
        <div class="text-sm text-gray-600 mb-2">Trạng thái khóa</div>

        {{-- Toggle UI (functional) --}}
        <form method="POST" 
              action="{{ $user->locked_at ? route('admin.users.unlock', $user->id) : route('admin.users.lock', $user->id) }}"
              id="toggleForm">
          @csrf
          @method('PATCH')
          
          <button type="button" onclick="document.getElementById('toggleForm').submit()" class="flex items-center gap-3">
            <div class="w-11 h-6 rounded-full {{ $user->locked_at ? 'bg-gray-400' : 'bg-green-400' }} relative">
              <div class="w-5 h-5 bg-white rounded-full absolute top-0.5 transition-all
                {{ $user->locked_at ? 'left-0.5' : 'left-5.5' }}">
              </div>
            </div>
            
            <div class="text-sm font-medium text-gray-800">
              {{ $user->locked_at ? 'Khóa tài khoản' : 'Đang hoạt động' }}
            </div>
          </button>

          <div class="mt-4">
            <label class="text-sm text-gray-600">Lý do khóa / Mở khóa</label>
            <select name="lock_reason" class="mt-2 w-full border border-gray-200 rounded-lg px-3 py-2">
              <option value="">Chọn lý do...</option>
              <option value="Spam / Lạm dụng" {{ $user->lock_reason === 'Spam / Lạm dụng' ? 'selected' : '' }}>Spam / Lạm dụng</option>
              <option value="Gian lận" {{ $user->lock_reason === 'Gian lận' ? 'selected' : '' }}>Gian lận</option>
              <option value="Yêu cầu từ khách" {{ $user->lock_reason === 'Yêu cầu từ khách' ? 'selected' : '' }}>Yêu cầu từ khách</option>
              <option value="Khác" {{ $user->lock_reason === 'Khác' ? 'selected' : '' }}>Khác</option>
            </select>
          </div>

          <div class="mt-4">
            <label class="text-sm text-gray-600">Ghi chú thêm</label>
            <textarea name="lock_note" class="mt-2 w-full border border-gray-200 rounded-lg px-3 py-2" rows="4"
                      placeholder="Nhập chi tiết lý do...">{{ $user->lock_note }}</textarea>
          </div>

          <div class="mt-4">
            <button type="submit" class="w-full px-4 py-3 rounded-xl {{ $user->locked_at ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }} text-white font-semibold">
              {{ $user->locked_at ? 'Mở khóa tài khoản' : 'Khóa tài khoản' }}
            </button>
          </div>
        </form>
      </div>

      {{-- Note: Toggle above now handles lock/unlock functionality --}}
      <div class="text-xs text-gray-500 mt-4">
        Bấm vào thanh trượt phía trên để khóa/mở khóa tài khoản
      </div>
    </div>
  </div>

  {{-- Orders table --}}
  <div class="bg-white rounded-2xl border border-gray-200 p-6 mt-6">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-2 font-semibold text-gray-900">
        <span class="material-icons-round text-yellow-600">history</span>
        Lịch sử mua hàng
      </div>

      <form method="GET" action="{{ route('admin.users.show', $user->id) }}" class="flex items-center gap-2">
        <div class="relative">
          <span class="material-icons-round absolute left-3 top-2.5 text-gray-400 text-base">search</span>
          <input name="order_q" value="{{ $orderQ ?? '' }}"
                 class="pl-9 pr-3 py-2 border border-gray-200 rounded-lg w-72"
                 placeholder="Tìm đơn hàng..." />
        </div>
        <button class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">Lọc</button>
      </form>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="text-gray-500 border-b">
          <tr>
            <th class="text-left py-3">MÃ ĐƠN HÀNG</th>
            <th class="text-left py-3">NGÀY ĐẶT</th>
            <th class="text-right py-3">TỔNG TIỀN</th>
            <th class="text-left py-3">TRẠNG THÁI</th>
            <th class="text-right py-3">CHI TIẾT</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          @foreach($orders as $o)
          @php
            $total = ($o->subtotal - $o->discount_amount + $o->shipping_fee);
          @endphp
          <tr>
            <td class="py-3 font-semibold text-gray-900">{{ $o->order_id }}</td>

            <td class="py-3 text-gray-700">
              {{ $o->created_at ? \Carbon\Carbon::parse($o->created_at)->format('d/m/Y - H:i') : '-' }}
            </td>

            <td class="py-3 text-right font-semibold text-gray-900">
              {{ number_format($total, 0, ',', '.') }}đ
            </td>

            <td class="py-3">
              <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700">
                {{ $o->payment_status ?? '-' }}
              </span>
            </td>

            <td class="py-3 text-right">
              <a href="{{ route('admin.orders.show', $o->order_id) }}"
                 class="inline-flex items-center justify-center w-9 h-9 rounded-lg hover:bg-gray-100 text-gray-600"
                 title="Xem chi tiết đơn">
                <span class="material-icons-round text-base">visibility</span>
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $orders->links() }}
    </div>
  </div>
</div>
@endsection
