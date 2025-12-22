@extends('admin.layout')

@section('content')
@php
  $pageTitle = 'Đơn hàng';
  $pageHeading = 'Chi tiết đơn hàng';
  $pageDescription = 'Xem thông tin đơn hàng và sản phẩm trong đơn.';
@endphp

<div class="space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Chi tiết đơn hàng #{{ $order->order_id }}</h1>
      <p class="text-sm text-gray-500">Trạng thái: {{ $order->status }}</p>
    </div>

    <a href="{{ route('admin.orders.index') }}"
       class="px-4 py-2 rounded-lg border hover:bg-gray-50 dark:hover:bg-white/5">
      Quay lại
    </a>
  </div>

  {{-- 2 khối thông tin --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-2xl p-5">
      <h3 class="font-semibold mb-4">Thông tin đơn hàng</h3>
      <div class="text-sm space-y-2">
        <div class="flex justify-between"><span class="text-gray-500">Mã đơn</span><span class="font-medium">{{ $order->order_id }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Ngày đặt</span><span class="font-medium">{{ optional($order->created_at)->format('d/m/Y H:i') }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Thanh toán</span><span class="font-medium">{{ $order->payment_method }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Phí ship</span><span class="font-medium">{{ number_format($order->shipping_fee ?? 0) }}đ</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Giảm giá</span><span class="font-medium">{{ number_format($order->discount_amount ?? 0) }}đ</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Tổng tiền</span><span class="font-bold text-red-500">{{ number_format($order->total_amount ?? 0) }}đ</span></div>
      </div>

    {{-- ✅ Minh chứng chuyển khoản --}}
    @if($order->payment_method === 'bank_transfer' && $order->transfer_image)
        @php
            // DB chỉ lưu tên file
            $src = asset('transfer-images/' . $order->transfer_image);
        @endphp

        <div class="mt-4">
            <div class="text-sm text-gray-500 mb-2">Minh chứng chuyển khoản</div>

            <a href="{{ $src }}" target="_blank">
                <img
                    class="max-w-[420px] w-full rounded-xl border object-contain bg-white"
                    src="{{ $src }}"
                    alt="transfer"
                    onerror="this.onerror=null;this.src='https://via.placeholder.com/600x300?text=No+Image';"
                >
            </a>
        </div>
    @endif

    @if($order->payment_method === 'bank_transfer')
        <div class="mt-5 border-t pt-4">
            <div class="font-semibold mb-2">Hoàn tiền</div>

            <form method="POST" action="{{ route('admin.orders.refund', $order->order_id) }}" class="flex flex-col gap-3">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="text-sm text-gray-500">Trạng thái hoàn</label>
                        <select name="refund_status" class="w-full px-3 py-2 rounded-lg border">
                            <option value="none" @selected($order->refund_status==='none')>Không</option>
                            <option value="requested" @selected($order->refund_status==='requested')>Chờ hoàn</option>
                            <option value="refunded" @selected($order->refund_status==='refunded')>Đã hoàn</option>
                            <option value="rejected" @selected($order->refund_status==='rejected')>Từ chối</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm text-gray-500">Số tiền hoàn</label>
                        <input name="refund_amount"
                               value="{{ $order->refund_amount ?? $order->total_amount }}"
                               class="w-full px-3 py-2 rounded-lg border" />
                    </div>

                    <div>
                        <label class="text-sm text-gray-500">Thời gian hoàn</label>
                        <div class="px-3 py-2 rounded-lg border bg-gray-50">
                            {{ $order->refunded_at ? \Carbon\Carbon::parse($order->refunded_at)->format('d/m/Y H:i') : '-' }}
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-sm text-gray-500">Ghi chú</label>
                    <textarea name="refund_note" class="w-full px-3 py-2 rounded-lg border" rows="2">{{ $order->refund_note }}</textarea>
                </div>

                <div class="flex justify-end">
                    <button class="px-4 py-2 rounded-lg bg-blue-600 text-white">Lưu hoàn tiền</button>
                </div>
            </form>
        </div>
    @endif

    </div>

    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-2xl p-5">
      <h3 class="font-semibold mb-4">Thông tin khách hàng</h3>
      <div class="text-sm space-y-2">
        <div class="flex justify-between"><span class="text-gray-500">Họ tên</span><span class="font-medium">{{ $order->full_name }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">SĐT</span><span class="font-medium">{{ $order->phone }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Email</span><span class="font-medium">{{ $order->email }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Tỉnh</span><span class="font-medium">{{ $order->province }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Phường/Xã</span><span class="font-medium">{{ $order->ward }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Địa chỉ</span><span class="font-medium">{{ $order->specific_address }}</span></div>
        <div class="pt-2 text-gray-600"><span class="text-gray-500">Ghi chú:</span> {{ $order->order_note ?? 'Không có' }}</div>
      </div>
    </div>
  </div>

  {{-- Danh sách sản phẩm --}}
  <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-border-dark font-semibold">Danh sách sản phẩm</div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-[#151A23]">
          <tr>
            <th class="px-6 py-4 text-left">Sản phẩm</th>
            <th class="px-6 py-4 text-left">Đơn giá</th>
            <th class="px-6 py-4 text-left">Số lượng</th>
            <th class="px-6 py-4 text-left">Thành tiền</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-border-dark">
          @forelse($order->orderItems as $item)
            <tr>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <img class="w-12 h-12 rounded-lg border object-cover"
                       src="{{ order_item_img($item) }}"
                       onerror="this.src='https://via.placeholder.com/48?text=IMG';"
                       alt="">
                  <div>
                    <div class="font-semibold">{{ $item->product_name }}</div>
                    @if($item->variant_info)
                      <div class="text-xs text-gray-500">{{ $item->variant_info }}</div>
                    @endif
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">{{ number_format($item->price ?? 0) }}đ</td>
              <td class="px-6 py-4">{{ $item->quantity ?? 0 }}</td>
              <td class="px-6 py-4 font-semibold">{{ number_format($item->total ?? 0) }}đ</td>
            </tr>
          @empty
            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Không có sản phẩm</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
