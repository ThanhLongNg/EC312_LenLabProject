@extends('admin.layout')

@section('title', 'Thêm sản phẩm mới')

@section('content')
<div class="p-6 max-w-6xl mx-auto">

    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.products.index') }}" class="hover:text-primary">Danh sách sản phẩm</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Thêm sản phẩm mới</span>
    </div>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- LEFT -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Thông tin cơ bản -->
                <div class="bg-white dark:bg-surface-dark rounded-xl p-6 border">
                    <h3 class="text-lg font-semibold mb-4">Thông tin cơ bản</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Tên sản phẩm</label>
                            <input name="name" required
                                   class="w-full rounded-lg border-gray-300 focus:ring-primary focus:border-primary"
                                   placeholder="Ví dụ: Túi tote len hoa cúc">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Giá bán (VNĐ)</label>
                                <input name="price" type="number" required
                                       class="w-full rounded-lg border-gray-300 focus:ring-primary focus:border-primary">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Số lượng</label>
                                <input name="quantity" type="number" required
                                       class="w-full rounded-lg border-gray-300 focus:ring-primary focus:border-primary">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Màu sắc</label>
                                <input name="color"
                                       class="w-full rounded-lg border-gray-300 focus:ring-primary focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Kích thước</label>
                                <input name="size"
                                       class="w-full rounded-lg border-gray-300 focus:ring-primary focus:border-primary">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Mô tả</label>
                            <textarea name="description" rows="4"
                                      class="w-full rounded-lg border-gray-300 focus:ring-primary focus:border-primary"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Phân loại hàng hóa -->
                <div class="bg-white dark:bg-surface-dark rounded-xl p-6 border">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Phân loại hàng hóa</h3>
                        <button type="button" id="btnAddOption"
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-dashed border-primary text-primary hover:bg-primary/5 text-sm font-medium">
                            <span class="material-icons-round text-base">add</span>
                            Thêm nhóm phân loại
                        </button>
                    </div>

                    <div id="optionsWrap" class="space-y-4"></div>

                    <div class="mt-6">
                        <h4 class="text-sm font-semibold mb-3 flex items-center gap-2">
                            <span class="material-icons-round text-gray-400 text-lg">table_view</span>
                            Bảng biến thể
                        </h4>

                        <div class="overflow-hidden border rounded-lg">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                                        <tr class="text-left text-gray-500">
                                            <th class="px-4 py-3">Biến thể</th>
                                            <th class="px-4 py-3 w-40">SKU</th>
                                            <th class="px-4 py-3 w-40">Giá</th>
                                            <th class="px-4 py-3 w-40">Kho</th>
                                        </tr>
                                    </thead>
                                    <tbody id="variantsTbody" class="divide-y bg-white dark:bg-surface-dark"></tbody>
                                </table>
                            </div>
                        </div>

                        <p class="text-xs text-gray-500 mt-2">
                            * Nếu bạn không tạo nhóm phân loại, hệ thống sẽ coi như sản phẩm không có biến thể.
                        </p>
                    </div>
                </div>

                <!-- Trạng thái -->
                <div class="bg-white dark:bg-surface-dark rounded-xl p-6 border">
                    <h3 class="text-lg font-semibold mb-4">Trạng thái</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Danh mục</label>
                            <select name="category_id" class="w-full rounded-lg border-gray-300">
                                <option value="1">Nguyên phụ liệu</option>
                                <option value="2">Đồ trang trí</option>
                                <option value="3">Thời trang len</option>
                                <option value="4">Combo tự làm</option>
                                <option value="5">Sách hướng dẫn</option>
                                <option value="6">Thú bông len</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Tình trạng kho</label>
                            <select name="status" class="w-full rounded-lg border-gray-300">
                                <option value="còn hàng">Còn hàng</option>
                                <option value="hết hàng">Hết hàng</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-6">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="new" value="1" class="rounded text-primary">
                            <span>Sản phẩm mới</span>
                        </label>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded text-primary">
                            <span>Hiển thị</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="space-y-6">

                <!-- Ảnh -->
                <div class="bg-white dark:bg-surface-dark rounded-xl p-6 border">
                    <h3 class="text-lg font-semibold mb-4">Hình ảnh</h3>
                    
                    <div class="grid gap-4">
                        <!-- Preview -->
                        <div class="aspect-square w-full rounded-xl border border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 overflow-hidden flex items-center justify-center">
                            <img id="imagePreview" src="" alt="Preview" class="hidden w-full h-full object-cover" />
                            <div id="imagePlaceholder" class="text-center p-6">
                                <span class="material-icons-round text-4xl text-gray-400 mb-2">cloud_upload</span>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-semibold">Nhấn để tải lên</span> hoặc kéo thả
                                </p>
                                <p class="text-xs text-gray-400 mt-1">PNG, JPG (tối đa 2MB)</p>
                            </div>
                        </div>
                        
                        <!-- Dropzone -->
                        <label id="dropzone"
                               class="cursor-pointer rounded-lg border border-gray-200 dark:border-gray-700 p-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <div class="flex items-center gap-3">
                                <span class="material-icons-round text-gray-400">photo_camera</span>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100">Chọn hoặc kéo thả ảnh</p>
                                    <p id="fileName" class="text-xs text-gray-500 truncate">Chưa có tệp nào</p>
                                </div>
                            </div>
                            <input id="imageInput" name="image" type="file" accept="image/*" class="hidden" required>
                        </label>
                    </div>
                </div>

                <!-- Hoàn tất -->
                <div class="bg-white dark:bg-surface-dark rounded-xl p-6 border">
                    <h3 class="text-lg font-semibold mb-2">Hoàn tất</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Kiểm tra thông tin trước khi lưu sản phẩm.
                    </p>

                    <div class="space-y-3">
                        <button type="submit"
                                class="w-full bg-primary hover:bg-primary-hover text-white py-2.5 rounded-lg font-medium">
                            Lưu sản phẩm
                        </button>

                        <a href="{{ route('admin.products.index') }}"
                           class="block text-center w-full border py-2.5 rounded-lg">
                            Hủy
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
  // ===== Image Upload =====
  const input = document.getElementById('imageInput');
  const dropzone = document.getElementById('dropzone');
  const preview = document.getElementById('imagePreview');
  const placeholder = document.getElementById('imagePlaceholder');
  const fileName = document.getElementById('fileName');

  function showPreview(file){
    if(!file) return;
    fileName.textContent = file.name;

    const url = URL.createObjectURL(file);
    preview.src = url;
    preview.classList.remove('hidden');
    placeholder.classList.add('hidden');
  }

  input.addEventListener('change', (e) => {
    const file = e.target.files?.[0];
    showPreview(file);
  });

  // Drag & Drop
  ['dragenter','dragover'].forEach(evt => {
    dropzone.addEventListener(evt, (e) => {
      e.preventDefault();
      dropzone.classList.add('ring-2','ring-primary');
    });
  });
  ['dragleave','drop'].forEach(evt => {
    dropzone.addEventListener(evt, (e) => {
      e.preventDefault();
      dropzone.classList.remove('ring-2','ring-primary');
    });
  });

  dropzone.addEventListener('drop', (e) => {
    const file = e.dataTransfer.files?.[0];
    if(!file) return;
    input.files = e.dataTransfer.files; // gán vào input để submit form
    showPreview(file);
  });

  // ===== SKU tự sinh (client) =====
  const nameInput = document.querySelector('input[name="name"]');
  const skuInput = document.querySelector('input[name="sku"]'); // nếu bạn có field sku
  function slugSkuBase(str){
    return (str || '')
      .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
      .replace(/[^a-zA-Z0-9]+/g,'-')
      .replace(/^-+|-+$/g,'')
      .toUpperCase()
      .slice(0, 18);
  }
  function rand4(){
    return Math.random().toString(36).substring(2,6).toUpperCase();
  }

  // Nếu bạn muốn có ô SKU chính cho sản phẩm: thêm input name="sku" ở form.
  // Nếu không có ô SKU thì bỏ đoạn này cũng ok.
  if (nameInput && skuInput) {
    nameInput.addEventListener('input', () => {
      if (skuInput.value.trim() !== '') return;
      const base = slugSkuBase(nameInput.value) || 'PRD';
      skuInput.value = `${base}-${rand4()}`;
    });
  }

  // ===== Variants dynamic =====
  const btnAddOption = document.getElementById('btnAddOption');
  const optionsWrap = document.getElementById('optionsWrap');
  const variantsTbody = document.getElementById('variantsTbody');

  let optionId = 0;

  function createOptionBlock() {
    const id = optionId++;
    const div = document.createElement('div');
    div.className = "p-4 rounded-xl border bg-gray-50 dark:bg-gray-800/40";
    div.dataset.optionId = id;

    div.innerHTML = `
      <div class="flex items-start justify-between gap-3">
        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-medium mb-1">Tên nhóm phân loại</label>
            <input type="text" class="opt-name w-full rounded-lg border-gray-300 focus:ring-primary focus:border-primary"
              placeholder="Ví dụ: Màu sắc, Kích thước">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Thêm giá trị (Enter)</label>
            <input type="text" class="opt-value-input w-full rounded-lg border-gray-300 focus:ring-primary focus:border-primary"
              placeholder="Nhập rồi nhấn Enter">
          </div>
        </div>
        <button type="button" class="btn-remove text-gray-400 hover:text-red-500 p-2 rounded-lg">
          <span class="material-icons-round">delete_outline</span>
        </button>
      </div>

      <div class="mt-3 flex flex-wrap gap-2 opt-values"></div>
    `;

    // remove option
    div.querySelector('.btn-remove').addEventListener('click', () => {
      div.remove();
      rebuildVariants();
    });

    // add chip on Enter
    const input = div.querySelector('.opt-value-input');
    input.addEventListener('keydown', (e) => {
      if (e.key !== 'Enter') return;
      e.preventDefault();
      const v = input.value.trim();
      if (!v) return;

      addChip(div, v);
      input.value = '';
      rebuildVariants();
    });

    // change name rebuild
    div.querySelector('.opt-name').addEventListener('input', rebuildVariants);

    return div;
  }

  function addChip(optionDiv, value) {
    const container = optionDiv.querySelector('.opt-values');
    // prevent duplicates in same option
    const exists = [...container.querySelectorAll('[data-val]')].some(x => x.dataset.val === value);
    if (exists) return;

    const chip = document.createElement('span');
    chip.className = "inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-sm font-medium bg-orange-100 text-orange-700 border border-orange-200";
    chip.dataset.val = value;
    chip.innerHTML = `
      <span>${escapeHtml(value)}</span>
      <button type="button" class="hover:text-orange-900">
        <span class="material-icons-round text-sm">close</span>
      </button>
    `;

    chip.querySelector('button').addEventListener('click', () => {
      chip.remove();
      rebuildVariants();
    });

    container.appendChild(chip);
  }

  function escapeHtml(str){
    return str.replace(/[&<>"']/g, m => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
    }[m]));
  }

  function getOptionsData() {
    const blocks = [...optionsWrap.querySelectorAll('[data-option-id]')];
    return blocks.map(b => {
      const name = b.querySelector('.opt-name').value.trim();
      const values = [...b.querySelectorAll('.opt-values [data-val]')].map(x => x.dataset.val);
      return { name, values };
    }).filter(o => o.name && o.values.length > 0);
  }

  function cartesian(arrays) {
    return arrays.reduce((acc, cur) => {
      const res = [];
      acc.forEach(a => cur.forEach(b => res.push([...a, b])));
      return res;
    }, [[]]);
  }

  function rebuildVariants() {
    const options = getOptionsData();
    variantsTbody.innerHTML = '';

    if (options.length === 0) return;

    const combos = cartesian(options.map(o => o.values)); // array of value arrays
    const optionNames = options.map(o => o.name);

    combos.forEach((vals, idx) => {
      const label = vals.map((v, i) => `${optionNames[i]}: ${v}`).join(' • ');

      // sku gợi ý theo tên sp + idx
      const base = slugSkuBase(nameInput?.value || '') || 'PRD';
      const skuSuggest = `${base}-${String(idx+1).padStart(3,'0')}`;

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="px-4 py-3 text-gray-800 dark:text-gray-100">
          ${escapeHtml(label)}
          <input type="hidden" name="variants[${idx}][label]" value="${escapeHtml(label)}">
          <input type="hidden" name="variants[${idx}][options]" value="${escapeHtml(JSON.stringify(vals))}">
        </td>

        <td class="px-4 py-2">
          <input name="variants[${idx}][sku]" value="${skuSuggest}"
            class="w-full rounded-lg border-gray-300 focus:ring-primary focus:border-primary">
        </td>

        <td class="px-4 py-2">
          <input name="variants[${idx}][price]" type="number" min="0"
            class="w-full rounded-lg border-gray-300 focus:ring-primary focus:border-primary" placeholder="0">
        </td>

        <td class="px-4 py-2">
          <input name="variants[${idx}][quantity]" type="number" min="0"
            class="w-full rounded-lg border-gray-300 focus:ring-primary focus:border-primary" placeholder="0">
        </td>
      `;
      variantsTbody.appendChild(tr);
    });
  }

  btnAddOption?.addEventListener('click', () => {
    optionsWrap.appendChild(createOptionBlock());
  });

  // Nếu đổi tên sản phẩm -> update lại SKU gợi ý của bảng
  nameInput?.addEventListener('input', () => rebuildVariants());
</script>
@endpush
