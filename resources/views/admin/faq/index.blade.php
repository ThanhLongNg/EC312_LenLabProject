@extends('admin.layout')

@section('title', 'Quản lý FAQ Chatbot')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Quản lý FAQ Chatbot</h2>
            <p class="text-muted mb-0">Quản lý câu hỏi thường gặp cho chatbot tự động</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFaqModal">
            <i class="fas fa-plus me-2"></i>Thêm FAQ
        </button>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm theo câu hỏi, từ khóa...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="categoryFilter">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $key => $name)
                            <option value="{{ $key }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">Trạng thái</option>
                        <option value="active">Hoạt động</option>
                        <option value="inactive">Tạm dừng</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                        <i class="fas fa-redo me-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="faqTable">
                    <thead class="table-light">
                        <tr>
                            <th width="50">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>CÂU HỎI</th>
                            <th width="120">DANH MỤC</th>
                            <th width="100">ĐỘ ƯU TIÊN</th>
                            <th width="100">SỬ DỤNG</th>
                            <th width="100">TRẠNG THÁI</th>
                            <th width="120">HÀNH ĐỘNG</th>
                        </tr>
                    </thead>
                    <tbody id="faqTableBody">
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Hiển thị <span id="showingFrom">1</span> - <span id="showingTo">20</span> trong số <span id="totalItems">0</span> FAQ
                </div>
                <nav>
                    <ul class="pagination mb-0" id="pagination">
                        <!-- Pagination will be generated via JS -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card mt-3" id="bulkActions" style="display: none;">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">Đã chọn <span id="selectedCount">0</span> FAQ:</span>
                <button type="button" class="btn btn-sm btn-success" onclick="bulkActivate()">
                    <i class="fas fa-check me-1"></i>Kích hoạt
                </button>
                <button type="button" class="btn btn-sm btn-warning" onclick="bulkDeactivate()">
                    <i class="fas fa-pause me-1"></i>Tạm dừng
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete()">
                    <i class="fas fa-trash me-1"></i>Xóa
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit FAQ Modal -->
<div class="modal fade" id="createFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm FAQ mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="faqForm">
                <div class="modal-body">
                    <input type="hidden" id="faqId" name="id">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Chọn danh mục</option>
                                @foreach($categories as $key => $name)
                                    <option value="{{ $key }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Độ ưu tiên</label>
                            <input type="number" class="form-control" id="priority" name="priority" min="0" max="100" value="0">
                            <small class="text-muted">Số càng cao càng ưu tiên (0-100)</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Câu hỏi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="question" name="question" required maxlength="500">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Từ khóa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="keywordsInput" placeholder="Nhập từ khóa, cách nhau bằng dấu phẩy">
                        <small class="text-muted">Ví dụ: giao hàng, ship, vận chuyển, delivery</small>
                        <div id="keywordTags" class="mt-2"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Câu trả lời <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="answer" name="answer" rows="5" required maxlength="2000"></textarea>
                        <div class="text-end">
                            <small class="text-muted"><span id="answerCount">0</span>/2000 ký tự</small>
                        </div>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="isActive" name="is_active" checked>
                        <label class="form-check-label" for="isActive">
                            Kích hoạt FAQ này
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Lưu FAQ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.keyword-tag {
    display: inline-block;
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 8px;
    margin: 2px;
    border-radius: 12px;
    font-size: 12px;
    border: 1px solid #bbdefb;
}

.keyword-tag .remove {
    margin-left: 5px;
    cursor: pointer;
    color: #f44336;
    font-weight: bold;
}

.usage-badge {
    background: #f5f5f5;
    color: #666;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 11px;
}

.priority-badge {
    background: linear-gradient(45deg, #ff9800, #f57c00);
    color: white;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 11px;
}
</style>
@endpush

@push('scripts')
<script>
let currentPage = 1;
let keywords = [];

$(document).ready(function() {
    loadFaqs();
    
    // Search functionality
    $('#searchInput').on('input', debounce(function() {
        currentPage = 1;
        loadFaqs();
    }, 500));
    
    // Filter functionality
    $('#categoryFilter, #statusFilter').on('change', function() {
        currentPage = 1;
        loadFaqs();
    });
    
    // Keywords input
    $('#keywordsInput').on('keypress', function(e) {
        if (e.which === 13 || e.which === 188) { // Enter or comma
            e.preventDefault();
            addKeyword();
        }
    });
    
    // Answer character count
    $('#answer').on('input', function() {
        $('#answerCount').text($(this).val().length);
    });
    
    // Form submission
    $('#faqForm').on('submit', function(e) {
        e.preventDefault();
        saveFaq();
    });
    
    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.row-checkbox').prop('checked', $(this).prop('checked'));
        updateBulkActions();
    });
});

