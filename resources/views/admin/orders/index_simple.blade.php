@extends('admin.layout')
@section('content')
@php
    $pageTitle = 'Đơn hàng';
    $pageHeading = 'Danh sách đơn hàng';
    $pageDescription = 'Quản lý và theo dõi trạng thái các đơn hàng.';
    $createUrl = route('admin.orders.create');
@endphp

<div class="p-6">
  {{-- Filters --}}
  <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-center gap-3 mb-5">
    <div class="flex items-center w-full md:w-[420px] bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-xl px-3 py-2">
      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
      </svg>
      <input name="q" value="{{ request('q') }}"
             type="text"
             class="w-full ml-2 outline-none bg-transparent border-0 focus:ring-0 text-sm"
             placeholder="Tìm theo mã đơn, tên khách hàng..." />
    </div>

    <select name="status"
      class="px-3 py-2 rounded-xl border border-gray-200 dark:border-border-dark bg-white dark:bg-surface-dark text-sm">
      <option value="">Trạng thái đơn hàng</option>
      <option value="pending" @selected(request('status')==='pending')>Chờ duyệt</option>
      <option value="processing" @selected(request('status')==='processing')>Đã duyệt</option>
      <option value="shipped" @selected(request('status')==='shipped')>Đang giao</option>
      <option value="delivered" @selected(request('status')==='delivered')>Đã giao</option>
      <option value="cancelled" @selected(request('status')==='cancelled')>Đã hủy</option>
    </select>

    <select name="payment_method"
      class="px-3 py-2 rounded-xl border border-gray-200 dark:border-border-dark bg-white dark:bg-surface-dark text-sm">
      <option value="">Thanh toán</option>
      <option value="cod" @selected(request('payment_method')==='cod')>COD</option>
      <option value="bank_transfer" @selected(request('payment_method')==='bank_transfer')>CK</option>
    </select>

    <select name="order_type"
      class="px-3 py-2 rounded-xl border border-gray-200 dark:border-border-dark bg-white dark:bg-surface-dark text-sm">
      <option value="">Loại đơn hàng</option>
      <option value="regular" @selected(request('order_type')==='regular')>Đơn hàng thường</option>
      <option value="custom" @selected(request('order_type')==='custom')>Đơn hàng đặt làm</option>
    </select>

    <button type="submit" class="px-3 py-2 rounded-xl border border-gray-200 dark:border-border-dark bg-white dark:bg-surface-dark">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 12.414V19a1 1 0 01-1.447.894l-4-2A1 1 0 019 17V12.414L3.293 6.707A1 1 0 013 6V4z"/>
      </svg>
    </button>
  </form>

  {{-- Table --}}
  <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-2xl overflow-hidden">
    <form id="bulkDeleteForm" method="POST" action="{{ route('admin.orders.bulkDelete') }}">
      @csrf
      @method('DELETE')
      
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-[#151A23]">
            <tr>
              <th class="px-6 py-4 w-10">
                <input type="checkbox" id="checkAll" class="h-4 w-4 rounded border-gray-300">
              </th>
              <th class="px-6 py-4 text-left">Mã đơn</th>
              <th class="px-6 py-4 text-left">Khách hàng</th>
              <th class="px-6 py-4 text-left">Ngày đặt</th>
              <th class="px-6 py-4 text-left">Tổng tiền</th>
              <th class="px-6 py-4 text-left">Thanh toán</th>
              <th class="px-6 py-4 text-left">Minh chứng</th>
              <th class="px-6 py-4 text-left">Trạng thái</th>
              <th class="px-6 py-4 text-left">Ghi chú</th>
              <th class="px-6 py-4"></th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-100 dark:divide-border-dark">
            @forelse($orders as $order)
              <tr class="hover:bg-gray-50/60 dark:hover:bg-white/5">
                <td class="px-6 py-4">
                  <input type="checkbox"
                         class="rowChk h-4 w-4 rounded border-gray-300"
                         name="ids[]"
                         value="{{ $order->order_id }}">
                </td>

                <td class="px-6 py-4 font-semibold text-[#C9A063]">
                  {{ $order->order_id }}
                  @if(isset($order->is_custom_request) && $order->is_custom_request)
                    <span class="ml-2 px-2 py-1 text-xs bg-purple-100 text-purple-700 rounded-full">Custom</span>
                  @endif
                </td>

                <td class="px-6 py-4">
                  <div class="font-semibold text-gray-900 dark:text-white">{{ $order->full_name }}</div>
                  <div class="text-xs text-gray-500">{{ $order->phone }}</div>
                </td>

                <td class="px-6 py-4 text-gray-700 dark:text-gray-200">
                  {{ optional($order->created_at)->format('d/m/Y') }}
                </td>

                <td class="px-6 py-4 font-semibold">
                  {{ number_format($order->total_amount ?? 0) }}đ
                </td>

                <td class="px-6 py-4 font-semibold">
                  {{ $order->payment_method === 'bank_transfer' ? 'CK' : 'COD' }}
                </td>

                <td class="px-6 py-4">
                  @if($order->payment_method === 'bank_transfer' && isset($order->transfer_image) && $order->transfer_image)
                    <button type="button"
                      class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-border-light dark:border-border-dark hover:bg-gray-50 dark:hover:bg-white/5"
                      onclick="window.openTransferImage('{{ 
                        isset($order->is_custom_request) && $order->is_custom_request 
                          ? asset('storage/' . $order->transfer_image)
                          : asset('transfer-images/' . $order->transfer_image) 
                      }}')">
                      <span class="material-icons-round text-gray-500">receipt_long</span>
                    </button>
                  @else
                    <span class="text-gray-400">-</span>
                  @endif
                </td>

                <td class="px-6 py-4">
                  @php
                    $badge = match($order->status) {
                      'pending'   => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                      'processing'=> 'bg-amber-100 text-amber-700 border-amber-200',
                      'shipped'   => 'bg-blue-100 text-blue-700 border-blue-200',
                      'delivered' => 'bg-green-100 text-green-700 border-green-200',
                      'cancelled' => 'bg-red-100 text-red-700 border-red-200',
                      default     => 'bg-gray-100 text-gray-700 border-gray-200',
                    };
                  @endphp

                  @if(isset($order->is_custom_request) && $order->is_custom_request)
                    <!-- Custom request status (editable dropdown) -->
                    <select
                      class="px-3 py-1 rounded-full text-xs font-semibold border bg-white dark:bg-surface-dark {{ $badge }}"
                      onchange="submitStatus('{{ $order->order_id }}', this.value)"
                    >
                      <option value="pending" @selected($order->status==='pending')>Chờ xác nhận</option>
                      <option value="processing" @selected($order->status==='processing')>Đang sản xuất</option>
                      <option value="delivered" @selected($order->status==='delivered')>Hoàn thành</option>
                      <option value="cancelled" @selected($order->status==='cancelled')>Đã hủy</option>
                    </select>
                  @else
                    <!-- Regular order status (editable) -->
                    <select
                      class="px-3 py-1 rounded-full text-xs font-semibold border bg-white dark:bg-surface-dark {{ $badge }}"
                      onchange="submitStatus('{{ $order->order_id }}', this.value)"
                    >
                      <option value="pending" @selected($order->status==='pending')>Chờ duyệt</option>
                      <option value="processing" @selected($order->status==='processing')>Đã duyệt</option>
                      <option value="shipped" @selected($order->status==='shipped')>Đang giao</option>
                      <option value="delivered" @selected($order->status==='delivered')>Đã giao</option>
                      <option value="cancelled" @selected($order->status==='cancelled')>Đã hủy</option>
                    </select>
                  @endif
                </td>

                <td class="px-6 py-4 text-gray-500">
                  {{ \Illuminate\Support\Str::limit($order->order_note, 25) ?: 'Không có ghi chú' }}
                </td>

                <td class="px-6 py-4 text-right">
                  <div class="flex items-center gap-2 justify-end">
                    @if(isset($order->is_custom_request) && $order->is_custom_request)
                      <a href="{{ route('admin.chatbot.chat-support.detail', $order->custom_request_id) }}"
                         class="px-3 py-2 rounded-lg border hover:bg-gray-50 dark:hover:bg-white/5">
                        Chi tiết
                      </a>
                    @else
                      <a href="{{ route('admin.orders.show', $order->order_id) }}"
                         class="px-3 py-2 rounded-lg border hover:bg-gray-50 dark:hover:bg-white/5">
                        Chi tiết
                      </a>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="px-6 py-10 text-center text-gray-500">Chưa có đơn hàng</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </form>

    <div class="p-4">
      {{ $orders->appends(request()->query())->links() }}
    </div>
  </div>

  <form id="statusForm" method="POST" class="hidden">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="statusValue">
  </form>

  <script>
  function submitStatus(orderId, status) {
    const form = document.getElementById('statusForm');
    const input = document.getElementById('statusValue');

    input.value = status;

    // Check if this is a custom request (starts with LL)
    if (orderId.startsWith('LL')) {
      // Handle custom request status update
      // Extract request ID from custom order ID format: LL + YYYYMMDD + sequential number
      // Example: LL202512240001 -> extract "1" (remove leading zeros)
      const customRequestId = orderId.replace(/^LL\d{8}/, '').replace(/^0+/, '') || '1';
      
      fetch(`/admin/api/chatbot/custom-requests/${customRequestId}/status`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          location.reload();
        } else {
          alert('Lỗi: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật trạng thái');
      });
      
      return;
    }

    // Handle regular order status update
    if (status === 'cancelled') {
      if (!confirm(`Bạn chắc chắn muốn hủy đơn ${orderId}?`)) return;
      form.action = `/admin/orders/${orderId}/cancel`; // ✅ gọi cancel()
    } else {
      form.action = `/admin/orders/${orderId}/status`; // ✅ gọi updateStatus()
    }

    form.submit();
  }

  // Image modal function
  window.openTransferImage = function(imageUrl) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 bg-black bg-opacity-75 flex items-center justify-center p-4';
    modal.onclick = () => modal.remove();
    
    modal.innerHTML = `
      <div class="relative max-w-4xl max-h-full">
        <img src="${imageUrl}" alt="Ảnh chuyển khoản" class="max-w-full max-h-full object-contain rounded-lg">
        <button onclick="event.stopPropagation(); this.parentElement.parentElement.remove()" 
                class="absolute top-4 right-4 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    `;
    
    document.body.appendChild(modal);
  };
  </script>
</div>

@endsection
