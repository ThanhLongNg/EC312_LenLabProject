<!-- Chatbot Widget -->
<div id="chatbotWidget" class="fixed bottom-6 right-6 z-50">
    <!-- Chatbot Button -->
    <button id="chatbotBtn" class="w-14 h-14 bg-primary hover:bg-primary/90 rounded-full shadow-lg flex items-center justify-center transition-all duration-300 hover:scale-110">
        <span class="material-symbols-outlined text-background-dark text-2xl">smart_toy</span>
    </button>
    
    <!-- Chatbot Modal -->
    <div id="chatbotModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="w-full max-w-sm bg-[#2d2d2d] rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300" id="chatbotPanel">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <button id="closeChatbot" class="text-white hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </button>
                    <div>
                        <h3 class="text-white font-semibold">Chatbot LENLAB</h3>
                        <div class="flex items-center gap-1">
                            <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                            <span class="text-green-400 text-xs">Xin chào bạn nhé!</span>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <button id="chatbotMenuBtn" class="text-white hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">more_vert</span>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="chatbotMenu" class="absolute right-0 top-8 bg-[#3d3d3d] rounded-lg shadow-lg border border-white/10 min-w-[150px] hidden z-10">
                        <button onclick="resetChatbot()" class="w-full text-left px-4 py-3 text-white hover:bg-[#4d4d4d] rounded-lg transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">refresh</span>
                            <span class="text-sm">Làm mới</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Chat Content -->
            <div class="h-96 overflow-y-auto p-4 space-y-4" id="chatContent">
                <!-- Initial welcome message with quick actions -->
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-background-dark text-xs font-bold">LA</span>
                    </div>
                    <div class="flex-1">
                        <div class="bg-[#3d3d3d] rounded-2xl rounded-tl-md p-3 mb-3">
                            <p class="text-white text-sm">
                                Xin chào! 👋 Tôi là trợ lý ảo LENLAB.<br><br>
                                Tôi có thể giúp bạn:<br>
                                • Trả lời câu hỏi về sản phẩm và dịch vụ<br>
                                • Nhận yêu cầu sản phẩm cá nhân hóa<br>
                                • Ước tính nguyên liệu cần thiết<br><br>
                                Bạn cần hỗ trợ gì? 😊
                            </p>
                        </div>
                        
                        <!-- Quick Action Buttons -->
                        <div class="space-y-2" id="quickActions">
                            <button class="w-full bg-[#3d3d3d] hover:bg-[#4d4d4d] rounded-xl p-3 text-left transition-colors" onclick="selectTopic('questions')">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-orange-500/20 rounded-lg flex items-center justify-center">
                                        <span class="material-symbols-outlined text-orange-400 text-lg">help</span>
                                    </div>
                                    <div>
                                        <div class="text-white font-medium text-sm">Hỏi đáp thắc mắc</div>
                                        <div class="text-gray-400 text-xs">Vận chuyển, đổi trả, bảo quản...</div>
                                    </div>
                                    <span class="material-symbols-outlined text-gray-400 ml-auto">chevron_right</span>
                                </div>
                            </button>
                            
                            <button class="w-full bg-[#3d3d3d] hover:bg-[#4d4d4d] rounded-xl p-3 text-left transition-colors" onclick="selectTopic('products')">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-red-500/20 rounded-lg flex items-center justify-center">
                                        <span class="material-symbols-outlined text-red-400 text-lg">inventory_2</span>
                                    </div>
                                    <div>
                                        <div class="text-white font-medium text-sm">Sản phẩm cá nhân hóa</div>
                                        <div class="text-gray-400 text-xs">Đặt làm mẫu thiết kế riêng</div>
                                    </div>
                                    <span class="material-symbols-outlined text-gray-400 ml-auto">chevron_right</span>
                                </div>
                            </button>
                            
                            <button class="w-full bg-[#3d3d3d] hover:bg-[#4d4d4d] rounded-xl p-3 text-left transition-colors" onclick="selectTopic('estimate')">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                                        <span class="material-symbols-outlined text-yellow-400 text-lg">calculate</span>
                                    </div>
                                    <div>
                                        <div class="text-white font-medium text-sm">Ước tính số lượng len</div>
                                        <div class="text-gray-400 text-xs">Tính toán nguyên liệu cần thiết</div>
                                    </div>
                                    <span class="material-symbols-outlined text-gray-400 ml-auto">chevron_right</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Input Area -->
            <div class="p-4 border-t border-white/10">
                <div class="flex items-center gap-3">
                    <button class="text-gray-400 hover:text-white transition-colors">
                        <span class="material-symbols-outlined">attach_file</span>
                    </button>
                    <div class="flex-1 relative">
                        <input type="text" id="chatInput" placeholder="Nhập tin nhắn..." 
                               class="w-full bg-[#3d3d3d] text-white placeholder-gray-400 rounded-full px-4 py-2 pr-12 focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <button id="sendMessage" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-primary hover:bg-primary/90 rounded-full flex items-center justify-center transition-colors">
                            <span class="material-symbols-outlined text-background-dark text-lg">send</span>
                        </button>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatbotBtn = document.getElementById('chatbotBtn');
    const chatbotModal = document.getElementById('chatbotModal');
    const chatbotPanel = document.getElementById('chatbotPanel');
    const closeChatbot = document.getElementById('closeChatbot');
    const chatInput = document.getElementById('chatInput');
    const sendMessage = document.getElementById('sendMessage');
    const chatContent = document.getElementById('chatContent');
    const chatbotMenuBtn = document.getElementById('chatbotMenuBtn');
    const chatbotMenu = document.getElementById('chatbotMenu');
    
    // Menu dropdown functionality
    chatbotMenuBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        chatbotMenu.classList.toggle('hidden');
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!chatbotMenuBtn.contains(e.target) && !chatbotMenu.contains(e.target)) {
            chatbotMenu.classList.add('hidden');
        }
    });
    
    // Open chatbot
    chatbotBtn.addEventListener('click', function() {
        chatbotModal.classList.remove('hidden');
        setTimeout(() => {
            chatbotPanel.classList.remove('scale-95');
            chatbotPanel.classList.add('scale-100');
        }, 10);
        chatInput.focus();
    });
    
    // Close chatbot
    function closeChatbotModal() {
        chatbotPanel.classList.remove('scale-100');
        chatbotPanel.classList.add('scale-95');
        setTimeout(() => {
            chatbotModal.classList.add('hidden');
        }, 300);
    }
    
    closeChatbot.addEventListener('click', closeChatbotModal);
    
    // Close on backdrop click
    chatbotModal.addEventListener('click', function(e) {
        if (e.target === chatbotModal) {
            closeChatbotModal();
        }
    });
    
    // Send message
    function sendUserMessage() {
        const message = chatInput.value.trim();
        if (message) {
            // Hide quick actions when user starts typing
            hideQuickActions();
            
            addUserMessage(message);
            chatInput.value = '';
            
            // Send to real API
            fetch('/api/chatbot/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message: message,
                    session_id: getChatSessionId(),
                    user_info: getUserInfo()
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Chatbot Response:', data); // Debug log
                if (data.success) {
                    addBotMessage(data.message);
                    
                    // Handle actions if any
                    if (data.actions && data.actions.length > 0) {
                        addActionButtons(data.actions);
                    }
                    
                    // Handle uploaded images display if any
                    if (data.uploaded_images && data.uploaded_images.length > 0) {
                        addUploadedImagesDisplay(data.uploaded_images);
                    }
                } else {
                    console.error('Server error:', data.message);
                    addBotMessage('Lỗi server: ' + (data.message || 'Không xác định'));
                }
            })
            .catch(error => {
                console.error('Network Error:', error);
                addBotMessage('Lỗi kết nối: ' + error.message);
            });
        }
    }
    
    // Hide quick actions
    function hideQuickActions() {
        const quickActions = document.getElementById('quickActions');
        if (quickActions) {
            quickActions.style.display = 'none';
        }
    }
    
    sendMessage.addEventListener('click', sendUserMessage);
    
    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendUserMessage();
        }
    });
    
    // Add user message to chat
    function addUserMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex justify-end';
        messageDiv.innerHTML = `
            <div class="bg-primary text-background-dark rounded-2xl rounded-tr-md p-3 max-w-xs">
                <p class="text-sm">${message}</p>
            </div>
        `;
        chatContent.appendChild(messageDiv);
        chatContent.scrollTop = chatContent.scrollHeight;
    }
    
    // Add bot message to chat
    function addBotMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex items-start gap-3';
        messageDiv.innerHTML = `
            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-background-dark text-xs font-bold">LA</span>
            </div>
            <div class="bg-[#3d3d3d] rounded-2xl rounded-tl-md p-3 max-w-xs">
                <p class="text-white text-sm">${message.replace(/\n/g, '<br>')}</p>
            </div>
        `;
        chatContent.appendChild(messageDiv);
        chatContent.scrollTop = chatContent.scrollHeight;
    }
    
    // Add action buttons
    function addActionButtons(actions) {
        const actionsDiv = document.createElement('div');
        actionsDiv.className = 'flex items-start gap-3 mt-2';
        
        let buttonsHtml = '';
        actions.forEach(action => {
            if (action.type === 'upload_image') {
                buttonsHtml += `
                    <button onclick="handleImageUpload()" class="bg-primary hover:bg-primary/90 text-background-dark px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">photo_camera</span>
                        ${action.label}
                    </button>
                `;
            } else if (action.type === 'add_to_cart') {
                buttonsHtml += `
                    <button onclick="handleAddToCart(${action.data.estimate_id})" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">shopping_cart</span>
                        ${action.label}
                    </button>
                `;
            } else if (action.type === 'redirect') {
                buttonsHtml += `
                    <a href="${action.url}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">login</span>
                        ${action.label}
                    </a>
                `;
            } else if (action.type === 'payment') {
                buttonsHtml += `
                    <button onclick="handlePayment(${action.data.request_id}, ${action.data.amount})" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">payment</span>
                        ${action.label}
                    </button>
                `;
            }
        });
        
        if (buttonsHtml) {
            actionsDiv.innerHTML = `
                <div class="w-8 h-8"></div>
                <div class="flex gap-2 flex-wrap">
                    ${buttonsHtml}
                </div>
            `;
            
            chatContent.appendChild(actionsDiv);
            chatContent.scrollTop = chatContent.scrollHeight;
        }
    }
    
    // Get or create chat session ID
    function getChatSessionId() {
        let sessionId = localStorage.getItem('chatbot_session_id');
        if (!sessionId) {
            sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('chatbot_session_id', sessionId);
        }
        return sessionId;
    }
    
    // Get user info (check if logged in)
    function getUserInfo() {
        // Check if user is logged in by looking for auth indicators
        const userMeta = document.querySelector('meta[name="user-id"]');
        const userName = document.querySelector('meta[name="user-name"]');
        const userEmail = document.querySelector('meta[name="user-email"]');
        
        return {
            is_logged_in: !!userMeta,
            user_id: userMeta ? userMeta.getAttribute('content') : null,
            name: userName ? userName.getAttribute('content') : null,
            email: userEmail ? userEmail.getAttribute('content') : null
        };
    }
    
    // Handle image upload
    function handleImageUpload() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/jpeg,image/png,image/jpg,image/gif';
        input.multiple = true;
        
        input.onchange = function(e) {
            const files = Array.from(e.target.files);
            
            // Validate files
            const maxFiles = 3;
            const maxSize = 5 * 1024 * 1024; // 5MB
            
            if (files.length > maxFiles) {
                addBotMessage(`❌ Chỉ được upload tối đa ${maxFiles} ảnh cùng lúc.`);
                return;
            }
            
            const validFiles = [];
            const errors = [];
            
            files.forEach((file, index) => {
                if (file.size > maxSize) {
                    errors.push(`Ảnh "${file.name}" quá lớn (tối đa 5MB)`);
                } else if (!file.type.startsWith('image/')) {
                    errors.push(`"${file.name}" không phải là file ảnh`);
                } else {
                    validFiles.push(file);
                }
            });
            
            if (errors.length > 0) {
                addBotMessage('❌ **Lỗi upload:**\n' + errors.join('\n'));
            }
            
            // Upload valid files
            validFiles.forEach(file => {
                uploadImage(file);
            });
        };
        
        input.click();
    }
    
    // Upload image to server
    function uploadImage(file) {
        // Show uploading message
        addUserMessage(`📤 Đang upload ảnh: ${file.name}...`);
        
        const formData = new FormData();
        formData.append('image', file);
        formData.append('session_id', getChatSessionId());
        
        fetch('/api/chatbot/upload-image', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message with image preview
                addImagePreviewMessage(data.image_url, data.file_name || file.name, data.message);
                
                // Also add bot confirmation message
                addBotMessage(`✅ ${data.message}\n\n💡 Bạn có thể tiếp tục upload thêm ảnh hoặc gõ "tiếp tục" để hoàn thành.`);
            } else {
                addBotMessage('❌ Lỗi upload ảnh: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            addBotMessage('❌ Lỗi upload ảnh. Vui lòng thử lại.');
        });
    }
    
    // Add uploaded images display
    function addUploadedImagesDisplay(uploadedImages) {
        if (uploadedImages && uploadedImages.length > 0) {
            const imagesDiv = document.createElement('div');
            imagesDiv.className = 'flex items-start gap-3 mt-2';
            
            let imagesHtml = '';
            uploadedImages.forEach((image, index) => {
                imagesHtml += `
                    <div class="bg-[#3d3d3d] rounded-lg p-2 max-w-48">
                        <img src="${image.url}" alt="Ảnh tham khảo ${index + 1}" class="w-full h-auto rounded border border-white/10" style="max-height: 150px; object-fit: cover;">
                        <p class="text-white text-xs mt-1 text-center">Ảnh ${index + 1}</p>
                    </div>
                `;
            });
            
            imagesDiv.innerHTML = `
                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-background-dark text-xs font-bold">LA</span>
                </div>
                <div class="flex gap-2 flex-wrap">
                    ${imagesHtml}
                </div>
            `;
            
            chatContent.appendChild(imagesDiv);
            chatContent.scrollTop = chatContent.scrollHeight;
        }
    }
    
    // Add image preview message
    function addImagePreviewMessage(imageUrl, fileName, message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex justify-end mb-2';
        messageDiv.innerHTML = `
            <div class="bg-primary text-background-dark rounded-2xl rounded-tr-md p-3 max-w-xs">
                <div class="mb-2">
                    <img src="${imageUrl}" alt="${fileName}" class="w-full max-w-48 h-auto rounded-lg border border-background-dark/20" style="max-height: 200px; object-fit: cover;">
                </div>
                <p class="text-sm font-medium">📸 ${fileName}</p>
                <p class="text-xs opacity-80 mt-1">Ảnh tham khảo đã upload</p>
            </div>
        `;
        chatContent.appendChild(messageDiv);
        chatContent.scrollTop = chatContent.scrollHeight;
    }
    
    // Handle add to cart
    function handleAddToCart(estimateId) {
        fetch('/api/chatbot/add-to-cart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                estimate_id: estimateId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addBotMessage('✅ ' + data.message);
            } else {
                addBotMessage('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            addBotMessage('Có lỗi xảy ra khi thêm vào giỏ hàng.');
        });
    }
    
    // Handle payment
    function handlePayment(requestId, amount) {
        // For now, just show a message that payment form will be implemented
        addBotMessage(`💳 **Chuyển đến trang thanh toán**\n\n🆔 Mã yêu cầu: #${requestId}\n💰 Số tiền: ${new Intl.NumberFormat('vi-VN').format(amount)}đ\n\n🔄 Tính năng thanh toán sẽ được triển khai trong phiên bản tiếp theo.`);
    }
    
    // Make functions globally accessible for onclick handlers
    window.handleImageUpload = handleImageUpload;
    window.handleAddToCart = handleAddToCart;
    window.handlePayment = handlePayment;
    window.getChatSessionId = getChatSessionId;
    window.addUserMessage = addUserMessage;
    window.addBotMessage = addBotMessage;
    window.addImagePreviewMessage = addImagePreviewMessage;
    window.uploadImage = uploadImage;
});