function loadFaqs() {
    const params = {
        page: currentPage,
        search: $('#searchInput').val(),
        category: $('#categoryFilter').val(),
        status: $('#statusFilter').val()
    };
    
    $.get('/admin/faq/list', params)
        .done(function(response) {
            if (response.success) {
                renderFaqTable(response.data);
                renderPagination(response.data);
                updateStats(response.data);
            }
        })
        .fail(function() {
            showAlert('Lỗi tải dữ liệu', 'error');
        });
}

function renderFaqTable(data) {
    const tbody = $('#faqTableBody');
    tbody.empty();
    
    if (data.data.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">Không có FAQ nào</p>
                </td>
            </tr>
        `);
        return;
    }
    
    data.data.forEach(function(faq) {
        const categoryBadge = getCategoryBadge(faq.category);
        const statusBadge = faq.is_active 
            ? '<span class="badge bg-success">Hoạt động</span>'
            : '<span class="badge bg-secondary">Tạm dừng</span>';
        
        tbody.append(`
            <tr>
                <td>
                    <input type="checkbox" class="form-check-input row-checkbox" value="${faq.id}">
                </td>
                <td>
                    <div class="fw-medium">${faq.question}</div>
                    <div class="text-muted small mt-1">
                        ${faq.keywords.slice(0, 3).map(k => `<span class="keyword-tag">${k}</span>`).join('')}
                        ${faq.keywords.length > 3 ? `<span class="text-muted">+${faq.keywords.length - 3} khác</span>` : ''}
                    </div>
                </td>
                <td>${categoryBadge}</td>
                <td>
                    <span class="priority-badge">${faq.priority}</span>
                </td>
                <td>
                    <span class="usage-badge">${faq.usage_count}</span>
                </td>
                <td>${statusBadge}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary" onclick="editFaq(${faq.id})" title="Chỉnh sửa">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-outline-${faq.is_active ? 'warning' : 'success'}" 
                                onclick="toggleStatus(${faq.id})" title="${faq.is_active ? 'Tạm dừng' : 'Kích hoạt'}">
                            <i class="fas fa-${faq.is_active ? 'pause' : 'play'}"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="deleteFaq(${faq.id})" title="Xóa">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `);
    });
    
    // Update bulk actions
    $('.row-checkbox').on('change', updateBulkActions);
}

function getCategoryBadge(category) {
    const categories = {
        'giao_hang': '<span class="badge bg-primary">Giao hàng</span>',
        'doi_tra': '<span class="badge bg-info">Đổi trả</span>',
        'san_pham': '<span class="badge bg-success">Sản phẩm</span>',
        'thanh_toan': '<span class="badge bg-warning">Thanh toán</span>',
        'ho_tro': '<span class="badge bg-danger">Hỗ trợ</span>',
        'general': '<span class="badge bg-secondary">Tổng quát</span>'
    };
    return categories[category] || '<span class="badge bg-light text-dark">Khác</span>';
}

function addKeyword() {
    const input = $('#keywordsInput');
    const keyword = input.val().trim().replace(',', '');
    
    if (keyword && !keywords.includes(keyword)) {
        keywords.push(keyword);
        renderKeywords();
        input.val('');
    }
}

function removeKeyword(keyword) {
    keywords = keywords.filter(k => k !== keyword);
    renderKeywords();
}

function renderKeywords() {
    const container = $('#keywordTags');
    container.empty();
    
    keywords.forEach(function(keyword) {
        container.append(`
            <span class="keyword-tag">
                ${keyword}
                <span class="remove" onclick="removeKeyword('${keyword}')">&times;</span>
            </span>
        `);
    });
}

function saveFaq() {
    const formData = {
        category: $('#category').val(),
        question: $('#question').val(),
        answer: $('#answer').val(),
        keywords: keywords,
        priority: $('#priority').val() || 0,
        is_active: $('#isActive').prop('checked')
    };
    
    const faqId = $('#faqId').val();
    const url = faqId ? `/admin/faq/${faqId}` : '/admin/faq';
    const method = faqId ? 'PUT' : 'POST';
    
    $.ajax({
        url: url,
        method: method,
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .done(function(response) {
        if (response.success) {
            $('#createFaqModal').modal('hide');
            loadFaqs();
            showAlert(response.message, 'success');
            resetForm();
        }
    })
    .fail(function(xhr) {
        const errors = xhr.responseJSON?.errors;
        if (errors) {
            Object.keys(errors).forEach(function(field) {
                showAlert(errors[field][0], 'error');
            });
        } else {
            showAlert('Có lỗi xảy ra', 'error');
        }
    });
}

function editFaq(id) {
    // Load FAQ data and populate form
    $.get(`/admin/faq/${id}/edit`)
        .done(function(response) {
            // Populate form with FAQ data
            $('#faqId').val(response.id);
            $('#category').val(response.category);
            $('#question').val(response.question);
            $('#answer').val(response.answer);
            $('#priority').val(response.priority);
            $('#isActive').prop('checked', response.is_active);
            
            keywords = response.keywords || [];
            renderKeywords();
            
            $('.modal-title').text('Chỉnh sửa FAQ');
            $('#createFaqModal').modal('show');
        });
}

function toggleStatus(id) {
    $.post(`/admin/faq/${id}/toggle-active`, {
        _token: $('meta[name="csrf-token"]').attr('content')
    })
    .done(function(response) {
        if (response.success) {
            loadFaqs();
            showAlert(response.message, 'success');
        }
    });
}

function deleteFaq(id) {
    if (confirm('Bạn có chắc muốn xóa FAQ này?')) {
        $.ajax({
            url: `/admin/faq/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            if (response.success) {
                loadFaqs();
                showAlert(response.message, 'success');
            }
        });
    }
}

