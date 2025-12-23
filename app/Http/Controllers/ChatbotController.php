<?php

namespace App\Http\Controllers;

use App\Models\ChatLog;
use App\Models\CustomProductRequest;
use App\Models\MaterialEstimate;
use App\Models\FaqItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    /**
     * Lấy FAQ responses từ database thay vì hardcode
     */
    private function getFaqResponses(): array
    {
        $faqs = FaqItem::active()->byPriority()->get();
        $responses = [];
        
        foreach ($faqs as $faq) {
            foreach ($faq->keywords as $keyword) {
                $responses[strtolower($keyword)] = $faq->answer;
            }
        }
        
        return $responses;
    }

    /**
     * Lấy danh sách FAQ theo category để hiển thị
     */
    private function getFaqsByCategory(): array
    {
        $faqs = FaqItem::active()
            ->byPriority()
            ->get()
            ->groupBy('category');
            
        $categoryNames = FaqItem::getCategories();
        $result = [];
        
        foreach ($faqs as $category => $items) {
            $result[$category] = [
                'name' => $categoryNames[$category] ?? $category,
                'items' => $items->take(3) // Lấy 3 FAQ phổ biến nhất mỗi category
            ];
        }
        
        return $result;
    }

    public function testIntent(Request $request): JsonResponse
    {
        $message = $request->message ?? 'Tôi có thắc mắc về sản phẩm và dịch vụ';
        $sessionId = $request->session_id ?? 'test_session';
        
        $intent = $this->classifyIntent($message, $sessionId);
        
        return response()->json([
            'message' => $message,
            'intent' => $intent,
            'lowercase' => strtolower($message),
            'exact_match' => strtolower($message) === 'tôi có thắc mắc về sản phẩm và dịch vụ'
        ]);
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'session_id' => 'nullable|string',
            'user_info' => 'nullable|array'
        ]);

        $sessionId = $request->session_id ?: Str::uuid()->toString();
        $userMessage = trim($request->message);
        $userId = auth()->id();
        $userInfo = $request->user_info ?? [];

        // Store additional user info in session for anonymous users
        if (!$userId && !empty($userInfo)) {
            // You could store this in a separate table or session storage
            // For now, we'll include it in the chat log context
        }

        // Phân loại intent
        $intent = $this->classifyIntent($userMessage, $sessionId);
        
        // Reset context nếu là quick action mới
        if (str_contains(strtolower($userMessage), 'faq') || 
            str_contains(strtolower($userMessage), 'custom') || 
            str_contains(strtolower($userMessage), 'estimate')) {
            // Xóa context cũ để bắt đầu conversation mới
            $this->resetConversationContext($sessionId);
        }
        
        // Xử lý theo intent với override cho quick actions
        if (str_contains(strtolower($userMessage), 'faq') || str_contains(strtolower($userMessage), 'hỏi đáp thắc mắc')) {
            $botReply = $this->handleFAQ($userMessage);
        } elseif (str_contains(strtolower($userMessage), 'custom') || str_contains(strtolower($userMessage), 'cá nhân hóa')) {
            $botReply = $this->handleCustomRequest($userMessage, $sessionId, $userId);
        } elseif (str_contains(strtolower($userMessage), 'estimate') || str_contains(strtolower($userMessage), 'ước tính nguyên liệu')) {
            $botReply = $this->handleMaterialEstimate($userMessage, $sessionId, $userId);
        } else {
            $botReply = match($intent) {
                'FAQ' => $this->handleFAQ($userMessage),
                'CUSTOM_REQUEST' => $this->handleCustomRequest($userMessage, $sessionId, $userId),
                'MATERIAL_ESTIMATE' => $this->handleMaterialEstimate($userMessage, $sessionId, $userId),
                default => $this->handleUnknown($userMessage)
            };
        }

        // Lưu log chat
        ChatLog::create([
            'session_id' => $sessionId,
            'user_id' => $userId,
            'user_message' => $userMessage,
            'bot_reply' => $botReply['message'],
            'intent' => $intent,
            'context' => $botReply['context'] ?? null
        ]);

        return response()->json([
            'success' => true,
            'message' => $botReply['message'],
            'session_id' => $sessionId,
            'intent' => $intent,
            'context' => $botReply['context'] ?? null,
            'actions' => $botReply['actions'] ?? []
        ]);
    }

    private function classifyIntent(string $message, string $sessionId): string
    {
        $originalMessage = $message;
        $message = strtolower($message);
        
        // Kiểm tra tin nhắn từ quick actions TRƯỚC (ưu tiên cao nhất)
        if (str_contains($message, 'faq') && str_contains($message, 'hỏi đáp thắc mắc')) {
            return 'FAQ';
        }
        
        if (str_contains($message, 'custom') && str_contains($message, 'cá nhân hóa')) {
            return 'CUSTOM_REQUEST';
        }
        
        if (str_contains($message, 'estimate') && str_contains($message, 'ước tính nguyên liệu')) {
            return 'MATERIAL_ESTIMATE';
        }

        // Kiểm tra context từ conversation trước (chỉ khi không phải quick action)
        $lastChat = ChatLog::where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->first();
            
        if ($lastChat && $lastChat->context) {
            $context = $lastChat->context;
            if (isset($context['waiting_for']) && $context['waiting_for']) {
                return $context['current_intent'] ?? 'UNKNOWN';
            }
        }

        // Keywords cho Custom Request (cụ thể hơn)
        $customKeywords = ['làm riêng', 'đặt làm', 'thiết kế riêng', 'cá nhân hóa', 'custom', 'đặt hàng riêng', 'làm theo yêu cầu'];
        foreach ($customKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return 'CUSTOM_REQUEST';
            }
        }

        // Keywords cho Material Estimate (cụ thể hơn)
        $materialKeywords = ['ước tính len', 'cần bao nhiêu len', 'tính len', 'nguyên liệu cần thiết', 'estimate', 'tính toán nguyên liệu'];
        foreach ($materialKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return 'MATERIAL_ESTIMATE';
            }
        }

        // Keywords cho FAQ (kiểm tra cuối cùng)
        $faqResponses = $this->getFaqResponses();
        $faqKeywords = array_keys($faqResponses);
        foreach ($faqKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return 'FAQ';
            }
        }

        return 'FAQ'; // Mặc định là FAQ thay vì UNKNOWN
    }

    private function resetConversationContext(string $sessionId): void
    {
        // Cập nhật context của chat log cuối cùng để reset conversation
        ChatLog::where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->first()
            ?->update(['context' => null]);
    }

    private function handleFAQ(string $message): array
    {
        $message = strtolower($message);
        
        // Nếu user chọn từ quick action FAQ
        if (str_contains($message, 'faq') || str_contains($message, 'hỏi đáp thắc mắc')) {
            // Lấy danh sách FAQ từ database để hiển thị
            $faqs = FaqItem::active()
                ->byPriority()
                ->limit(12) // Giới hạn 12 câu hỏi phổ biến nhất
                ->get();
            
            $faqList = "Bạn có thắc mắc gì về:\n\n";
            
            // Tạo danh sách FAQ với emoji theo category
            $categoryEmojis = [
                'giao_hang' => '🚚',
                'doi_tra' => '🔄', 
                'san_pham' => '🧶',
                'thanh_toan' => '💳',
                'ho_tro' => '�',
                'general' => '📋'
            ];
            
            foreach ($faqs as $faq) {
                $emoji = $categoryEmojis[$faq->category] ?? '❓';
                $faqList .= "{$emoji} **{$faq->question}**\n";
            }
            
            $faqList .= "\nHãy gõ từ khóa hoặc câu hỏi cụ thể mà bạn muốn biết! 😊";
            
            return [
                'message' => $faqList,
                'context' => [
                    'current_intent' => 'FAQ',
                    'step' => 'waiting_question',
                    'waiting_for' => 'faq_question'
                ]
            ];
        }
        
        // Tìm kiếm FAQ từ database
        $faq = FaqItem::searchByKeywords($message);
        if ($faq) {
            // Tăng số lần sử dụng
            $faq->incrementUsage();
            
            return [
                'message' => $faq->answer . "\n\nBạn có câu hỏi nào khác không? 😊"
            ];
        }

        // Fallback với gợi ý từ database
        $popularFaqs = FaqItem::active()
            ->orderBy('usage_count', 'desc')
            ->limit(5)
            ->get();
            
        $suggestions = "Xin lỗi, tôi chưa hiểu câu hỏi: \"$message\" 😅\n\n";
        $suggestions .= "Bạn có thể hỏi về:\n";
        
        foreach ($popularFaqs as $faq) {
            $suggestions .= "• **{$faq->question}**\n";
        }
        
        $suggestions .= "\nHoặc liên hệ hotline **1900-xxxx** để được hỗ trợ trực tiếp! 📞";

        return [
            'message' => $suggestions
        ];
    }

    private function handleCustomRequest(string $message, string $sessionId, ?int $userId): array
    {
        // Nếu user chọn từ quick action "sản phẩm cá nhân hóa"
        if (str_contains(strtolower($message), 'custom') && str_contains(strtolower($message), 'cá nhân hóa')) {
            return [
                'message' => "🎨 **Tạo sản phẩm cá nhân hóa**\n\n" .
                           "Tuyệt vời! Tôi sẽ giúp bạn tạo sản phẩm riêng theo ý muốn.\n\n" .
                           "Bạn muốn làm loại sản phẩm gì?\n\n" .
                           "1️⃣ **Móc khóa len** - Nhỏ gọn, dễ thương\n" .
                           "2️⃣ **Thú bông** - Đáng yêu, ôm được\n" .
                           "3️⃣ **Túi xách** - Thời trang, tiện dụng\n" .
                           "4️⃣ **Áo len** - Ấm áp, phong cách\n" .
                           "5️⃣ **Khăn len** - Sang trọng, ấm cổ\n" .
                           "6️⃣ **Khác** - Mô tả sản phẩm bạn muốn\n\n" .
                           "Chỉ cần gõ số hoặc tên sản phẩm nhé! 😊",
                'context' => [
                    'current_intent' => 'CUSTOM_REQUEST',
                    'step' => 'product_type',
                    'waiting_for' => 'product_type'
                ]
            ];
        }
        
        $lastChat = ChatLog::where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->first();

        $context = $lastChat->context ?? [];
        $step = $context['step'] ?? 'start';

        // Kiểm tra xem có request đang active không
        $existingRequest = CustomProductRequest::where('session_id', $sessionId)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->first();

        // Nếu có request đang active, tiếp tục với request đó
        if ($existingRequest && $step !== 'start') {
            return $this->continueExistingRequest($existingRequest, $message, $context);
        }

        switch ($step) {
            case 'start':
                // BƯỚC 1: KHỞI TẠO YÊU CẦU
                return [
                    'message' => '🎨 **Tạo sản phẩm cá nhân hóa**\n\nTôi sẽ giúp bạn tạo yêu cầu sản phẩm riêng theo ý muốn!\n\nBạn muốn làm loại sản phẩm gì?\n\n1️⃣ Móc khóa len\n2️⃣ Thú bông\n3️⃣ Túi xách\n4️⃣ Áo len\n5️⃣ Khác (vui lòng mô tả)',
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'product_type',
                        'waiting_for' => 'product_type'
                    ]
                ];

            case 'product_type':
                // BƯỚC 2: THU THẬP THÔNG TIN CƠ BẢN - Loại sản phẩm
                $productType = $this->parseProductType($message);
                return [
                    'message' => "✅ Loại sản phẩm: **{$productType}**\n\nBạn muốn kích thước như thế nào?\n\n📏 **Ví dụ:**\n• Nhỏ (10-15cm)\n• Vừa (20-25cm)\n• Lớn (30-35cm)\n• Hoặc kích thước cụ thể: 20cm x 15cm",
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'size',
                        'waiting_for' => 'size',
                        'product_type' => $productType
                    ]
                ];

            case 'size':
                // BƯỚC 3: THU THẬP THÔNG TIN - Kích thước
                return [
                    'message' => "✅ Kích thước: **{$message}**\n\n�  **Màu sắc mong muốn:**\n\nBạn muốn sản phẩm có màu gì?\n\n💡 **Ví dụ:**\n• Đỏ tươi\n• Xanh navy\n• Hồng pastel\n• Nhiều màu (vui lòng mô tả)\n• Theo ảnh tham khảo",
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'color',
                        'waiting_for' => 'color',
                        'product_type' => $context['product_type'],
                        'size' => $message
                    ]
                ];

            case 'color':
                // BƯỚC 4: THU THẬP THÔNG TIN - Màu sắc
                return [
                    'message' => "✅ Màu sắc: **{$message}**\n\n🎭 **Phong cách thiết kế:**\n\nBạn muốn sản phẩm có phong cách như thế nào?\n\n🎨 **Lựa chọn:**\n• Đơn giản, tối giản\n• Dễ thương, kawaii\n• Sang trọng, lịch lãm\n• Vintage, cổ điển\n• Hiện đại, trendy\n• Khác (vui lòng mô tả)",
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'style',
                        'waiting_for' => 'style',
                        'product_type' => $context['product_type'],
                        'size' => $context['size'],
                        'color' => $message
                    ]
                ];

            case 'style':
                // BƯỚC 5: THU THẬP THÔNG TIN - Phong cách
                return [
                    'message' => "✅ Phong cách: **{$message}**\n\n🎯 **Mục đích sử dụng:**\n\nBạn sẽ dùng sản phẩm này để làm gì?\n\n📝 **Ví dụ:**\n• Quà tặng sinh nhật\n• Đồ trang trí phòng\n• Sử dụng hàng ngày\n• Quà lưu niệm\n• Bán hàng\n• Khác (vui lòng mô tả)",
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'purpose',
                        'waiting_for' => 'purpose',
                        'product_type' => $context['product_type'],
                        'size' => $context['size'],
                        'color' => $context['color'],
                        'style' => $message
                    ]
                ];

            case 'purpose':
                // BƯỚC 6: THU THẬP THÔNG TIN - Mục đích sử dụng
                return [
                    'message' => "✅ Mục đích: **{$message}**\n\n✨ **Chi tiết đặc biệt:**\n\nBạn có muốn thêm chi tiết đặc biệt nào không?\n\n🎁 **Ví dụ:**\n• Thêm tên/chữ thêu\n• Logo/biểu tượng riêng\n• Phụ kiện đi kèm\n• Đóng gói đặc biệt\n• Không cần thêm gì\n\n*Hãy mô tả chi tiết nhé!*",
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'special_details',
                        'waiting_for' => 'special_details',
                        'product_type' => $context['product_type'],
                        'size' => $context['size'],
                        'color' => $context['color'],
                        'style' => $context['style'],
                        'purpose' => $message
                    ]
                ];

            case 'special_details':
                // BƯỚC 7: UPLOAD ẢNH MINH HỌA
                return [
                    'message' => "✅ Chi tiết đặc biệt: **{$message}**\n\n📸 **Upload ảnh tham khảo (tùy chọn):**\n\n🖼️ **Bạn có thể gửi:**\n• Ảnh sản phẩm mẫu\n• Ảnh phong cách mong muốn\n• Sketch hoặc ý tưởng\n• Ảnh màu sắc tham khảo\n\n👆 Nhấn nút **\"📸 Upload ảnh\"** để tải ảnh lên hoặc gõ **\"bỏ qua\"** nếu không có ảnh.\nSau khi upload xong, gõ **\"tiếp tục\"** để hoàn thành.",
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'upload_images',
                        'waiting_for' => 'images',
                        'product_type' => $context['product_type'],
                        'size' => $context['size'],
                        'color' => $context['color'],
                        'style' => $context['style'],
                        'purpose' => $context['purpose'],
                        'special_details' => $message,
                        'uploaded_images' => []
                    ],
                    'actions' => [
                        [
                            'type' => 'upload_image',
                            'label' => '📸 Upload ảnh tham khảo',
                            'multiple' => true,
                            'max_files' => 3
                        ]
                    ]
                ];

            case 'upload_images':
                if (strtolower(trim($message)) === 'tiếp tục' || strtolower(trim($message)) === 'bỏ qua') {
                    // Check if user is logged in
                    if (!$userId) {
                        // BƯỚC 8A: THU THẬP THÔNG TIN LIÊN HỆ - Họ tên
                        return [
                            'message' => "📞 **Thông tin liên hệ**\n\nĐể admin có thể liên hệ và báo giá, vui lòng cung cấp thông tin của bạn.\n\n👤 **Bước 1/3: Họ và tên**\n\nVui lòng cho biết họ tên đầy đủ của bạn:",
                            'context' => [
                                'current_intent' => 'CUSTOM_REQUEST',
                                'step' => 'contact_name',
                                'waiting_for' => 'contact_name',
                                'product_type' => $context['product_type'],
                                'size' => $context['size'],
                                'color' => $context['color'],
                                'style' => $context['style'],
                                'purpose' => $context['purpose'],
                                'special_details' => $context['special_details'],
                                'uploaded_images' => $context['uploaded_images'] ?? []
                            ]
                        ];
                    } else {
                        // User đã đăng nhập, tạo request luôn
                        return $this->createCustomRequest($context, $sessionId, $userId, null);
                    }
                } else {
                    return [
                        'message' => '📸 Vui lòng upload ảnh tham khảo bằng nút **"📸 Upload ảnh"** hoặc:\n• Gõ **"tiếp tục"** nếu đã upload xong\n• Gõ **"bỏ qua"** nếu không có ảnh\n\n💡 *Tip: Ảnh tham khảo giúp admin hiểu rõ yêu cầu của bạn hơn!*',
                        'context' => $context,
                        'actions' => [
                            [
                                'type' => 'upload_image',
                                'label' => '📸 Upload thêm ảnh',
                                'multiple' => true,
                                'max_files' => 3
                            ]
                        ]
                    ];
                }

            case 'contact_name':
                // BƯỚC 8B: THU THẬP SỐ ĐIỆN THOẠI
                if (strlen(trim($message)) < 2) {
                    return [
                        'message' => "❌ **Họ tên quá ngắn**\n\nVui lòng nhập họ tên đầy đủ của bạn:",
                        'context' => $context
                    ];
                }
                
                return [
                    'message' => "✅ Họ tên: **{$message}**\n\n📱 **Bước 2/3: Số điện thoại**\n\nVui lòng nhập số điện thoại để admin có thể liên hệ:\n\n💡 *Ví dụ: 0901234567*",
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'contact_phone',
                        'waiting_for' => 'contact_phone',
                        'product_type' => $context['product_type'],
                        'size' => $context['size'],
                        'color' => $context['color'],
                        'style' => $context['style'],
                        'purpose' => $context['purpose'],
                        'special_details' => $context['special_details'],
                        'uploaded_images' => $context['uploaded_images'] ?? [],
                        'contact_name' => $message
                    ]
                ];

            case 'contact_phone':
                // BƯỚC 8C: THU THẬP EMAIL
                $phone = trim($message);
                if (!preg_match('/^[0-9+\-\s()]{8,15}$/', $phone)) {
                    return [
                        'message' => "❌ **Số điện thoại không hợp lệ**\n\nVui lòng nhập số điện thoại hợp lệ:\n\n💡 *Ví dụ: 0901234567 hoặc +84901234567*",
                        'context' => $context
                    ];
                }
                
                return [
                    'message' => "✅ Số điện thoại: **{$phone}**\n\n📧 **Bước 3/3: Email**\n\nVui lòng nhập địa chỉ email của bạn:\n\n💡 *Ví dụ: example@gmail.com*",
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'contact_email',
                        'waiting_for' => 'contact_email',
                        'product_type' => $context['product_type'],
                        'size' => $context['size'],
                        'color' => $context['color'],
                        'style' => $context['style'],
                        'purpose' => $context['purpose'],
                        'special_details' => $context['special_details'],
                        'uploaded_images' => $context['uploaded_images'] ?? [],
                        'contact_name' => $context['contact_name'],
                        'contact_phone' => $phone
                    ]
                ];

            case 'contact_email':
                // BƯỚC 8D: XỬ LÝ THÔNG TIN LIÊN HỆ HOÀN CHỈNH
                $email = trim($message);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return [
                        'message' => "❌ **Email không hợp lệ**\n\nVui lòng nhập địa chỉ email hợp lệ:\n\n💡 *Ví dụ: example@gmail.com*",
                        'context' => $context
                    ];
                }
                
                $contactInfo = [
                    'name' => $context['contact_name'],
                    'phone' => $context['contact_phone'],
                    'email' => $email
                ];
                
                // Tạo request với thông tin liên hệ đầy đủ
                return $this->createCustomRequest($context, $sessionId, $userId, $contactInfo);

            default:
                return [
                    'message' => '❌ Có vẻ như có lỗi xảy ra trong quá trình xử lý.\n\n🔄 Bạn có thể bắt đầu lại bằng cách nói:\n• "làm riêng"\n• "đặt hàng cá nhân hóa"\n• "custom sản phẩm"\n\n😊 Tôi sẵn sàng hỗ trợ bạn!'
                ];
        }
    }

    private function continueExistingRequest(CustomProductRequest $request, string $message, array $context): array
    {
        // BƯỚC 5: TRAO ĐỔI 2 CHIỀU & BƯỚC 6: FALLBACK TIMEOUT
        if ($request->status === 'pending_admin_response') {
            // Kiểm tra timeout (24h chưa có phản hồi)
            if ($request->isAwaitingAdminResponse()) {
                return [
                    'message' => "⏰ **Thông báo timeout**\n\nAdmin chưa phản hồi yêu cầu #{$request->id} sau 24 giờ.\n\n📞 **Liên hệ trực tiếp để được hỗ trợ nhanh hơn:**\n• 📱 Hotline: **1900-xxxx**\n• 📧 Email: **support@lenlab.vn**\n• 💬 Facebook: **fb.com/lenlab**\n\n✅ Yêu cầu của bạn vẫn được lưu và sẽ được xử lý sớm nhất!",
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'timeout_fallback',
                        'request_id' => $request->id
                    ]
                ];
            }

            // Lưu tin nhắn bổ sung từ khách hàng
            $this->saveAdditionalMessage($request, $message, 'customer');

            return [
                'message' => "📝 **Tin nhắn đã được ghi nhận!**\n\n💬 Nội dung: \"{$message}\"\n\n⏳ **Trạng thái:** {$request->status_text}\n🆔 **Mã yêu cầu:** #{$request->id}\n\n🔔 Admin sẽ xem và phản hồi sớm nhất có thể.\nBạn có thể tiếp tục gửi thêm thông tin hoặc ảnh nếu cần!",
                'context' => [
                    'current_intent' => 'CUSTOM_REQUEST',
                    'step' => 'waiting_admin',
                    'request_id' => $request->id,
                    'waiting_for' => 'admin_response'
                ]
            ];
        }

        // Kiểm tra phản hồi từ admin
        if ($request->status === 'admin_responded' && $request->admin_response) {
            return [
                'message' => "📢 **Admin đã phản hồi yêu cầu #{$request->id}:**\n\n💬 {$request->admin_response}\n\n🔄 Bạn có câu hỏi gì thêm không? Tôi sẽ chuyển tiếp cho admin!",
                'context' => [
                    'current_intent' => 'CUSTOM_REQUEST',
                    'step' => 'admin_conversation',
                    'request_id' => $request->id
                ]
            ];
        }

        // BƯỚC 7: CHỐT YÊU CẦU & ĐẶT CỌC
        if ($request->status === 'confirmed' && $request->estimated_price) {
            $depositAmount = $request->calculateDepositAmount();
            
            return [
                'message' => "🎉 **Yêu cầu đã được xác nhận!**\n\n💰 **Thông tin thanh toán:**\n• 💵 Giá sản phẩm: **" . number_format($request->estimated_price) . "đ**\n• 🏦 Tiền đặt cọc ({$request->deposit_percentage}%): **" . number_format($depositAmount) . "đ**\n• 💳 Còn lại: **" . number_format($request->calculateRemainingAmount()) . "đ**\n\n📅 **Thời gian hoàn thành:** 7-14 ngày làm việc\n\n🚀 Bạn có muốn đặt cọc ngay để bắt đầu sản xuất không?",
                'context' => [
                    'current_intent' => 'CUSTOM_REQUEST',
                    'step' => 'deposit_payment',
                    'request_id' => $request->id
                ],
                'actions' => [
                    [
                        'type' => 'deposit_payment',
                        'label' => '💳 Đặt cọc ngay',
                        'data' => [
                            'request_id' => $request->id,
                            'amount' => $depositAmount
                        ]
                    ]
                ]
            ];
        }

        // BƯỚC 8: THANH TOÁN & GIAO HÀNG
        if ($request->status === 'deposit_paid') {
            return [
                'message' => "✅ **Đã nhận tiền đặt cọc!**\n\n🏭 **Trạng thái:** Đang chuẩn bị sản xuất\n📅 **Dự kiến hoàn thành:** 7-14 ngày\n\n📍 **Cần địa chỉ giao hàng:**\nVui lòng cung cấp thông tin giao hàng:\n• Họ tên người nhận\n• Số điện thoại\n• Địa chỉ chi tiết\n\n💡 *Ví dụ: Nguyễn Văn A - 0901234567 - 123 Đường ABC, Phường XYZ, Quận 1, TP.HCM*",
                'context' => [
                    'current_intent' => 'CUSTOM_REQUEST',
                    'step' => 'collect_shipping_address',
                    'request_id' => $request->id
                ]
            ];
        }

        if ($request->status === 'production_completed') {
            $remainingAmount = $request->calculateRemainingAmount();
            
            return [
                'message' => "🎊 **Sản phẩm đã hoàn thành!**\n\n📸 *[Ảnh sản phẩm hoàn thành sẽ được admin gửi]*\n\n💳 **Thanh toán phần còn lại:**\n• Số tiền: **" . number_format($remainingAmount) . "đ**\n\n✅ Sau khi thanh toán, chúng tôi sẽ giao hàng ngay!\n\n🚚 **Thời gian giao hàng:** 2-3 ngày làm việc",
                'context' => [
                    'current_intent' => 'CUSTOM_REQUEST',
                    'step' => 'final_payment',
                    'request_id' => $request->id
                ],
                'actions' => [
                    [
                        'type' => 'final_payment',
                        'label' => '💳 Thanh toán ngay',
                        'data' => [
                            'request_id' => $request->id,
                            'amount' => $remainingAmount
                        ]
                    ]
                ]
            ];
        }

        // Trạng thái khác
        return [
            'message' => "📊 **Trạng thái yêu cầu #{$request->id}:**\n\n🔄 {$request->status_text}\n\n💬 Bạn có câu hỏi gì về đơn hàng không?",
            'context' => [
                'current_intent' => 'CUSTOM_REQUEST',
                'step' => 'check_status',
                'request_id' => $request->id
            ]
        ];
    }

    private function saveAdditionalMessage(CustomProductRequest $request, string $message, string $sender): void
    {
        // Lưu tin nhắn bổ sung vào admin_notes hoặc tạo bảng riêng nếu cần
        $currentNotes = $request->admin_notes ? json_decode($request->admin_notes, true) : [];
        $currentNotes[] = [
            'sender' => $sender,
            'message' => $message,
            'timestamp' => now()->toISOString()
        ];
        
        $request->update([
            'admin_notes' => json_encode($currentNotes)
        ]);
    }

    private function parseProductType(string $input): string
    {
        $input = strtolower(trim($input));
        
        // Custom Request options
        if (str_contains($input, '1') || str_contains($input, 'móc khóa')) {
            return 'Móc khóa len';
        } elseif (str_contains($input, '2') || str_contains($input, 'thú bông')) {
            return 'Thú bông';
        } elseif (str_contains($input, '3') || str_contains($input, 'túi')) {
            return 'Túi xách';
        } elseif (str_contains($input, '4') || str_contains($input, 'áo')) {
            return 'Áo len';
        } elseif (str_contains($input, '5') || str_contains($input, 'khăn')) {
            return 'Khăn len';
        } 
        // Material Estimate options
        elseif (str_contains($input, 'áo len') || str_contains($input, 'áo')) {
            return 'Áo len';
        } elseif (str_contains($input, 'khăn len') || str_contains($input, 'khăn')) {
            return 'Khăn len';
        } elseif (str_contains($input, 'mũ len') || str_contains($input, 'mũ')) {
            return 'Mũ len';
        } elseif (str_contains($input, 'thú bông') || str_contains($input, 'gấu') || str_contains($input, 'thỏ')) {
            return 'Thú bông';
        } elseif (str_contains($input, 'túi xách') || str_contains($input, 'túi')) {
            return 'Túi xách';
        } elseif (str_contains($input, 'phụ kiện') || str_contains($input, 'găng tay') || str_contains($input, 'tất')) {
            return 'Phụ kiện len';
        } else {
            return ucfirst($input); // Trả về input gốc nếu là "khác"
        }
    }

    private function handleAdminResponseCheck(CustomProductRequest $request, string $message): array
    {
        // This method is now integrated into continueExistingRequest
        return $this->continueExistingRequest($request, $message, []);
    }

    private function handleMaterialEstimate(string $message, string $sessionId, ?int $userId): array
    {
        // Nếu user chọn từ quick action "ước tính số lượng len cần thiết"
        if (strtolower($message) === 'tôi muốn ước tính số lượng len cần thiết') {
            return [
                'message' => "📏 **Ước tính nguyên liệu cần thiết**\n\n" .
                           "Tôi sẽ giúp bạn tính toán chính xác số lượng len và nguyên liệu cần thiết!\n\n" .
                           "Bạn muốn làm sản phẩm gì?\n\n" .
                           "🧥 **Áo len** - Áo dài tay, áo vest\n" .
                           "🧣 **Khăn len** - Khăn quàng cổ, khăn choàng\n" .
                           "🎩 **Mũ len** - Mũ beanie, mũ bucket\n" .
                           "🧸 **Thú bông** - Gấu, thỏ, các loại thú cưng\n" .
                           "👜 **Túi xách** - Túi tote, túi đeo chéo\n" .
                           "🧤 **Phụ kiện** - Găng tay, tất len\n\n" .
                           "Hãy cho tôi biết loại sản phẩm bạn muốn làm nhé! 😊",
                'context' => [
                    'current_intent' => 'MATERIAL_ESTIMATE',
                    'step' => 'product_type',
                    'waiting_for' => 'product_type'
                ]
            ];
        }
        
        $lastChat = ChatLog::where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->first();

        $context = $lastChat->context ?? [];
        $step = $context['step'] ?? 'start';

        switch ($step) {
            case 'start':
                return [
                    'message' => 'Tôi sẽ giúp bạn ước tính số lượng len cần thiết! 📏\n\nBạn muốn làm sản phẩm gì? (áo len, khăn, mũ, thú bông...)',
                    'context' => [
                        'current_intent' => 'MATERIAL_ESTIMATE',
                        'step' => 'product_type',
                        'waiting_for' => 'product_type'
                    ]
                ];

            case 'product_type':
                return [
                    'message' => "Kích thước {$message} bạn muốn làm là gì? (S, M, L, XL)",
                    'context' => [
                        'current_intent' => 'MATERIAL_ESTIMATE',
                        'step' => 'size',
                        'waiting_for' => 'size',
                        'product_type' => $message
                    ]
                ];

            case 'size':
                return [
                    'message' => 'Bạn muốn dùng loại len nào?\n1. Cotton (mềm mại, thoáng khí)\n2. Wool (ấm áp, sang trọng)\n3. Acrylic (bền đẹp, dễ giặt)\n\nChỉ cần trả lời số hoặc tên loại len nhé!',
                    'context' => [
                        'current_intent' => 'MATERIAL_ESTIMATE',
                        'step' => 'yarn_type',
                        'waiting_for' => 'yarn_type',
                        'product_type' => $context['product_type'],
                        'size' => $message
                    ]
                ];

            case 'yarn_type':
                // Xử lý input yarn type
                $yarnType = $this->parseYarnType($message);
                
                // Mock AI estimation
                $estimate = $this->mockAIEstimate($context['product_type'], $context['size'], $yarnType);
                
                // Lưu vào database
                $materialEstimate = MaterialEstimate::create([
                    'session_id' => $sessionId,
                    'user_id' => $userId,
                    'product_type' => $context['product_type'],
                    'size' => $context['size'],
                    'yarn_type' => $yarnType,
                    'estimated_materials' => $estimate['materials'],
                    'total_estimated_cost' => $estimate['total_cost']
                ]);

                $materialsText = '';
                foreach ($estimate['materials'] as $material) {
                    $materialsText .= "• {$material['name']}: {$material['quantity']} {$material['unit']} (~{$material['cost']}đ)\n";
                }

                return [
                    'message' => "Dựa trên thông tin bạn cung cấp, ước tính nguyên liệu cần thiết:\n\n{$materialsText}\n💰 Tổng chi phí ước tính: ~{$estimate['total_cost']}đ\n\nBạn có muốn tôi thêm các nguyên liệu này vào giỏ hàng không? 🛒",
                    'context' => null,
                    'actions' => [
                        [
                            'type' => 'add_to_cart',
                            'label' => 'Thêm vào giỏ hàng',
                            'data' => [
                                'estimate_id' => $materialEstimate->id,
                                'materials' => $estimate['materials']
                            ]
                        ]
                    ]
                ];

            default:
                return [
                    'message' => 'Có vẻ như có lỗi xảy ra. Bạn có thể bắt đầu lại bằng cách nói "ước tính len" nhé! 😊'
                ];
        }
    }

    private function parseYarnType(string $input): string
    {
        $input = strtolower($input);
        
        if (str_contains($input, '1') || str_contains($input, 'cotton')) {
            return 'cotton';
        } elseif (str_contains($input, '2') || str_contains($input, 'wool')) {
            return 'wool';
        } elseif (str_contains($input, '3') || str_contains($input, 'acrylic')) {
            return 'acrylic';
        }
        
        return 'cotton'; // default
    }

    private function mockAIEstimate(string $productType, string $size, string $yarnType): array
    {
        // Mock AI estimation - trong thực tế sẽ gọi AI service
        $baseQuantities = [
            'áo len' => ['S' => 300, 'M' => 350, 'L' => 400, 'XL' => 450],
            'khăn' => ['S' => 150, 'M' => 200, 'L' => 250, 'XL' => 300],
            'mũ' => ['S' => 100, 'M' => 120, 'L' => 140, 'XL' => 160],
            'thú bông' => ['S' => 200, 'M' => 250, 'L' => 300, 'XL' => 350],
        ];

        $yarnPrices = [
            'cotton' => 45000, // per 100g
            'wool' => 65000,
            'acrylic' => 35000
        ];

        $quantity = $baseQuantities[$productType][$size] ?? 200;
        $pricePerUnit = $yarnPrices[$yarnType] ?? 45000;
        
        $materials = [
            [
                'name' => "Len {$yarnType}",
                'quantity' => $quantity,
                'unit' => 'gram',
                'cost' => round(($quantity / 100) * $pricePerUnit)
            ]
        ];

        // Thêm phụ kiện nếu cần
        if (in_array($productType, ['áo len', 'khăn'])) {
            $materials[] = [
                'name' => 'Kim đan',
                'quantity' => 1,
                'unit' => 'bộ',
                'cost' => 25000
            ];
        }

        $totalCost = array_sum(array_column($materials, 'cost'));

        return [
            'materials' => $materials,
            'total_cost' => $totalCost
        ];
    }

    private function handleUnknown(string $message): array
    {
        return [
            'message' => 'Xin chào! Tôi có thể giúp bạn:\n\n🔍 Trả lời câu hỏi về sản phẩm, giao hàng, đổi trả\n🎨 Nhận đặt làm sản phẩm cá nhân hóa\n📏 Ước tính nguyên liệu cần thiết\n\nBạn cần hỗ trợ gì nhé? 😊'
        ];
    }

    public function getConversationHistory(Request $request): JsonResponse
    {
        $sessionId = $request->session_id;
        
        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID is required'
            ]);
        }

        $history = ChatLog::getConversationHistory($sessionId);

        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }

    public function getHistory(Request $request): JsonResponse
    {
        $sessionId = $request->query('session_id');
        
        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID is required'
            ], 400);
        }

        $history = ChatLog::getConversationHistory($sessionId, 20);

        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'session_id' => 'required|string'
        ]);

        try {
            $sessionId = $request->session_id;
            $image = $request->file('image');
            
            // Store image
            $path = $image->store('chatbot/images', 'public');
            
            // Find related custom request and add image
            $customRequest = CustomProductRequest::where('session_id', $sessionId)->first();
            if ($customRequest) {
                $images = $customRequest->reference_images ?? [];
                $images[] = $path;
                $customRequest->update(['reference_images' => $images]);
            }

            return response()->json([
                'success' => true,
                'image_path' => $path,
                'message' => 'Ảnh đã được upload thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi upload ảnh: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addEstimateToCart(Request $request): JsonResponse
    {
        $request->validate([
            'estimate_id' => 'required|integer|exists:material_estimates,id'
        ]);

        try {
            $estimate = MaterialEstimate::findOrFail($request->estimate_id);
            
            // Add to cart logic here (implement based on your cart system)
            // For now, just return success
            
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vào giỏ hàng thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi thêm vào giỏ hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processDepositPayment(Request $request): JsonResponse
    {
        $request->validate([
            'request_id' => 'required|integer|exists:custom_product_requests,id'
        ]);

        try {
            $customRequest = CustomProductRequest::findOrFail($request->request_id);
            
            if (!$customRequest->canPayDeposit()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể thanh toán đặt cọc ở trạng thái hiện tại'
                ], 400);
            }

            // Generate payment URL (implement based on your payment gateway)
            $paymentUrl = $this->generatePaymentUrl($customRequest, 'deposit');
            
            return response()->json([
                'success' => true,
                'payment_url' => $paymentUrl,
                'message' => 'Chuyển hướng đến trang thanh toán'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xử lý thanh toán: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processFinalPayment(Request $request): JsonResponse
    {
        $request->validate([
            'request_id' => 'required|integer|exists:custom_product_requests,id'
        ]);

        try {
            $customRequest = CustomProductRequest::findOrFail($request->request_id);
            
            if (!$customRequest->canPayFinal()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể thanh toán phần còn lại ở trạng thái hiện tại'
                ], 400);
            }

            // Generate payment URL (implement based on your payment gateway)
            $paymentUrl = $this->generatePaymentUrl($customRequest, 'final');
            
            return response()->json([
                'success' => true,
                'payment_url' => $paymentUrl,
                'message' => 'Chuyển hướng đến trang thanh toán'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xử lý thanh toán: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generatePaymentUrl(CustomProductRequest $customRequest, string $type): string
    {
        // This is a placeholder - implement based on your payment gateway
        // For example, VNPay, MoMo, ZaloPay, etc.
        
        $amount = $type === 'deposit' ? $customRequest->deposit_amount : $customRequest->remaining_amount;
        $orderCode = $customRequest->order_code ?: 'CR' . $customRequest->id . '_' . time();
        
        // Update order code if not set
        if (!$customRequest->order_code) {
            $customRequest->update(['order_code' => $orderCode]);
        }
        
        // Return a placeholder URL - replace with actual payment gateway integration
        return "/checkout/custom-request/{$customRequest->id}?type={$type}&amount={$amount}";
    }

    /**
     * Parse contact information from user message
     */
    private function parseContactInfo(string $message): array
    {
        $lines = explode("\n", $message);
        $contactInfo = [
            'name' => null,
            'phone' => null,
            'email' => null
        ];

        foreach ($lines as $line) {
            $line = trim($line);
            
            // Parse name
            if (preg_match('/^(họ tên|tên|name):\s*(.+)$/i', $line, $matches)) {
                $contactInfo['name'] = trim($matches[2]);
            }
            
            // Parse phone
            if (preg_match('/^(số điện thoại|điện thoại|phone|sdt):\s*(.+)$/i', $line, $matches)) {
                $contactInfo['phone'] = trim($matches[2]);
            }
            
            // Parse email
            if (preg_match('/^(email|mail):\s*(.+)$/i', $line, $matches)) {
                $contactInfo['email'] = trim($matches[2]);
            }
        }

        // Validate required fields
        $valid = !empty($contactInfo['name']) && 
                 !empty($contactInfo['phone']) && 
                 !empty($contactInfo['email']) &&
                 filter_var($contactInfo['email'], FILTER_VALIDATE_EMAIL);

        return [
            'valid' => $valid,
            'data' => $contactInfo
        ];
    }

    /**
     * Create custom product request
     */
    private function createCustomRequest(array $context, string $sessionId, ?int $userId, ?array $contactInfo): array
    {
        $uploadedImages = $context['uploaded_images'] ?? [];
        
        // Tạo mô tả chi tiết từ các thông tin đã thu thập
        $detailedDescription = "🎨 THÔNG TIN SẢN PHẨM:\n";
        $detailedDescription .= "• Loại sản phẩm: {$context['product_type']}\n";
        $detailedDescription .= "• Kích thước: {$context['size']}\n";
        $detailedDescription .= "• Màu sắc: {$context['color']}\n";
        $detailedDescription .= "• Phong cách: {$context['style']}\n";
        $detailedDescription .= "• Mục đích sử dụng: {$context['purpose']}\n";
        $detailedDescription .= "• Chi tiết đặc biệt: {$context['special_details']}\n";
        
        $requestData = [
            'session_id' => $sessionId,
            'user_id' => $userId,
            'product_type' => $context['product_type'],
            'size' => $context['size'],
            'description' => $detailedDescription,
            'reference_images' => $uploadedImages,
            'status' => 'pending_admin_response',
            'deposit_percentage' => 30.0
        ];

        // Add contact info if provided (for non-logged-in users)
        if ($contactInfo) {
            $requestData['contact_info'] = json_encode($contactInfo);
        }

        $request = CustomProductRequest::create($requestData);

        $contactText = '';
        if ($contactInfo) {
            $contactText = "\n• 📞 Liên hệ: {$contactInfo['name']} - {$contactInfo['phone']}";
        }

        $summaryText = "📋 **THÔNG TIN TÓM TẮT:**\n";
        $summaryText .= "• 🎨 Sản phẩm: {$context['product_type']}\n";
        $summaryText .= "• 📏 Kích thước: {$context['size']}\n";
        $summaryText .= "• 🌈 Màu sắc: {$context['color']}\n";
        $summaryText .= "• 🎭 Phong cách: {$context['style']}\n";
        $summaryText .= "• 🎯 Mục đích: {$context['purpose']}\n";
        $summaryText .= "• ✨ Chi tiết đặc biệt: {$context['special_details']}\n";
        $summaryText .= "• 📸 Ảnh tham khảo: " . count($uploadedImages) . " ảnh{$contactText}";

        return [
            'message' => "� **YoÊU CẦU ĐÃ ĐƯỢC GỬI THÀNH CÔNG!**\n\n{$summaryText}\n\n🆔 **Mã yêu cầu:** #{$request->id}\n\n⏰ **Thời gian phản hồi:** Admin sẽ xem xét và phản hồi trong vòng **24 giờ**.\n\n💬 Bạn có thể tiếp tục chat để theo dõi tiến độ hoặc bổ sung thông tin!",
            'context' => [
                'current_intent' => 'CUSTOM_REQUEST',
                'step' => 'waiting_admin',
                'request_id' => $request->id,
                'waiting_for' => 'admin_response'
            ]
        ];
    }

    /**
     * Reset conversation context
     */
    public function resetConversation(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);

        try {
            $sessionId = $request->session_id;
            
            // Xóa tất cả chat logs của session này
            ChatLog::where('session_id', $sessionId)->delete();
            
            // Hủy các custom requests đang pending (không xóa hoàn toàn để admin vẫn có thể xem)
            CustomProductRequest::where('session_id', $sessionId)
                ->whereIn('status', ['pending', 'pending_admin_response'])
                ->update(['status' => 'cancelled']);
                
            // Xóa material estimates chưa hoàn thành
            MaterialEstimate::where('session_id', $sessionId)
                ->where('status', 'pending')
                ->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Đã làm mới chatbot thành công! Bạn có thể bắt đầu cuộc trò chuyện mới.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi reset cuộc trò chuyện: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get chatbot statistics (for admin)
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $stats = [
                'total_conversations' => ChatLog::distinct('session_id')->count(),
                'total_messages' => ChatLog::count(),
                'custom_requests' => CustomProductRequest::count(),
                'material_estimates' => MaterialEstimate::count(),
                'intents_breakdown' => ChatLog::selectRaw('intent, COUNT(*) as count')
                    ->groupBy('intent')
                    ->pluck('count', 'intent')
                    ->toArray()
            ];

            return response()->json([
                'success' => true,
                'statistics' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi lấy thống kê: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get FAQ list for chatbot display
     */
    public function getFaqList(): JsonResponse
    {
        try {
            $faqs = FaqItem::active()
                ->byPriority()
                ->get(['id', 'category', 'question', 'keywords', 'usage_count']);

            $categories = FaqItem::getCategories();
            
            return response()->json([
                'success' => true,
                'faqs' => $faqs,
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi lấy danh sách FAQ: ' . $e->getMessage()
            ], 500);
        }
    }
}