// Topic selection functions
function selectTopic(topic) {
    let message = '';
    switch(topic) {
        case 'questions':
            message = 'FAQ - Hỏi đáp thắc mắc';
            break;
        case 'products':
            message = 'CUSTOM - Sản phẩm cá nhân hóa';
            break;
        case 'estimate':
            message = 'ESTIMATE - Ước tính nguyên liệu';
            break;
    }
    
    // Hide quick actions
    const quickActions = document.getElementById('quickActions');
    if (quickActions) {
        quickActions.style.display = 'none';
    }
    
    // Send the topic selection as a user message
    const chatInput = document.getElementById('chatInput');
    chatInput.value = message;
    document.getElementById('sendMessage').click();
}

// Reset chatbot function
function resetChatbot() {
    // Close menu first
    const chatbotMenu = document.getElementById('chatbotMenu');
    chatbotMenu.classList.add('hidden');
    
    // Clear chat content and restore initial state
    const chatContent = document.getElementById('chatContent');
    chatContent.innerHTML = `
        <!-- Initial welcome message with quick actions -->
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-background-dark text-xs font-bold">LA</span>
            </div>
            <div class="flex-1">
                <div class="bg-[#3d3d3d] rounded-2xl rounded-tl-md p-3 mb-3">
                    <p class="text-white text-sm">
                        Xin chào! 👋 Tôi là trợ lý ảo LENLAB.<br><br>
                        Tôi có thể giúp bạn:<br>
                        • Trả lời câu hỏi về sản phẩm và dịch vụ<br>
                        • Nhận yêu cầu sản phẩm cá nhân hóa<br>
                        • Ước tính nguyên liệu cần thiết<br><br>
                        Bạn cần hỗ trợ gì? 😊
                    </p>
                </div>
                
                <!-- Quick Action Buttons -->
                <div class="space-y-2" id="quickActions">
                    <button class="w-full bg-[#3d3d3d] hover:bg-[#4d4d4d] rounded-xl p-3 text-left transition-colors" onclick="selectTopic('questions')">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-orange-500/20 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-orange-400 text-lg">help</span>
                            </div>
                            <div>
                                <div class="text-white font-medium text-sm">Hỏi đáp thắc mắc</div>
                                <div class="text-gray-400 text-xs">Vận chuyển, đổi trả, bảo quản...</div>
                            </div>
                            <span class="material-symbols-outlined text-gray-400 ml-auto">chevron_right</span>
                        </div>
                    </button>
                    
                    <button class="w-full bg-[#3d3d3d] hover:bg-[#4d4d4d] rounded-xl p-3 text-left transition-colors" onclick="selectTopic('products')">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-red-500/20 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-red-400 text-lg">inventory_2</span>
                            </div>
                            <div>
                                <div class="text-white font-medium text-sm">Sản phẩm cá nhân hóa</div>
                                <div class="text-gray-400 text-xs">Đặt làm mẫu thiết kế riêng</div>
                            </div>
                            <span class="material-symbols-outlined text-gray-400 ml-auto">chevron_right</span>
                        </div>
                    </button>
                    
                    <button class="w-full bg-[#3d3d3d] hover:bg-[#4d4d4d] rounded-xl p-3 text-left transition-colors" onclick="selectTopic('estimate')">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-yellow-400 text-lg">calculate</span>
                            </div>
                            <div>
                                <div class="text-white font-medium text-sm">Ước tính số lượng len</div>
                                <div class="text-gray-400 text-xs">Tính toán nguyên liệu cần thiết</div>
                            </div>
                            <span class="material-symbols-outlined text-gray-400 ml-auto">chevron_right</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Clear input
    const chatInput = document.getElementById('chatInput');
    chatInput.value = '';
    
    // Reset session by calling API
    fetch('/api/chatbot/reset', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            session_id: window.getChatSessionId()
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Chatbot reset:', data);
        // Generate new session ID
        localStorage.removeItem('chatbot_session_id');
    })
    .catch(error => {
        console.error('Reset error:', error);
    });
}
</script>