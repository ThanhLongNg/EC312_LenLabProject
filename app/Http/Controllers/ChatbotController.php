<?php

namespace App\Http\Controllers;

use App\Models\ChatLog;
use App\Models\CustomProductRequest;
use App\Models\ChatSupportLog;
use App\Models\MaterialEstimate;
use App\Models\FaqItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{

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

        // Kiểm tra xem có thông báo từ admin không (chỉ hiển thị thông báo, không chat trực tiếp)
        $adminNotifications = ChatLog::where('session_id', $sessionId)
            ->where('intent', 'ADMIN_NOTIFICATION')
            ->whereJsonDoesntContain('context->read', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Nếu có thông báo từ admin, hiển thị với nút "Mở chat"
        if ($adminNotifications->isNotEmpty()) {
            $notification = $adminNotifications->first();
            
            // Đánh dấu đã đọc
            $context = $notification->context ?? [];
            $context['read'] = true;
            $notification->update(['context' => $context]);
            
            $customRequestId = $context['custom_request_id'] ?? null;
            
            // Trả về thông báo với nút redirect
            return response()->json([
                'success' => true,
                'message' => $notification->bot_reply . "\n\n💬 **Để trao đổi chi tiết, vui lòng mở Chat Support**",
                'session_id' => $sessionId,
                'intent' => 'ADMIN_NOTIFICATION',
                'context' => $notification->context,
                'is_admin_notification' => true,
                'actions' => $customRequestId ? [
                    [
                        'type' => 'redirect',
                        'label' => '💬 Mở Chat Support',
                        'url' => "/chat-support/{$customRequestId}"
                    ]
                ] : []
            ]);
        }

        // Phân loại intent
        $intent = $this->classifyIntent($userMessage, $sessionId);
        
        // Xử lý theo intent
        $botReply = match($intent) {
            'FAQ' => $this->handleFAQ($userMessage),
            'CUSTOM_REQUEST' => $this->handleCustomRequest($userMessage, $sessionId, $userId),
            'MATERIAL_ESTIMATE' => $this->handleMaterialEstimate($userMessage, $sessionId, $userId),
            default => $this->handleUnknown($userMessage)
        };

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
        $message = strtolower($message);
        
        // Kiểm tra context từ conversation trước
        $lastChat = ChatLog::where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->first();
            
        if ($lastChat && $lastChat->context) {
            $context = $lastChat->context;
            if (isset($context['waiting_for']) && $context['waiting_for']) {
                return $context['current_intent'] ?? 'UNKNOWN';
            }
        }

        // Keywords cho Custom Request
        $customKeywords = ['làm riêng', 'đặt làm', 'thiết kế riêng', 'cá nhân hóa', 'custom', 'đặt hàng riêng'];
        foreach ($customKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return 'CUSTOM_REQUEST';
            }
        }

        // Keywords cho Material Estimate
        $materialKeywords = ['ước tính len', 'cần bao nhiêu len', 'tính len', 'nguyên liệu', 'estimate'];
        foreach ($materialKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return 'MATERIAL_ESTIMATE';
            }
        }

        // Kiểm tra FAQ keywords từ database
        $faqs = FaqItem::active()->get();
        foreach ($faqs as $faq) {
            $keywords = is_array($faq->keywords) ? $faq->keywords : json_decode($faq->keywords ?? '[]', true);
            
            foreach (($keywords ?? []) as $keyword) {
                $keyword = mb_strtolower(trim($keyword));
                if ($keyword !== '' && mb_strpos($message, $keyword) !== false) {
                    return 'FAQ';
                }
            }
        }

        return 'FAQ'; // Mặc định là FAQ
    }

    /**
     * FLOW MỚI: Xử lý yêu cầu sản phẩm cá nhân hóa (KHÔNG dùng đặt cọc)
     */
    private function handleCustomRequest(string $message, string $sessionId, ?int $userId): array
    {
        // BƯỚC 1: KHỞI TẠO YÊU CẦU
        if (str_contains(strtolower($message), 'custom') && str_contains(strtolower($message), 'cá nhân hóa')) {
            return [
                'message' => "🎨 **Tạo sản phẩm cá nhân hóa**\n\n" .
                           "Tuyệt vời! Tôi sẽ giúp bạn tạo sản phẩm riêng theo ý muốn.\n\n" .
                           "**Bước 1:** Bạn muốn làm loại sản phẩm gì?\n\n" .
                           "1️⃣ **Móc khóa len**\n" .
                           "2️⃣ **Thú bông**\n" .
                           "3️⃣ **Túi xách**\n" .
                           "4️⃣ **Áo len**\n" .
                           "5️⃣ **Khăn len**\n" .
                           "6️⃣ **Khác**\n\n" .
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

        // Kiểm tra request đang active - CHỈ áp dụng cho các trạng thái sau khi đã tạo request
        $existingRequest = CustomProductRequest::where('session_id', $sessionId)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->first();

        // Nếu có request đang active VÀ đang ở trạng thái chờ admin hoặc đang trao đổi
        if ($existingRequest && in_array($step, ['waiting_admin', 'in_discussion', 'awaiting_payment', 'payment_submitted', 'paid'])) {
            return $this->continueExistingRequest($existingRequest, $message, $context);
        }

        // BƯỚC 2: THU THẬP THÔNG TIN
        switch ($step) {
            case 'start':
                return [
                    'message' => '🎨 **Tạo sản phẩm cá nhân hóa**\n\nTôi sẽ giúp bạn tạo yêu cầu sản phẩm riêng theo ý muốn!\n\n**Bước 1:** Bạn muốn làm loại sản phẩm gì?\n\n1️⃣ Móc khóa len\n2️⃣ Thú bông\n3️⃣ Túi xách\n4️⃣ Áo len\n5️⃣ Khăn len\n6️⃣ Khác\n\nVui lòng chọn số hoặc gõ tên sản phẩm! 😊',
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'product_type',
                        'waiting_for' => 'product_type'
                    ]
                ];

            case 'product_type':
                $productType = $this->parseProductType($message);
                
                return [
                    'message' => "✅ Loại sản phẩm: **{$productType}**\n\n**Bước 2:** Bạn muốn kích thước như thế nào?\n\n📏 **Ví dụ:**\n• Nhỏ (10-15cm)\n• Vừa (20-25cm)\n• Lớn (30-35cm)\n• Hoặc kích thước cụ thể: 20cm x 15cm\n\nVui lòng cho biết kích thước mong muốn:",
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'size',
                        'waiting_for' => 'size',
                        'product_type' => $productType
                    ]
                ];

            case 'size':
                return [
                    'message' => "✅ Kích thước: **{$message}**\n\n**Bước 3:** Vui lòng mô tả chi tiết sản phẩm bạn muốn:\n\n💡 **Hãy bao gồm:**\n• Màu sắc mong muốn\n• Phong cách thiết kế\n• Chi tiết đặc biệt\n• Mục đích sử dụng\n\n*Mô tả càng chi tiết càng giúp chúng tôi hiểu rõ yêu cầu của bạn!*",
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'description',
                        'waiting_for' => 'description',
                        'product_type' => $context['product_type'],
                        'size' => $message
                    ]
                ];

            case 'description':
                // BƯỚC 3: UPLOAD ẢNH MINH HỌA
                return [
                    'message' => "✅ Mô tả: **{$message}**\n\n**Bước 4:** Upload ảnh tham khảo (tùy chọn)\n\n📸 **Bạn có thể gửi:**\n• Ảnh sản phẩm mẫu\n• Ảnh phong cách mong muốn\n• Sketch hoặc ý tưởng\n• Ảnh màu sắc tham khảo\n\n👆 Nhấn nút **\"📸 Upload ảnh\"** để tải ảnh lên hoặc gõ **\"bỏ qua\"** nếu không có ảnh.\nSau khi upload xong, gõ **\"tiếp tục\"** để hoàn thành.",
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'upload_images',
                        'waiting_for' => 'images',
                        'product_type' => $context['product_type'],
                        'size' => $context['size'],
                        'description' => $message,
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
                    // KIỂM TRA ĐĂNG NHẬP BẮT BUỘC
                    if (!$userId) {
                        // Guest user PHẢI đăng nhập/đăng ký để mua
                        return [
                            'message' => "🔐 **Yêu cầu đăng nhập**\n\n" .
                                       "Để tiếp tục tạo yêu cầu sản phẩm cá nhân hóa, bạn cần đăng nhập hoặc đăng ký tài khoản.\n\n" .
                                       "🎯 **Lợi ích khi có tài khoản:**\n" .
                                       "• Theo dõi tiến độ đơn hàng\n" .
                                       "• Lưu lịch sử trao đổi\n" .
                                       "• Quản lý địa chỉ giao hàng\n" .
                                       "• Nhận thông báo cập nhật\n\n" .
                                       "👆 Vui lòng đăng nhập/đăng ký rồi quay lại chat để tiếp tục!",
                            'context' => null, // Reset context
                            'actions' => [
                                [
                                    'type' => 'redirect',
                                    'label' => '🔑 Đăng nhập',
                                    'url' => '/login'
                                ],
                                [
                                    'type' => 'redirect', 
                                    'label' => '📝 Đăng ký',
                                    'url' => '/register'
                                ]
                            ]
                        ];
                    } else {
                        // User đã đăng nhập, tạo request luôn
                        $createdRequest = $this->createCustomRequest($context, $sessionId, $userId, null);
                        
                        // Thêm action để mở chat support nếu cần
                        if (isset($createdRequest['context']['request_id'])) {
                            $createdRequest['actions'][] = [
                                'type' => 'redirect',
                                'label' => '💬 Mở Chat Support',
                                'url' => "/chat-support/{$createdRequest['context']['request_id']}"
                            ];
                        }
                        
                        return $createdRequest;
                    }
                } else {
                    // Hiển thị ảnh đã upload (nếu có)
                    $uploadedImages = $context['uploaded_images'] ?? [];
                    $imagePreview = '';
                    
                    if (!empty($uploadedImages)) {
                        $imagePreview = "\n\n📸 **Ảnh đã upload (" . count($uploadedImages) . "):**\n";
                        foreach ($uploadedImages as $index => $imagePath) {
                            $imageUrl = asset('storage/' . $imagePath);
                            $imagePreview .= "• Ảnh " . ($index + 1) . ": Đã lưu thành công\n";
                        }
                    }
                    
                    return [
                        'message' => '📸 Vui lòng upload ảnh tham khảo bằng nút **"📸 Upload ảnh"** hoặc:\n• Gõ **"tiếp tục"** nếu đã upload xong\n• Gõ **"bỏ qua"** nếu không có ảnh' . $imagePreview . '\n\n💡 *Tip: Ảnh tham khảo giúp admin hiểu rõ yêu cầu của bạn hơn!*',
                        'context' => $context,
                        'actions' => [
                            [
                                'type' => 'upload_image',
                                'label' => '📸 Upload ảnh tham khảo',
                                'multiple' => true,
                                'max_files' => 3
                            ]
                        ],
                        'uploaded_images' => array_map(function($imagePath) {
                            return [
                                'path' => $imagePath,
                                'url' => asset('storage/' . $imagePath),
                                'preview_html' => '<img src="' . asset('storage/' . $imagePath) . '" alt="Ảnh tham khảo" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd; margin: 5px 0;">'
                            ];
                        }, $uploadedImages)
                    ];
                }

        }

        return ['message' => 'Có lỗi xảy ra. Vui lòng thử lại.'];
    }

    /**
     * Tiếp tục xử lý request đang có
     */
    private function continueExistingRequest(CustomProductRequest $request, string $message, array $context): array
    {
        switch ($request->status) {
            case 'pending_admin_response':
                // Lưu tin nhắn bổ sung từ khách hàng
                $this->saveCustomerMessage($request, $message);

                return [
                    'message' => "📝 **Tin nhắn đã được ghi nhận!**\n\n💬 Nội dung: \"{$message}\"\n\n🆔 **Mã yêu cầu:** #{$request->id}\n\n🔔 Admin sẽ phản hồi sớm nhất có thể.",
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'waiting_admin',
                        'request_id' => $request->id
                    ]
                ];

            case 'in_discussion':
                // BƯỚC 5: ADMIN PHẢN HỒI & TRAO ĐỔI
                $this->saveCustomerMessage($request, $message);

                return [
                    'message' => "💬 **Tin nhắn đã được gửi cho admin!**\n\n📝 Nội dung: \"{$message}\"\n\n🆔 **Mã yêu cầu:** #{$request->id}",
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'in_discussion',
                        'request_id' => $request->id
                    ]
                ];

            case 'awaiting_payment':
                // BƯỚC 6: DẪN FLOW THANH TOÁN (1 LẦN)
                return [
                    'message' => "💰 **Yêu cầu đã được chốt giá!**\n\n🆔 **Mã yêu cầu:** #{$request->id}\n💵 **Tổng số tiền:** " . number_format($request->final_price) . "đ\n📅 **Thời gian hoàn thành:** {$request->estimated_completion_days} ngày\n\n🚀 Nhấn nút bên dưới để tiến hành thanh toán:",
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'awaiting_payment',
                        'request_id' => $request->id
                    ],
                    'actions' => [
                        [
                            'type' => 'payment',
                            'label' => '💳 Tiến hành thanh toán',
                            'data' => [
                                'request_id' => $request->id,
                                'amount' => $request->final_price
                            ]
                        ]
                    ]
                ];

            case 'payment_submitted':
                return [
                    'message' => "✅ **Đã nhận thông tin thanh toán!**\n\n🆔 **Mã yêu cầu:** #{$request->id}\n⏳ **Trạng thái:** Chờ admin xác nhận thanh toán",
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'payment_submitted',
                        'request_id' => $request->id
                    ]
                ];

            case 'paid':
                // BƯỚC 8: HIỂN THỊ TRONG PROFILE KHÁCH HÀNG
                return [
                    'message' => "🎉 **Thanh toán thành công!**\n\n🆔 **Mã yêu cầu:** #{$request->id}\n🏭 **Trạng thái:** Đang sản xuất\n📅 **Dự kiến hoàn thành:** {$request->estimated_completion_days} ngày\n\n📞 Admin sẽ liên hệ khi sản phẩm hoàn thành!",
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'paid',
                        'request_id' => $request->id
                    ]
                ];

            case 'completed':
                return [
                    'message' => "🎊 **Đơn hàng đã hoàn thành!**\n\n🆔 **Mã yêu cầu:** #{$request->id}\n✅ **Trạng thái:** Hoàn thành\n\n🙏 Cảm ơn bạn đã sử dụng dịch vụ!",
                    'context' => [
                        'current_intent' => 'CUSTOM_REQUEST',
                        'step' => 'completed',
                        'request_id' => $request->id
                    ]
                ];

            case 'cancelled':
                return [
                    'message' => "❌ **Yêu cầu đã bị hủy**\n\n🆔 **Mã yêu cầu:** #{$request->id}\n📝 **Lý do:** {$request->cancelled_reason}\n\n🔄 Bạn có thể tạo yêu cầu mới!",
                    'context' => null
                ];
        }

        return ['message' => 'Trạng thái không xác định.'];
    }

    /**
     * Lưu tin nhắn từ khách hàng
     */
    private function saveCustomerMessage(CustomProductRequest $request, string $message): void
    {
        ChatSupportLog::create([
            'custom_request_id' => $request->id,
            'sender_type' => 'customer',
            'sender_id' => $request->user_id,
            'message' => $message,
            'is_read' => false
        ]);
    }

    private function parseProductType(string $input): string
    {
        $input = strtolower(trim($input));
        
        // Kiểm tra chính xác số
        if ($input === '1' || str_contains($input, 'móc khóa')) {
            return 'Móc khóa len';
        } elseif ($input === '2' || str_contains($input, 'thú bông')) {
            return 'Thú bông';
        } elseif ($input === '3' || str_contains($input, 'túi')) {
            return 'Túi xách';
        } elseif ($input === '4' || str_contains($input, 'áo')) {
            return 'Áo len';
        } elseif ($input === '5' || str_contains($input, 'khăn')) {
            return 'Khăn len';
        } else {
            return ucfirst($input);
        }
    }

    /**
     * BƯỚC 4: TẠO YÊU CẦU CHO ADMIN
     */
    private function createCustomRequest(array $context, string $sessionId, ?int $userId, ?array $contactInfo = null): array
    {
        $uploadedImages = $context['uploaded_images'] ?? [];
        
        $detailedDescription = "🎨 THÔNG TIN SẢN PHẨM:\n";
        $detailedDescription .= "• Loại sản phẩm: {$context['product_type']}\n";
        $detailedDescription .= "• Kích thước: {$context['size']}\n";
        $detailedDescription .= "• Mô tả chi tiết: {$context['description']}\n";
        
        $requestData = [
            'session_id' => $sessionId,
            'user_id' => $userId, // Bắt buộc phải có user_id (đã đăng nhập)
            'product_type' => $context['product_type'],
            'size' => $context['size'],
            'description' => $detailedDescription,
            'reference_images' => $uploadedImages,
            'status' => 'pending_admin_response'
        ];

        $request = CustomProductRequest::create($requestData);

        return [
            'message' => "✅ **YÊU CẦU ĐÃ ĐƯỢC GỬI THÀNH CÔNG!**\n\n🆔 **Mã yêu cầu:** #{$request->id}\n\n⏰ **Thời gian phản hồi:** Admin sẽ xem xét và phản hồi trong vòng **24 giờ**.\n\n💬 Bạn có thể tiếp tục chat để theo dõi tiến độ hoặc bổ sung thông tin!",
            'context' => [
                'current_intent' => 'CUSTOM_REQUEST',
                'step' => 'waiting_admin',
                'request_id' => $request->id,
                'waiting_for' => 'admin_response'
            ]
        ];
    }

    private function handleFAQ(string $message): array
    {
        $text = mb_strtolower(trim($message));
        
        // Lấy FAQ active theo priority
        $faqs = FaqItem::active()->byPriority()->get();
        
        foreach ($faqs as $faq) {
            $keywords = is_array($faq->keywords) ? $faq->keywords : json_decode($faq->keywords ?? '[]', true);
            
            foreach (($keywords ?? []) as $kw) {
                $kw = mb_strtolower(trim($kw));
                if ($kw !== '' && mb_strpos($text, $kw) !== false) {
                    // Tăng usage_count để theo dõi thống kê
                    $faq->increment('usage_count');
                    
                    return [
                        'message' => $faq->answer,
                        'context' => [
                            'faq_id' => $faq->id,
                            'matched_keyword' => $kw
                        ]
                    ];
                }
            }
        }
        
        // Fallback nếu không tìm thấy FAQ phù hợp
        return [
            'message' => "Mình chưa có câu trả lời sẵn cho nội dung này 😅\n\n🔍 **Bạn có thể hỏi về:**\n• Giao hàng & vận chuyển\n• Đổi trả sản phẩm\n• Thanh toán\n• Thông tin sản phẩm\n• Chính sách bảo hành\n\nHoặc gõ **\"custom\"** để đặt làm sản phẩm riêng! 🎨"
        ];
    }

    private function handleMaterialEstimate(string $message, string $sessionId, ?int $userId): array
    {
        return [
            'message' => 'Tôi sẽ giúp bạn ước tính nguyên liệu cần thiết. Bạn muốn làm sản phẩm gì?'
        ];
    }

    private function handleUnknown(string $message): array
    {
        // Thử tìm FAQ gần đúng
        $faqs = FaqItem::active()->byPriority()->limit(3)->get();
        $suggestions = '';
        
        if ($faqs->isNotEmpty()) {
            $suggestions = "\n\n💡 **Có thể bạn muốn hỏi:**\n";
            foreach ($faqs as $index => $faq) {
                $firstKeyword = is_array($faq->keywords) ? 
                    ($faq->keywords[0] ?? 'FAQ') : 
                    (json_decode($faq->keywords ?? '[]', true)[0] ?? 'FAQ');
                $suggestions .= "• " . ucfirst($firstKeyword) . "\n";
            }
        }
        
        return [
            'message' => "👋 **Xin chào! Tôi là trợ lý ảo của Lenlab**\n\n🤖 **Tôi có thể giúp bạn:**\n\n🔍 **Trả lời câu hỏi** về sản phẩm, giao hàng, đổi trả\n🎨 **Đặt làm sản phẩm cá nhân hóa** (gõ \"custom\")\n📏 **Ước tính nguyên liệu** cần thiết\n💬 **Tư vấn sản phẩm** phù hợp" . $suggestions . "\n\n😊 **Bạn cần hỗ trợ gì ạ?**"
        ];
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
            $fullUrl = asset('storage/' . $path);
            
            // Lấy context hiện tại từ chat log
            $lastChat = ChatLog::where('session_id', $sessionId)
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($lastChat && $lastChat->context) {
                $context = $lastChat->context;
                
                // Thêm ảnh vào context nếu đang ở bước upload_images
                if (isset($context['step']) && $context['step'] === 'upload_images') {
                    $uploadedImages = $context['uploaded_images'] ?? [];
                    $uploadedImages[] = $path;
                    
                    // Cập nhật context với ảnh mới
                    $context['uploaded_images'] = $uploadedImages;
                    
                    // Cập nhật chat log với context mới
                    $lastChat->update(['context' => $context]);
                }
            }
            
            // Tìm và cập nhật custom request nếu đã tồn tại
            $customRequest = CustomProductRequest::where('session_id', $sessionId)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($customRequest) {
                $images = $customRequest->reference_images ?? [];
                $images[] = $path;
                $customRequest->update(['reference_images' => $images]);
            }

            return response()->json([
                'success' => true,
                'image_path' => $path,
                'image_url' => $fullUrl,
                'message' => 'Ảnh đã được upload thành công!',
                'file_name' => $image->getClientOriginalName(),
                'file_size' => $image->getSize(),
                'preview_html' => '<div class="uploaded-image-preview" style="margin: 10px 0;"><img src="' . $fullUrl . '" alt="Ảnh tham khảo" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;"><br><small style="color: #666;">Ảnh tham khảo đã upload</small></div>'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi upload ảnh: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách ảnh đã upload trong session
     */
    public function getUploadedImages(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);

        try {
            $sessionId = $request->session_id;
            
            // Lấy context từ chat log
            $lastChat = ChatLog::where('session_id', $sessionId)
                ->orderBy('created_at', 'desc')
                ->first();
                
            $uploadedImages = [];
            if ($lastChat && $lastChat->context && isset($lastChat->context['uploaded_images'])) {
                $images = $lastChat->context['uploaded_images'];
                
                foreach ($images as $imagePath) {
                    $uploadedImages[] = [
                        'path' => $imagePath,
                        'url' => asset('storage/' . $imagePath)
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'images' => $uploadedImages,
                'count' => count($uploadedImages)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi lấy danh sách ảnh: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa ảnh đã upload
     */
    public function deleteUploadedImage(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'image_path' => 'required|string'
        ]);

        try {
            $sessionId = $request->session_id;
            $imagePath = $request->image_path;
            
            // Lấy context từ chat log
            $lastChat = ChatLog::where('session_id', $sessionId)
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($lastChat && $lastChat->context && isset($lastChat->context['uploaded_images'])) {
                $context = $lastChat->context;
                $uploadedImages = $context['uploaded_images'];
                
                // Xóa ảnh khỏi array
                $uploadedImages = array_filter($uploadedImages, function($path) use ($imagePath) {
                    return $path !== $imagePath;
                });
                
                // Cập nhật context
                $context['uploaded_images'] = array_values($uploadedImages);
                $lastChat->update(['context' => $context]);
                
                // Xóa file vật lý (tùy chọn)
                $fullPath = storage_path('app/public/' . $imagePath);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa ảnh thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xóa ảnh: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xử lý thanh toán cho custom request
     */
    public function processPayment(Request $request): JsonResponse
    {
        try {
            // Log incoming request for debugging
            \Log::info('Payment request received', [
                'request_data' => $request->except(['payment_bill_image']),
                'has_image' => $request->hasFile('payment_bill_image'),
                'user_id' => auth()->id()
            ]);

            $request->validate([
                'request_id' => 'required|integer|exists:custom_product_requests,id',
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'customer_email' => 'required|email|max:255',
                'shipping_address' => 'required|string',
                'payment_bill_image' => 'required|image|mimes:jpeg,png,jpg|max:5120'
            ]);

            $customRequest = CustomProductRequest::findOrFail($request->request_id);
            
            // Check if user owns this request
            if (!auth()->check() || $customRequest->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền truy cập yêu cầu này'
                ], 403);
            }
            
            if (!$customRequest->canPay()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể thanh toán ở trạng thái hiện tại. Trạng thái: ' . $customRequest->status
                ], 400);
            }

            // Store payment bill image
            $billPath = $request->file('payment_bill_image')->store('payment_bills', 'public');
            
            $paymentInfo = [
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'payment_method' => 'bank_transfer',
                'amount' => $customRequest->final_price,
                'payment_date' => now()->toDateTimeString()
            ];

            // Submit payment using model method
            $customRequest->submitPayment($paymentInfo, $billPath);
            
            // Handle shipping address - convert string to structured array
            $shippingAddressData = [
                'full_address' => $request->shipping_address,
                'updated_at' => now()->toDateTimeString()
            ];
            
            // If address_data is provided (new address), add structured data
            if ($request->has('address_data')) {
                try {
                    $addressData = json_decode($request->address_data, true);
                    if ($addressData) {
                        $shippingAddressData = array_merge($shippingAddressData, $addressData);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to parse address_data', ['error' => $e->getMessage()]);
                }
            }
            
            // If selected_address_id is provided (saved address)
            if ($request->has('selected_address_id')) {
                $shippingAddressData['selected_address_id'] = $request->selected_address_id;
            }
            
            // Update shipping address
            $customRequest->update([
                'shipping_address' => $shippingAddressData
            ]);

            \Log::info('Payment processed successfully', [
                'request_id' => $customRequest->id,
                'user_id' => auth()->id(),
                'status' => $customRequest->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thông tin thanh toán đã được gửi thành công. Admin sẽ xác nhận sớm nhất có thể.'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Payment validation error', [
                'errors' => $e->errors(),
                'request_data' => $request->except(['payment_bill_image'])
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Payment processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_id' => $request->get('request_id'),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xử lý thanh toán: ' . $e->getMessage()
            ], 500);
        }
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

        $history = ChatLog::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }

    /**
     * Check for new admin messages
     */
    public function checkAdminMessages(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);

        $sessionId = $request->session_id;
        
        // Lấy tin nhắn admin chưa đọc
        $adminMessages = ChatLog::where('session_id', $sessionId)
            ->where('intent', 'ADMIN_RESPONSE')
            ->whereJsonDoesntContain('context->read', true)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($adminMessages->isNotEmpty()) {
            // Đánh dấu đã đọc
            foreach ($adminMessages as $message) {
                $context = $message->context ?? [];
                $context['read'] = true;
                $message->update(['context' => $context]);
            }

            return response()->json([
                'success' => true,
                'has_new_messages' => true,
                'messages' => $adminMessages->map(function($msg) {
                    return [
                        'message' => $msg->bot_reply,
                        'created_at' => $msg->created_at->format('H:i d/m/Y'),
                        'context' => $msg->context
                    ];
                })
            ]);
        }

        return response()->json([
            'success' => true,
            'has_new_messages' => false,
            'messages' => []
        ]);
    }

    public function resetConversation(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);

        try {
            $sessionId = $request->session_id;
            
            ChatLog::where('session_id', $sessionId)->delete();
            
            CustomProductRequest::where('session_id', $sessionId)
                ->whereIn('status', ['pending_admin_response'])
                ->update(['status' => 'cancelled']);
            
            return response()->json([
                'success' => true,
                'message' => 'Đã làm mới chatbot thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi reset: ' . $e->getMessage()
            ], 500);
        }
    }
}