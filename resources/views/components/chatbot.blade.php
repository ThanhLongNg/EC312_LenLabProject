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
                            <span class="text-green-400 text-xs">Đã có mặt trực tuyến</span>
                        </div>
                    </div>
                </div>
                <button class="text-white hover:text-primary transition-colors">
                    <span class="material-symbols-outlined">more_vert</span>
                </button>
            </div>
            
            <!-- Chat Content -->
            <div class="h-96 overflow-y-auto p-4 space-y-4" id="chatContent">
                <!-- Date Header -->
                <div class="text-center">
                    <span class="text-gray-400 text-xs bg-black/20 px-3 py-1 rounded-full">Hôm nay, 10:23 AM</span>
                </div>
                
                <!-- Bot Message -->
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-background-dark text-xs font-bold">LA</span>
                    </div>
                    <div class="flex-1">
                        <div class="bg-[#3d3d3d] rounded-2xl rounded-tl-md p-3 mb-3">
                            <p class="text-white text-sm">
                                Xin chào! 👋 Mình là trợ lý ảo LENLAB.<br>
                                Để hỗ trợ tốt nhất, vui lòng chọn một trong các chủ đề dưới đây để bắt đầu:
                            </p>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="space-y-2">
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
                <div class="text-center mt-2">
                    <span class="text-gray-500 text-xs">Được hỗ trợ bởi LENLAB AI</span>
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
            addUserMessage(message);
            chatInput.value = '';
            
            // Simulate bot response
            setTimeout(() => {
                addBotMessage("Cảm ơn bạn đã liên hệ! Tôi sẽ hỗ trợ bạn ngay.");
            }, 1000);
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
                <p class="text-white text-sm">${message}</p>
            </div>
        `;
        chatContent.appendChild(messageDiv);
        chatContent.scrollTop = chatContent.scrollHeight;
    }
});

// Topic selection functions
function selectTopic(topic) {
    let response = '';
    switch(topic) {
        case 'questions':
            response = 'Bạn có thắc mắc gì về sản phẩm, vận chuyển hay chính sách đổi trả? Tôi sẵn sàng hỗ trợ!';
            break;
        case 'products':
            response = 'Bạn muốn đặt làm sản phẩm theo thiết kế riêng? Hãy mô tả ý tưởng của bạn, tôi sẽ tư vấn chi tiết!';
            break;
        case 'estimate':
            response = 'Để ước tính số lượng len cần thiết, bạn vui lòng cho biết loại sản phẩm và kích thước mong muốn nhé!';
            break;
    }
    
    // Add bot response
    setTimeout(() => {
        const chatContent = document.getElementById('chatContent');
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex items-start gap-3';
        messageDiv.innerHTML = `
            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-background-dark text-xs font-bold">LA</span>
            </div>
            <div class="bg-[#3d3d3d] rounded-2xl rounded-tl-md p-3">
                <p class="text-white text-sm">${response}</p>
            </div>
        `;
        chatContent.appendChild(messageDiv);
        chatContent.scrollTop = chatContent.scrollHeight;
    }, 500);
}
</script>