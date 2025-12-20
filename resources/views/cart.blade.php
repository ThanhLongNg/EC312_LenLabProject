<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Giỏ hàng - LENLAB</title>
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&family=Noto+Sans:wght@400;500;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#FAC638",
                        "background-dark": "#1a1a1a",
                        "surface-dark": "#2d2d2d",
                        "card-dark": "#333333"
                    },
                    fontFamily: {
                        "display": ["Spline Sans", "sans-serif"],
                        "body": ["Noto Sans", "sans-serif"]
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Spline Sans', sans-serif;
            background: #1a1a1a;
            min-height: 100vh;
            padding-bottom: 100px; /* Space for fixed bottom bar */
        }
        
        .cart-container {
            background: #1a1a1a;
            max-width: 400px;
            margin: 0 auto;
            min-height: 100vh;
        }
        
        .cart-item {
            background: rgba(45, 45, 45, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .cart-item:hover {
            background: rgba(60, 60, 60, 0.8);
        }
        
        .quantity-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(45, 45, 45, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .quantity-btn:hover {
            background: rgba(60, 60, 60, 0.9);
            border-color: #FAC638;
        }
        
        .quantity-btn.add {
            background: #FAC638;
            color: #1a1a1a;
        }
        
        .quantity-btn.add:hover {
            background: #f59e0b;
        }
        
        /* Checkbox Styles */
        .item-checkbox {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.3);
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .item-checkbox.checked {
            background: #FAC638;
            border-color: #FAC638;
        }
        
        .item-checkbox.checked::after {
            content: '✓';
            color: #1a1a1a;
            font-size: 12px;
            font-weight: bold;
        }
        
        .cart-item.unselected {
            opacity: 0.6;
        }
        
        .voucher-section {
            background: rgba(45, 45, 45, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 100%;
            overflow: hidden;
        }
        
        .voucher-input {
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 100%;
            overflow: hidden;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .voucher-input.invalid {
            border-color: rgba(239, 68, 68, 0.5);
            background: rgba(26, 26, 26, 0.9);
        }
        
        .voucher-input select {
            max-width: calc(100% - 40px);
            width: calc(100% - 40px);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding-right: 0;
            background: transparent;
            border: none;
            outline: none;
            appearance: none;
            cursor: pointer;
            color: white;
        }
        
        .voucher-input select option {
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            background: #2d2d2d;
            color: white;
            padding: 8px 12px;
        }
        
        .voucher-input select option:first-child {
            color: #9ca3af;
        }
        
        .voucher-input:focus-within {
            border-color: #FAC638;
        }
        
        .checkout-btn {
            background: linear-gradient(135deg, #FAC638, #f59e0b);
            transition: all 0.3s ease;
        }
        
        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(250, 198, 56, 0.4);
        }
        
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }
        
        .loading {
            text-align: center;
            padding: 40px 20px;
        }
    </style>
</head>

<body class="bg-background-dark">
    <div class="cart-container">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-700">
            <button onclick="window.location.href='/'" class="text-white hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </button>
            <h1 class="text-white font-semibold text-lg">
                Giỏ hàng <span class="text-primary" id="cartCount">(0)</span>
            </h1>
            <button class="text-white hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-2xl">more_vert</span>
            </button>
        </div>

        <!-- Cart Items -->
        <div class="p-4" id="cartItemsContainer">
            <!-- Loading -->
            <div class="loading" id="loadingCart">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-4"></div>
                <p class="text-gray-400">Đang tải giỏ hàng...</p>
            </div>
            
            <!-- Cart Items will be loaded here -->
            <div id="cartItems" class="space-y-4"></div>
        </div>

        <!-- Voucher Section -->
        <div class="px-4 mb-6" id="voucherSection" style="display: none;">
            <div class="voucher-section rounded-xl p-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-primary/20 rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-sm">local_offer</span>
                    </div>
                    <span class="text-white font-medium">Chọn mã giảm giá</span>
                    <span class="text-primary text-sm font-semibold ml-auto" id="voucherStatus">CHỌN</span>
                </div>
                
                <!-- Voucher Selection Button -->
                <button onclick="selectVoucher()" class="voucher-input rounded-lg p-3 flex items-center justify-between w-full hover:bg-opacity-80 transition-all">
                    <span class="text-gray-400" id="voucherPlaceholder">Chọn voucher...</span>
                    <span class="material-symbols-outlined text-gray-400">chevron_right</span>
                </button>
                
                <p id="voucherMessage" class="text-sm mt-2 hidden"></p>
                
                <!-- Applied Voucher Display -->
                <div id="appliedVoucher" class="hidden mt-3 p-3 bg-primary/10 border border-primary/30 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-sm">check_circle</span>
                            <span class="text-white text-sm font-medium" id="appliedVoucherName"></span>
                        </div>
                        <button onclick="removeVoucher()" class="text-gray-400 hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                    <p class="text-primary text-xs mt-1" id="appliedVoucherDesc"></p>
                </div>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="px-4 mb-20" id="summarySection" style="display: none;">
            <div class="space-y-3 mb-6">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Tiền hàng</span>
                    <span class="text-white font-semibold" id="subtotal">0đ</span>
                </div>
                
                
                <div id="discountRow" class="flex justify-between items-center hidden">
                    <span class="text-gray-400">Giảm giá</span>
                    <span class="text-green-400 font-semibold" id="discountAmount">-0đ</span>
                </div>
                
                <hr class="border-gray-700">
                
                <div class="flex justify-between items-center">
                    <span class="text-white text-lg font-bold">Tạm tính</span>
                    <span class="text-primary text-xl font-bold" id="totalAmount">0đ</span>
                </div>
            </div>
        </div>

        <!-- Empty Cart -->
        <div class="empty-cart hidden" id="emptyCart">
            <div class="w-24 h-24 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-gray-500 text-4xl">shopping_cart</span>
            </div>
            <h3 class="text-white text-xl font-semibold mb-2">Giỏ hàng trống</h3>
            <p class="text-gray-400 mb-6">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
            <button onclick="window.location.href='/san-pham'" class="checkout-btn px-8 py-3 rounded-2xl text-background-dark font-bold">
                Mua sắm ngay
            </button>
        </div>
    </div>

    <!-- Fixed Checkout Button -->
    <div class="fixed bottom-0 left-1/2 transform -translate-x-1/2 w-full max-width-400 bg-background-dark/95 backdrop-blur-md border-t border-gray-700 p-4" id="checkoutBar" style="display: none; max-width: 400px;">
        <button onclick="checkout()" class="checkout-btn w-full py-4 rounded-2xl text-background-dark font-bold text-lg flex items-center justify-center gap-2">
            Thanh toán
            <span class="material-symbols-outlined">arrow_forward</span>
        </button>
    </div>

    <script>
        let cart = [];
        let selectedItems = new Set(); // Track selected items
        let discountAmount = 0;
        let discountPercent = 0;
        let availableVouchers = [];
        let appliedVoucher = null;

        $(document).ready(function() {
            loadCart();
            
            // Setup CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Check if returning from voucher selection
            checkVoucherFromUrl();
        });

        function getVariantInfo(item) {
            let variantHtml = '';
            
            if (item.variant_info) {
                let variantInfo;
                try {
                    variantInfo = typeof item.variant_info === 'string' 
                        ? JSON.parse(item.variant_info) 
                        : item.variant_info;
                } catch (e) {
                    variantInfo = {};
                }
                
                if (variantInfo.variant_name) {
                    variantHtml = `<p class="text-gray-400 text-sm mb-2">Loại: ${variantInfo.variant_name}</p>`;
                } else {
                    variantHtml = `<p class="text-gray-400 text-sm mb-2">${item.product?.category || ''}</p>`;
                }
            } else {
                variantHtml = `<p class="text-gray-400 text-sm mb-2">${item.product?.category || ''}</p>`;
            }
            
            return variantHtml;
        }

        function loadCart() {
            $('#loadingCart').show();
            
            @auth
                $.get('/api/cart', function(response) {
                    cart = response.cart || [];
                    renderCart();
                    updateSummary();
                    $('#loadingCart').hide();
                }).fail(function() {
                    $('#loadingCart').hide();
                    showEmptyCart();
                });
            @else
                // For guest users, show empty cart or redirect to login
                $('#loadingCart').hide();
                showEmptyCart();
            @endauth
        }

        function renderCart() {
            const container = $('#cartItems');
            
            if (cart.length === 0) {
                showEmptyCart();
                return;
            }
            
            // Select all items by default on first load
            if (selectedItems.size === 0) {
                cart.forEach(item => selectedItems.add(item.id));
            }
            
            $('#cartCount').text(`(${cart.length})`);
            $('#voucherSection, #summarySection, #checkoutBar').show();
            $('#emptyCart').hide();
            
            let html = '';
            
            cart.forEach(item => {
                const product = item.product || {};
                const imageUrl = product.image && product.image !== 'default.jpg' 
                    ? `/PRODUCT-IMG/${product.image}` 
                    : `https://via.placeholder.com/80x80/FAC638/FFFFFF?text=${encodeURIComponent((product.name || 'SP').substring(0, 2))}`;
                
                const price = parseFloat(product.price) || 0;
                const formattedPrice = price.toLocaleString('vi-VN');
                const isSelected = selectedItems.has(item.id);
                
                html += `
                    <div class="cart-item rounded-xl p-4 ${!isSelected ? 'unselected' : ''}" data-item-id="${item.id}">
                        <div class="flex items-center gap-4">
                            <!-- Checkbox -->
                            <div class="item-checkbox ${isSelected ? 'checked' : ''}" onclick="toggleItemSelection(${item.id})"></div>
                            
                            <!-- Product Image -->
                            <img src="${imageUrl}" 
                                 alt="${product.name || 'Sản phẩm'}" 
                                 class="w-20 h-20 object-cover rounded-xl"
                                 onerror="this.src='https://via.placeholder.com/80x80/FAC638/FFFFFF?text=${encodeURIComponent((product.name || 'SP').substring(0, 2))}'">
                            
                            <!-- Product Info -->
                            <div class="flex-1">
                                <h3 class="text-white font-semibold mb-1">${product.name || 'Sản phẩm'}</h3>
                                ${getVariantInfo(item)}
                                <p class="text-primary font-bold">${formattedPrice}đ</p>
                            </div>
                            
                            <!-- Delete Button -->
                            <button onclick="removeFromCart(${item.id})" class="text-gray-400 hover:text-red-400 transition-colors">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                        
                        <!-- Quantity Controls -->
                        <div class="flex items-center justify-end gap-3 mt-4">
                            <button onclick="updateQuantity(${item.id}, 'decrease')" class="quantity-btn">
                                <span class="material-symbols-outlined text-sm">remove</span>
                            </button>
                            <span class="text-white font-semibold min-w-[20px] text-center">${item.quantity}</span>
                            <button onclick="updateQuantity(${item.id}, 'increase')" class="quantity-btn add">
                                <span class="material-symbols-outlined text-sm">add</span>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            container.html(html);
            
            // Revalidate voucher after rendering cart
            setTimeout(() => {
                if (appliedVoucher) {
                    revalidateVoucher();
                }
            }, 100);
        }

        function showEmptyCart() {
            $('#cartCount').text('(0)');
            $('#cartItems, #voucherSection, #summarySection, #checkoutBar').hide();
            $('#emptyCart').removeClass('hidden');
        }

        function updateQuantity(itemId, action) {
            $.post('/api/cart/update', {
                id: itemId,
                action: action
            }, function(response) {
                if (response.success) {
                    loadCart();
                    
                    // Re-validate voucher after quantity change
                    setTimeout(() => {
                        if (appliedVoucher) {
                            revalidateVoucher();
                        }
                    }, 100); // Small delay to ensure cart is loaded
                } else {
                    alert('Có lỗi xảy ra, vui lòng thử lại!');
                }
            }).fail(function() {
                alert('Có lỗi xảy ra, vui lòng thử lại!');
            });
        }

        function removeFromCart(itemId) {
            if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                $.post('/api/cart/delete', {
                    id: itemId
                }, function(response) {
                    if (response.success) {
                        loadCart();
                    } else {
                        alert('Có lỗi xảy ra, vui lòng thử lại!');
                    }
                }).fail(function() {
                    alert('Có lỗi xảy ra, vui lòng thử lại!');
                });
            }
        }

        function toggleItemSelection(itemId) {
            if (selectedItems.has(itemId)) {
                selectedItems.delete(itemId);
            } else {
                selectedItems.add(itemId);
            }
            
            // Update UI
            const itemElement = $(`.cart-item[data-item-id="${itemId}"]`);
            const checkbox = itemElement.find('.item-checkbox');
            
            if (selectedItems.has(itemId)) {
                itemElement.removeClass('unselected');
                checkbox.addClass('checked');
            } else {
                itemElement.addClass('unselected');
                checkbox.removeClass('checked');
            }
            
            updateSummary();
            
            // Re-validate voucher if one is applied
            if (appliedVoucher) {
                revalidateVoucher();
            }
        }

        function updateSummary() {
            let subtotal = 0;
            let selectedCount = 0;
            
            // Only calculate for selected items
            cart.forEach(item => {
                if (selectedItems.has(item.id)) {
                    const price = parseFloat(item.product?.price) || 0;
                    subtotal += price * item.quantity;
                    selectedCount++;
                }
            });
            
            const discount = discountPercent > 0 ? (subtotal * discountPercent / 100) : discountAmount;
            const total = subtotal - discount;
            
            console.log('Summary update:', {
                subtotal: subtotal,
                discountPercent: discountPercent,
                discountAmount: discountAmount,
                discount: discount,
                total: total,
                selectedCount: selectedCount
            });
            
            $('#subtotal').text(subtotal.toLocaleString('vi-VN') + 'đ');
            $('#totalAmount').text(total.toLocaleString('vi-VN') + 'đ');
            
            // Update summary section
            const summaryTitle = selectedCount > 0 ? `Tiền hàng (${selectedCount} sản phẩm)` : 'Tiền hàng';
            $('#summarySection .text-gray-400').first().text(summaryTitle);
            
            if (discount > 0) {
                $('#discountRow').removeClass('hidden');
                $('#discountAmount').text('-' + discount.toLocaleString('vi-VN') + 'đ');
            } else {
                $('#discountRow').addClass('hidden');
            }
            
            // Show/hide checkout button based on selection
            if (selectedCount > 0) {
                $('#checkoutBar').show();
            } else {
                $('#checkoutBar').hide();
            }
        }

        function selectVoucher() {
            // Navigate to voucher selection page with return URL
            window.location.href = '/vouchers?return=cart';
        }

        function checkVoucherFromUrl() {
            // Check if there's a selected voucher in URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const selectedVoucher = urlParams.get('voucher');
            
            if (selectedVoucher) {
                console.log('Auto-applying voucher from URL:', selectedVoucher);
                
                // Wait for cart to load and ensure selectedItems are set
                const waitForCartAndApplyVoucher = () => {
                    if (cart.length > 0 && selectedItems.size > 0) {
                        console.log('Cart loaded, applying voucher:', {
                            cartLength: cart.length,
                            selectedItemsSize: selectedItems.size
                        });
                        applyVoucher(selectedVoucher);
                    } else {
                        console.log('Waiting for cart to load...', {
                            cartLength: cart.length,
                            selectedItemsSize: selectedItems.size
                        });
                        setTimeout(waitForCartAndApplyVoucher, 200);
                    }
                };
                
                // Start checking after a short delay
                setTimeout(waitForCartAndApplyVoucher, 500);
                
                // Clean up URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }

        function revalidateVoucher() {
            if (!appliedVoucher) return;
            
            // Calculate current cart total for selected items
            let cartTotal = 0;
            cart.forEach(item => {
                if (selectedItems.has(item.id)) {
                    const price = parseFloat(item.product?.price) || 0;
                    cartTotal += price * item.quantity;
                }
            });
            
            console.log('Revalidating voucher:', {
                voucherCode: appliedVoucher.code,
                minOrderValue: appliedVoucher.min_order_value,
                cartTotal: cartTotal,
                isValid: !appliedVoucher.min_order_value || cartTotal >= appliedVoucher.min_order_value
            });
            
            // Check if voucher is still valid
            // Convert min_order_value to number to ensure proper comparison
            const minOrderValue = parseFloat(appliedVoucher.min_order_value) || 0;
            
            if (minOrderValue > 0 && cartTotal < minOrderValue) {
                // Voucher no longer valid - show error message
                const needed = minOrderValue - cartTotal;
                showVoucherMessage(`Đơn hàng tối thiểu ${minOrderValue.toLocaleString('vi-VN')}đ để sử dụng voucher này. Cần thêm ${needed.toLocaleString('vi-VN')}đ`, 'error');
                
                // Update voucher status to show it's temporarily invalid
                $('#voucherStatus').text('TẠM KHÓA').removeClass('text-green-400 text-primary').addClass('text-red-400');
                
                // Hide applied voucher display but keep the voucher info
                $('#appliedVoucher').addClass('hidden');
                
                // Add invalid styling
                $('.voucher-input').addClass('invalid');
                
                // Update placeholder to show voucher is selected but not applicable
                let voucherDisplay = appliedVoucher.code;
                if (appliedVoucher.type === 'percentage' || appliedVoucher.type === 'percent') {
                    voucherDisplay += ` • Giảm ${appliedVoucher.discount_value}%`;
                } else if (appliedVoucher.type === 'fixed_amount' || appliedVoucher.type === 'fixed') {
                    voucherDisplay += ` • Giảm ${parseInt(appliedVoucher.discount_value).toLocaleString('vi-VN')}đ`;
                }
                $('#voucherPlaceholder').text(voucherDisplay).removeClass('text-gray-400 text-white').addClass('text-red-400');
                
                // Reset discount but keep voucher info for re-validation
                discountAmount = 0;
                discountPercent = 0;
                updateSummary();
            } else {
                // Voucher is valid - hide error message and apply discount
                $('#voucherMessage').addClass('hidden');
                
                // Update status back to applied
                $('#voucherStatus').text('ĐÃ ÁP DỤNG').removeClass('text-red-400 text-primary').addClass('text-green-400');
                
                // Show applied voucher display
                showAppliedVoucher();
                
                // Recalculate discount
                if (appliedVoucher.type === 'percentage' || appliedVoucher.type === 'percent') {
                    discountPercent = parseFloat(appliedVoucher.discount_value);
                    discountAmount = 0;
                } else if (appliedVoucher.type === 'fixed_amount' || appliedVoucher.type === 'fixed') {
                    discountAmount = parseFloat(appliedVoucher.discount_value);
                    discountPercent = 0;
                } else if (appliedVoucher.type === 'free_shipping') {
                    discountAmount = 30000;
                    discountPercent = 0;
                } else {
                    discountAmount = parseFloat(appliedVoucher.discount_value);
                    discountPercent = 0;
                }
                
                updateSummary();
            }
        }

        function applyVoucher(voucherCode = null) {
            const code = voucherCode || $('#voucherSelect').val();
            
            if (!code) {
                showVoucherMessage('Vui lòng chọn voucher', 'error');
                return;
            }

            // Calculate current cart total for selected items
            let cartTotal = 0;
            cart.forEach(item => {
                if (selectedItems.has(item.id)) {
                    const price = parseFloat(item.product?.price) || 0;
                    cartTotal += price * item.quantity;
                }
            });
            
            console.log('Applying voucher:', {
                code: code,
                cartTotal: cartTotal,
                selectedItems: Array.from(selectedItems),
                cart: cart,
                cartLength: cart.length,
                selectedItemsSize: selectedItems.size
            });
            
            // Validation before sending request
            if (cartTotal === 0) {
                console.error('Cart total is 0, cannot apply voucher');
                showVoucherMessage('Giỏ hàng trống hoặc chưa chọn sản phẩm nào', 'error');
                return;
            }
            
            $.post('/api/vouchers/apply', {
                voucher_code: code,
                cart_total: cartTotal
            }, function(response) {
                console.log('Voucher apply response:', response);
                
                if (response.success) {
                    appliedVoucher = response.voucher;
                    
                    // Calculate discount based on voucher type
                    if (appliedVoucher.type === 'percentage' || appliedVoucher.type === 'percent') {
                        discountPercent = parseFloat(appliedVoucher.discount_value);
                        discountAmount = 0;
                    } else if (appliedVoucher.type === 'fixed_amount' || appliedVoucher.type === 'fixed') {
                        discountAmount = parseFloat(appliedVoucher.discount_value);
                        discountPercent = 0;
                    } else if (appliedVoucher.type === 'free_shipping') {
                        // Handle free shipping (could be implemented later)
                        discountAmount = 30000; // Assume 30k shipping fee
                        discountPercent = 0;
                    } else {
                        // Fallback - assume it's a fixed amount
                        discountAmount = parseFloat(appliedVoucher.discount_value);
                        discountPercent = 0;
                    }
                    
                    showAppliedVoucher();
                    showVoucherMessage('Áp dụng voucher thành công!', 'success');
                    $('#voucherStatus').text('ĐÃ ÁP DỤNG').addClass('text-green-400').removeClass('text-primary');
                    updateSummary();
                    
                    // Immediately revalidate to ensure voucher conditions are met
                    setTimeout(() => {
                        revalidateVoucher();
                    }, 100);
                } else {
                    discountAmount = 0;
                    discountPercent = 0;
                    appliedVoucher = null;
                    showVoucherMessage(response.message || 'Voucher không hợp lệ', 'error');
                    updateSummary();
                }
            }).fail(function(xhr, status, error) {
                console.error('Voucher apply failed:', {
                    xhr: xhr,
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                showVoucherMessage('Có lỗi xảy ra, vui lòng thử lại!', 'error');
            });
        }

        function showAppliedVoucher() {
            if (appliedVoucher) {
                let description = '';
                if (appliedVoucher.type === 'percentage' || appliedVoucher.type === 'percent') {
                    description = `Giảm ${appliedVoucher.discount_value}%`;
                } else if (appliedVoucher.type === 'fixed_amount' || appliedVoucher.type === 'fixed') {
                    description = `Giảm ${parseInt(appliedVoucher.discount_value).toLocaleString('vi-VN')}đ`;
                } else if (appliedVoucher.type === 'free_shipping') {
                    description = 'Miễn phí vận chuyển';
                } else {
                    // Fallback
                    description = `Giảm ${appliedVoucher.discount_value}${appliedVoucher.type === 'percent' ? '%' : 'đ'}`;
                }
                
                $('#appliedVoucherName').text(appliedVoucher.code);
                $('#appliedVoucherDesc').text(description);
                $('#appliedVoucher').removeClass('hidden');
                $('#voucherPlaceholder').text(`${appliedVoucher.code} • ${description}`).removeClass('text-gray-400 text-red-400').addClass('text-white');
                
                // Remove invalid styling
                $('.voucher-input').removeClass('invalid');
            }
        }

        function removeVoucher() {
            $.post('/api/vouchers/remove', {}, function(response) {
                if (response.success) {
                    appliedVoucher = null;
                    discountAmount = 0;
                    discountPercent = 0;
                    
                    $('#appliedVoucher').addClass('hidden');
                    $('#voucherPlaceholder').text('Chọn voucher...').removeClass('text-white').addClass('text-gray-400');
                    $('#voucherStatus').text('CHỌN').removeClass('text-green-400').addClass('text-primary');
                    $('#voucherMessage').addClass('hidden'); // Hide error message
                    
                    showVoucherMessage('Đã hủy voucher', 'success');
                    updateSummary();
                }
            }).fail(function() {
                showVoucherMessage('Có lỗi xảy ra khi hủy voucher', 'error');
            });
        }

        function showVoucherMessage(message, type) {
            const messageEl = $('#voucherMessage');
            messageEl.text(message)
                    .removeClass('hidden text-green-400 text-red-400')
                    .addClass(type === 'success' ? 'text-green-400' : 'text-red-400');
            
            setTimeout(() => {
                messageEl.addClass('hidden');
            }, 3000);
        }

        function checkout() {
            if (selectedItems.size === 0) {
                alert('Vui lòng chọn ít nhất một sản phẩm để thanh toán!');
                return;
            }
            
            @auth
                // Store selected items in session
                const selectedItemIds = Array.from(selectedItems);
                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                // Save selected items to session
                $.post('/api/checkout/set-selected-items', {
                    selected_items: selectedItemIds
                }, function(response) {
                    if (response.success) {
                        // Save voucher discount to session if applied
                        if (appliedVoucher) {
                            const discount = discountPercent > 0 ? 
                                (calculateSelectedSubtotal() * discountPercent / 100) : 
                                discountAmount;
                            sessionStorage.setItem('voucher_discount', discount);
                        }
                        
                        window.location.href = '/checkout';
                    } else {
                        alert('Có lỗi xảy ra: ' + response.message);
                    }
                }).fail(function() {
                    alert('Có lỗi xảy ra, vui lòng thử lại!');
                });
            @else
                if (confirm('Vui lòng đăng nhập để tiếp tục thanh toán. Chuyển đến trang đăng nhập?')) {
                    window.location.href = '{{ route("login") }}';
                }
            @endauth
        }

        function calculateSelectedSubtotal() {
            let subtotal = 0;
            cart.forEach(item => {
                if (selectedItems.has(item.id)) {
                    const price = parseFloat(item.product?.price) || 0;
                    subtotal += price * item.quantity;
                }
            });
            return subtotal;
        }
    </script>
</body>
</html>