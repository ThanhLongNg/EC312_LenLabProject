@extends('admin.layout')

@section('title', 'LENLAB - Quản lý sản phẩm')

@php
    // Variables for header
    $pageTitle = 'Sản phẩm';
    $pageHeading = 'Danh sách sản phẩm';
    $pageDescription = 'Quản lý kho hàng và danh mục sản phẩm của bạn.';
    $createUrl = route('admin.products.create');
@endphp

@section('content')
<!-- Bulk Delete Form (hidden) -->
<form id="bulkDeleteForm" method="POST" action="{{ route('admin.products.bulkDelete') }}" style="display: none;">
    @csrf
    @method('DELETE')
    <div id="bulkDeleteIdsWrap"></div>
</form>

<!-- Search and Filter Bar -->
<div class="bg-surface-light dark:bg-surface-dark rounded-xl p-4 mb-6 shadow-sm border border-border-light dark:border-border-dark flex flex-col md:flex-row gap-4 items-center justify-between">
    <!-- Search Input -->
    <div class="relative w-full md:w-96">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <span class="material-icons-round text-gray-400">search</span>
        </span>
        <input type="text" 
               class="pl-10 w-full rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-primary focus:border-primary transition-colors" 
               placeholder="Tìm kiếm theo tên, SKU..." 
               name="search" 
               value="{{ request('search') }}">
    </div>
    
    <!-- Filters -->
    <div class="flex items-center gap-3 w-full md:w-auto">
        <select class="rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-primary focus:border-primary text-sm py-2 px-3" name="category">
            <option value="">Tất cả danh mục</option>
            @foreach($categories as $id => $name)
                <option value="{{ $id }}" {{ request('category') == $id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
        
        <select class="rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-primary focus:border-primary text-sm py-2 px-3" name="is_active">
            <option value="">Trạng thái</option>
            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Đang bán</option>
            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Đã ẩn</option>
        </select>
        
        <button type="button" class="p-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
            <span class="material-icons-round text-xl">filter_list</span>
        </button>
    </div>
</div>

<!-- Products Table -->
<div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400 text-xs uppercase font-semibold border-b border-border-light dark:border-border-dark">
                    <th class="px-6 py-4 w-12">
                        <input type="checkbox" id="checkAll" class="rounded border-gray-300 text-primary focus:ring-primary bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                    </th>
                    <th class="px-6 py-4">Sản phẩm</th>
                    <th class="px-6 py-4">Mã SKU</th>
                    <th class="px-6 py-4">Giá bán</th>
                    <th class="px-6 py-4 text-center">Tồn kho</th>
                    <th class="px-6 py-4">Danh mục</th>
                    <th class="px-6 py-4">Trạng thái</th>
                    <th class="px-6 py-4 text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-light dark:divide-border-dark text-sm">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                    <td class="px-6 py-4">
                        <input type="checkbox" class="rowChk rounded border-gray-300 text-primary focus:ring-primary bg-gray-50 dark:bg-gray-700 dark:border-gray-600" value="{{ $product->id }}">
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-lg bg-gray-200 dark:bg-gray-700 overflow-hidden flex-shrink-0 border border-gray-200 dark:border-gray-600 flex items-center justify-center text-gray-400 dark:text-gray-500">
                                @if($product->image)
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="material-icons-round text-xl">image</span>
                                @endif
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    @if($product->color || $product->size)
                                        @if($product->color)Màu: {{ $product->color }}@endif
                                        @if($product->color && $product->size) • @endif
                                        @if($product->size)Size: {{ $product->size }}@endif
                                    @else
                                        ID: #{{ $product->id }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300 font-mono">
                        PRD-{{ str_pad($product->id, 3, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                        {{ number_format($product->price) }}₫
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($product->quantity > 20)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">{{ $product->quantity }}</span>
                        @elseif($product->quantity > 5)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">{{ $product->quantity }}</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">{{ $product->quantity }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-gray-600 dark:text-gray-300">
                            @switch($product->category_id)
                                @case(1) Nguyên phụ liệu @break
                                @case(2) Đồ trang trí @break
                                @case(3) Thời trang len @break
                                @case(4) Combo tự làm @break
                                @case(5) Sách hướng dẫn @break
                                @case(6) Thú bông len @break
                                @default Chưa phân loại
                            @endswitch
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer toggle-active-btn" 
                                   data-id="{{ $product->id }}" 
                                   {{ $product->is_active ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/30 dark:peer-focus:ring-primary/50 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                        </label>
                    </td>
                    <td class="px-4 py-4 text-right">
                        <div class="inline-flex items-center gap-2">
                            <!-- Sửa -->
                            <a href="{{ route('admin.products.edit', $product->id) }}"
                               class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200
                                      text-gray-700 hover:bg-gray-50 transition">
                                <span class="material-icons-round text-[18px]">edit</span>
                                <span class="hidden sm:inline">Sửa</span>
                            </a>

                            <!-- Xóa -->
                            <button type="button"
                                    data-id="{{ $product->id }}"
                                    class="btnDelete inline-flex items-center gap-2 px-3 py-2 rounded-lg
                                           bg-red-500 text-white hover:bg-red-600 transition">
                                <span class="material-icons-round text-[18px]">delete</span>
                                <span class="hidden sm:inline">Xóa</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <span class="material-icons-round text-4xl text-gray-400">inventory_2</span>
                            <p class="text-gray-500 dark:text-gray-400">Không có sản phẩm nào</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($products->hasPages())
    <div class="bg-surface-light dark:bg-surface-dark px-6 py-4 border-t border-border-light dark:border-border-dark flex items-center justify-between">
        <div class="text-sm text-gray-500 dark:text-gray-400">
            Hiển thị <span class="font-medium text-gray-900 dark:text-white">{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</span> 
            trong số <span class="font-medium text-gray-900 dark:text-white">{{ $products->total() }}</span> sản phẩm
        </div>
        <div class="flex gap-2">
            @if($products->onFirstPage())
                <button disabled class="px-3 py-1 border border-border-light dark:border-border-dark rounded-md text-gray-400 cursor-not-allowed">
                    <span class="material-icons-round text-sm">chevron_left</span>
                </button>
            @else
                <a href="{{ $products->previousPageUrl() }}" class="px-3 py-1 border border-border-light dark:border-border-dark rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <span class="material-icons-round text-sm">chevron_left</span>
                </a>
            @endif
            
            @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                @if($page == $products->currentPage())
                    <button class="px-3 py-1 bg-primary text-white rounded-md hover:bg-primary-hover font-medium text-sm">{{ $page }}</button>
                @else
                    <a href="{{ $url }}" class="px-3 py-1 border border-border-light dark:border-border-dark rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 text-sm">{{ $page }}</a>
                @endif
            @endforeach
            
            @if($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" class="px-3 py-1 border border-border-light dark:border-border-dark rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <span class="material-icons-round text-sm">chevron_right</span>
                </a>
            @else
                <button disabled class="px-3 py-1 border border-border-light dark:border-border-dark rounded-md text-gray-400 cursor-not-allowed">
                    <span class="material-icons-round text-sm">chevron_right</span>
                </button>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection

<!-- Delete Modal -->
<div id="deleteModal"
     class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-lg">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 flex items-center justify-center
                        rounded-full bg-red-100 text-red-600">
                <span class="material-icons-round">warning</span>
            </div>

            <div>
                <h3 class="text-lg font-semibold">Xác nhận xóa sản phẩm</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Bạn có chắc chắn muốn xóa
                    <span id="deleteProductName" class="font-medium"></span>?
                    Hành động này không thể hoàn tác.
                </p>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <button onclick="closeDeleteModal()"
                    class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-100">
                Hủy
            </button>

            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                    Xác nhận xóa
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Select All functionality
    document.getElementById('checkAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.rowChk');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkDeleteIds();
    });

    // Individual checkbox change
    document.querySelectorAll('.rowChk').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkDeleteIds);
    });

    // Update bulk delete IDs + enable/disable button
    function updateBulkDeleteIds() {
        const checkedBoxes = document.querySelectorAll('.rowChk:checked');
        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        
        // Render inputs ids[]
        const wrap = document.getElementById('bulkDeleteIdsWrap');
        if (wrap) {
            wrap.innerHTML = ids.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('');
        }
        
        const btn = document.getElementById('bulkDeleteBtn');
        const countEl = document.getElementById('selectedCount');
        if (countEl) countEl.textContent = ids.length;
        if (btn) btn.disabled = ids.length === 0;
    }

    // Bulk delete button click
    document.getElementById('bulkDeleteBtn')?.addEventListener('click', function () {
        const idsInputs = document.querySelectorAll('#bulkDeleteIdsWrap input[name="ids[]"]');
        const ids = Array.from(idsInputs).map(input => input.value);
        
        if (!ids.length) return;
        
        if (confirm(`Bạn có chắc muốn xóa ${ids.length} sản phẩm đã chọn?`)) {
            // Submit form hidden (đúng với form bulkDeleteForm bạn đang có)
            document.getElementById('bulkDeleteForm').submit();
        }
    });

    // Toggle active functionality
    document.querySelectorAll('.toggle-active-btn').forEach(button => {
        button.addEventListener('change', function() {
            const productId = this.dataset.id;
            
            fetch(`/admin/products/${productId}/toggle-active`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                } else {
                    // Revert toggle if failed
                    this.checked = !this.checked;
                    showAlert('error', 'Có lỗi xảy ra: ' + data.message);
                }
            })
            .catch(error => {
                // Revert toggle if failed
                this.checked = !this.checked;
                console.error('Error:', error);
                showAlert('error', 'Có lỗi xảy ra khi thay đổi trạng thái');
            });
        });
    });

    // Delete functionality - Open modal with product info
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btnDelete');
        if (!btn) return;

        const id = btn.dataset.id;
        const row = btn.closest('tr');
        const productName = row.querySelector('td:nth-child(2) .font-medium')?.textContent || 'sản phẩm này';
        
        openDeleteModal(id, productName);
    });

    // Show alert function
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200';
        const iconColor = type === 'success' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
        const icon = type === 'success' ? 'check_circle' : 'error';
        
        alertDiv.className = `mb-4 ${bgColor} border px-4 py-3 rounded-lg flex items-center gap-2`;
        alertDiv.innerHTML = `
            <span class="material-icons-round ${iconColor}">${icon}</span>
            <span>${message}</span>
            <button onclick="this.parentElement.remove()" class="ml-auto ${iconColor} hover:opacity-75">
                <span class="material-icons-round text-sm">close</span>
            </button>
        `;
        
        const contentArea = document.querySelector('.bg-surface-light');
        contentArea.parentNode.insertBefore(alertDiv, contentArea);
        
        // Auto dismiss after 3 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 3000);
    }

    // Initialize bulk delete IDs
    updateBulkDeleteIds();
</script>

<script>
    // Global modal control functions
    function openDeleteModal(id, name) {
        document.getElementById('deleteProductName').innerText = name;
        document.getElementById('deleteForm').action = `/admin/products/${id}`;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
    }
</script>
@endpush