function resetForm() {
    $('#faqForm')[0].reset();
    $('#faqId').val('');
    keywords = [];
    renderKeywords();
    $('.modal-title').text('Thêm FAQ mới');
}

function updateBulkActions() {
    const selected = $('.row-checkbox:checked').length;
    $('#selectedCount').text(selected);
    $('#bulkActions').toggle(selected > 0);
    $('#selectAll').prop('indeterminate', selected > 0 && selected < $('.row-checkbox').length);
}

function resetFilters() {
    $('#searchInput, #categoryFilter, #statusFilter').val('');
    currentPage = 1;
    loadFaqs();
}

function renderPagination(data) {
    const pagination = $('#pagination');
    pagination.empty();
    
    if (data.last_page <= 1) return;
    
    // Previous button
    if (data.current_page > 1) {
        pagination.append(`
            <li class="page-item">
                <a class="page-link" href="#" onclick="changePage(${data.current_page - 1})">‹</a>
            </li>
        `);
    }
    
    // Page numbers
    const start = Math.max(1, data.current_page - 2);
    const end = Math.min(data.last_page, data.current_page + 2);
    
    for (let i = start; i <= end; i++) {
        pagination.append(`
            <li class="page-item ${i === data.current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
            </li>
        `);
    }
    
    // Next button
    if (data.current_page < data.last_page) {
        pagination.append(`
            <li class="page-item">
                <a class="page-link" href="#" onclick="changePage(${data.current_page + 1})">›</a>
            </li>
        `);
    }
}

function changePage(page) {
    currentPage = page;
    loadFaqs();
}

function updateStats(data) {
    $('#showingFrom').text(data.from || 0);
    $('#showingTo').text(data.to || 0);
    $('#totalItems').text(data.total || 0);
}

function bulkActivate() {
    const selected = $('.row-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selected.length === 0) return;
    
    if (confirm(`Kích hoạt ${selected.length} FAQ đã chọn?`)) {
        // Implement bulk activate
        console.log('Bulk activate:', selected);
    }
}

function bulkDeactivate() {
    const selected = $('.row-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selected.length === 0) return;
    
    if (confirm(`Tạm dừng ${selected.length} FAQ đã chọn?`)) {
        // Implement bulk deactivate
        console.log('Bulk deactivate:', selected);
    }
}

function bulkDelete() {
    const selected = $('.row-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selected.length === 0) return;
    
    if (confirm(`Xóa ${selected.length} FAQ đã chọn? Hành động này không thể hoàn tác.`)) {
        $.ajax({
            url: '/admin/faq',
            method: 'DELETE',
            data: { ids: selected },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            if (response.success) {
                loadFaqs();
                showAlert(response.message, 'success');
                $('#selectAll').prop('checked', false);
                updateBulkActions();
            }
        })
        .fail(function() {
            showAlert('Có lỗi xảy ra khi xóa', 'error');
        });
    }
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showAlert(message, type) {
    // Simple alert implementation - you can enhance this
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Add to top of content area
    $('.container-fluid').prepend(alertHtml);
    
    // Auto dismiss after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
@endpush