<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatLog;
use App\Models\CustomProductRequest;
use App\Models\ChatSupportLog;
use App\Models\MaterialEstimate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ChatbotController extends Controller
{
    /**
     * Display custom product requests management page
     */
    public function customRequests()
    {
        $requests = CustomProductRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.chatbot.custom-requests', compact('requests'));
    }

    /**
     * Get detailed request information for modal
     */
    public function getRequestDetails($id): JsonResponse
    {
        try {
            $request = CustomProductRequest::with('user')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'request' => [
                    'id' => $request->id,
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'customer_email' => $request->customer_email,
                    'product_type' => $request->product_type,
                    'size' => $request->size,
                    'description' => $request->description,
                    'reference_images' => $request->reference_images,
                    'status' => $request->status,
                    'status_text' => $request->status_text,
                    'estimated_price' => $request->estimated_price,
                    'final_price' => $request->final_price,
                    'estimated_completion_days' => $request->estimated_completion_days,
                    'admin_response' => $request->admin_response,
                    'admin_notes' => $request->admin_notes,
                    'cancelled_reason' => $request->cancelled_reason,
                    'payment_info' => $request->payment_info,
                    'payment_bill_image' => $request->payment_bill_image,
                    'shipping_address' => $request->shipping_address,
                    'created_at' => $request->created_at->format('d/m/Y H:i'),
                    'admin_responded_at' => $request->admin_responded_at ? $request->admin_responded_at->format('d/m/Y H:i') : null,
                    'payment_submitted_at' => $request->payment_submitted_at ? $request->payment_submitted_at->format('d/m/Y H:i') : null,
                    'payment_confirmed_at' => $request->payment_confirmed_at ? $request->payment_confirmed_at->format('d/m/Y H:i') : null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi lấy thông tin yêu cầu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update custom product request
     */
    public function updateCustomRequest(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|string',
            'admin_response' => 'nullable|string',
            'estimated_price' => 'nullable|numeric|min:0',
            'deposit_percentage' => 'nullable|numeric|min:0|max:100',
            'estimated_completion_days' => 'nullable|integer|min:1'
        ]);

        try {
            $customRequest = CustomProductRequest::findOrFail($id);
            
            $updateData = [
                'status' => $request->status,
                'admin_response' => $request->admin_response,
                'admin_responded_at' => now()
            ];

            if ($request->estimated_price) {
                $updateData['estimated_price'] = $request->estimated_price;
                $updateData['deposit_percentage'] = $request->deposit_percentage ?? 30;
                $updateData['deposit_amount'] = ($request->estimated_price * ($request->deposit_percentage ?? 30)) / 100;
                $updateData['remaining_amount'] = $request->estimated_price - $updateData['deposit_amount'];
            }

            if ($request->estimated_completion_days) {
                $updateData['estimated_completion_days'] = $request->estimated_completion_days;
            }

            $customRequest->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật yêu cầu thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi cập nhật: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel custom product request
     */
    public function cancelRequest(Request $request, $id): JsonResponse
    {
        $request->validate([
            'cancelled_reason' => 'required|string|max:500'
        ]);

        try {
            $customRequest = CustomProductRequest::findOrFail($id);
            
            $customRequest->update([
                'status' => 'cancelled',
                'cancelled_reason' => $request->cancelled_reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã hủy yêu cầu'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi hủy yêu cầu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display chat support interface
     */
    public function chatSupport()
    {
        $activeRequests = CustomProductRequest::whereIn('status', [
            'pending_admin_response',
            'admin_responded',
            'in_discussion'
        ])
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->get();

        return view('admin.chatbot.chat-support', compact('activeRequests'));
    }

    /**
     * Display chat support for specific request
     */
    public function chatSupportWithRequest($requestId)
    {
        $request = CustomProductRequest::with('user')->findOrFail($requestId);
        
        // Lấy lịch sử chat support logs thay vì chat logs thông thường
        $chatHistory = ChatSupportLog::where('custom_request_id', $requestId)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.chatbot.chat-support-detail', compact('request', 'chatHistory'));
    }

    /**
     * Phản hồi yêu cầu - chuyển hướng sang chat support
     */
    public function respondToRequest($requestId)
    {
        $request = CustomProductRequest::findOrFail($requestId);
        
        if (!$request->canStartDiscussion()) {
            return redirect()->back()->with('error', 'Không thể phản hồi yêu cầu ở trạng thái hiện tại');
        }

        // Chuyển trạng thái sang in_discussion
        $request->startDiscussion();

        // Chuyển hướng sang trang chat support
        return redirect()->route('admin.chatbot.chat-support.detail', $requestId);
    }

    /**
     * Chốt yêu cầu & báo giá
     */
    public function finalizeRequest(Request $request, $requestId)
    {
        $request->validate([
            'final_price' => 'required|numeric|min:0',
            'estimated_completion_days' => 'required|integer|min:1'
        ]);

        try {
            $customRequest = CustomProductRequest::findOrFail($requestId);
            
            if (!$customRequest->canFinalize()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể chốt yêu cầu ở trạng thái hiện tại'
                ], 400);
            }

            // Chốt yêu cầu với giá cuối cùng
            $customRequest->finalizeRequest(
                $request->final_price,
                $request->estimated_completion_days
            );

            // Gửi thông báo cho khách hàng qua chatbot
            $this->sendNotificationToCustomer($customRequest, 
                "💰 **Yêu cầu #{$customRequest->order_id} đã được chốt giá!**\n\n" .
                "💵 **Tổng số tiền:** " . number_format($customRequest->final_price) . "đ\n" .
                "📅 **Thời gian hoàn thành:** {$customRequest->estimated_completion_days} ngày\n\n" .
                "🚀 Vui lòng tiến hành thanh toán để bắt đầu sản xuất!"
            );

            return response()->json([
                'success' => true,
                'message' => 'Đã chốt yêu cầu và báo giá thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi chốt yêu cầu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kết thúc hội thoại (hủy yêu cầu)
     */
    public function endConversation(Request $request, $requestId)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            $customRequest = CustomProductRequest::findOrFail($requestId);
            
            if (!$customRequest->canCancel()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể hủy yêu cầu ở trạng thái hiện tại'
                ], 400);
            }

            // Hủy yêu cầu
            $customRequest->cancelRequest($request->reason);

            // Gửi thông báo kết thúc lịch sự cho khách hàng
            $this->sendNotificationToCustomer($customRequest,
                "❌ **Yêu cầu #{$customRequest->order_id} đã được kết thúc**\n\n" .
                "📝 **Lý do:** {$request->reason}\n\n" .
                "🙏 Cảm ơn bạn đã quan tâm đến dịch vụ của chúng tôi. " .
                "Bạn có thể tạo yêu cầu mới bất cứ lúc nào!"
            );

            return response()->json([
                'success' => true,
                'message' => 'Đã kết thúc hội thoại'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi kết thúc hội thoại: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xác nhận thanh toán
     */
    public function confirmPayment($requestId)
    {
        try {
            $customRequest = CustomProductRequest::findOrFail($requestId);
            
            if (!$customRequest->canConfirmPayment()) {
                $statusText = $customRequest->status_text;
                return response()->json([
                    'success' => false,
                    'message' => "Không thể xác nhận thanh toán. Trạng thái hiện tại: {$statusText}. Chỉ có thể xác nhận thanh toán khi trạng thái là 'Đã gửi bill - Chờ xác nhận'."
                ], 400);
            }

            // Xác nhận thanh toán
            $customRequest->confirmPayment();

            // Gửi thông báo cho khách hàng
            $this->sendNotificationToCustomer($customRequest,
                "🎉 **Thanh toán đã được xác nhận!**\n\n" .
                "🆔 **Mã yêu cầu:** {$customRequest->order_id}\n" .
                "🏭 **Trạng thái:** Đang sản xuất\n" .
                "📅 **Dự kiến hoàn thành:** {$customRequest->estimated_completion_days} ngày\n\n" .
                "📞 Chúng tôi sẽ liên hệ với bạn khi sản phẩm hoàn thành!"
            );

            return response()->json([
                'success' => true,
                'message' => 'Đã xác nhận thanh toán thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác nhận thanh toán: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gửi thông báo cho khách hàng qua chatbot (không phải chat trực tiếp)
     */
    private function sendNotificationToCustomer(CustomProductRequest $customRequest, string $message): void
    {
        // Lưu vào chat logs như thông báo hệ thống (không phải chat trực tiếp)
        ChatLog::create([
            'session_id' => $customRequest->session_id,
            'user_id' => $customRequest->user_id,
            'user_message' => '[SYSTEM NOTIFICATION]',
            'bot_reply' => $message,
            'intent' => 'ADMIN_NOTIFICATION', // Đổi từ ADMIN_RESPONSE thành ADMIN_NOTIFICATION
            'context' => [
                'notification' => true,
                'custom_request_id' => $customRequest->id,
                'read' => false // Chưa đọc
            ]
        ]);
    }

    /**
     * Display chat logs
     */
    public function chatLogs()
    {
        $logs = ChatLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $stats = [
            'total_conversations' => ChatLog::distinct('session_id')->count(),
            'today_messages' => ChatLog::whereDate('created_at', today())->count(),
            'intents_breakdown' => ChatLog::selectRaw('intent, COUNT(*) as count')
                ->groupBy('intent')
                ->pluck('count', 'intent')
                ->toArray()
        ];

        return view('admin.chatbot.chat-logs', compact('logs', 'stats'));
    }

    /**
     * Display material estimates
     */
    public function materialEstimates()
    {
        $estimates = MaterialEstimate::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_estimates' => MaterialEstimate::count(),
            'added_to_cart' => MaterialEstimate::where('added_to_cart', true)->count(),
            'avg_cost' => MaterialEstimate::avg('total_estimated_cost'),
            'popular_products' => MaterialEstimate::selectRaw('product_type, COUNT(*) as count')
                ->groupBy('product_type')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->pluck('count', 'product_type')
                ->toArray()
        ];

        return view('admin.chatbot.material-estimates', compact('estimates', 'stats'));
    }

    /**
     * Display chatbot analytics
     */
    public function analytics()
    {
        $dateRange = request('range', '7days');
        
        switch ($dateRange) {
            case '24hours':
                $startDate = Carbon::now()->subDay();
                break;
            case '7days':
                $startDate = Carbon::now()->subWeek();
                break;
            case '30days':
                $startDate = Carbon::now()->subMonth();
                break;
            default:
                $startDate = Carbon::now()->subWeek();
        }

        // Messages over time
        $messagesOverTime = ChatLog::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Intent distribution
        $intentDistribution = ChatLog::where('created_at', '>=', $startDate)
            ->selectRaw('intent, COUNT(*) as count')
            ->groupBy('intent')
            ->pluck('count', 'intent')
            ->toArray();

        // Custom requests status
        $requestsStatus = CustomProductRequest::where('created_at', '>=', $startDate)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Popular products for estimates
        $popularEstimates = MaterialEstimate::where('created_at', '>=', $startDate)
            ->selectRaw('product_type, COUNT(*) as count, AVG(total_estimated_cost) as avg_cost')
            ->groupBy('product_type')
            ->orderBy('count', 'desc')
            ->get();

        // Response time analysis (mock data for now)
        $avgResponseTime = '2.3 phút';
        $satisfactionRate = '94%';

        return view('admin.chatbot.analytics', compact(
            'messagesOverTime',
            'intentDistribution', 
            'requestsStatus',
            'popularEstimates',
            'avgResponseTime',
            'satisfactionRate',
            'dateRange'
        ));
    }

    /**
     * Get chat history for admin interface
     */
    public function getChatHistory(Request $request): JsonResponse
    {
        $sessionId = $request->session_id;
        
        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Session ID is required'
            ], 400);
        }

        $history = ChatLog::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user_message' => $log->user_message,
                    'bot_reply' => $log->bot_reply,
                    'intent' => $log->intent,
                    'context' => $log->context,
                    'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                    'user' => $log->user ? $log->user->name : 'Guest'
                ];
            });

        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }

    /**
     * Send admin message to customer
     */
    public function sendAdminMessage(Request $request): JsonResponse
    {
        $request->validate([
            'custom_request_id' => 'required|integer|exists:custom_product_requests,id',
            'message' => 'required|string|max:1000'
        ]);

        try {
            $adminId = auth('admin')->id();
            
            // Nếu có custom_request_id, lưu vào chat support logs
            if ($request->custom_request_id) {
                $customRequest = CustomProductRequest::findOrFail($request->custom_request_id);
                
                // Lưu tin nhắn admin vào chat support logs
                ChatSupportLog::create([
                    'custom_request_id' => $request->custom_request_id,
                    'sender_type' => 'admin',
                    'sender_id' => $adminId,
                    'message' => $request->message,
                    'is_read' => false
                ]);

                // Cập nhật trạng thái nếu cần
                if ($customRequest->status === 'pending_admin_response') {
                    $customRequest->update(['status' => 'in_discussion']);
                }

                // Tạo notification cho user qua chatbot (sử dụng session_id từ custom request)
                if ($customRequest->session_id) {
                    ChatLog::create([
                        'session_id' => $customRequest->session_id,
                        'user_id' => $customRequest->user_id,
                        'user_message' => '[ADMIN MESSAGE]',
                        'bot_reply' => "📩 **Admin đã gửi tin nhắn mới**\n\n" . $request->message,
                        'intent' => 'ADMIN_NOTIFICATION',
                        'context' => [
                            'notification' => true,
                            'admin_message' => true,
                            'admin_id' => $adminId,
                            'custom_request_id' => $request->custom_request_id,
                            'read' => false
                        ]
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Tin nhắn đã được gửi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi gửi tin nhắn: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update custom request status from orders page
     */
    public function updateCustomRequestStatus(Request $request, $requestId)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,delivered,cancelled'
        ]);

        try {
            $customRequest = CustomProductRequest::findOrFail($requestId);
            
            // Map order status to custom request status and handle transitions
            switch ($request->status) {
                case 'pending':
                    // Chờ xác nhận - chỉ cho phép nếu đang ở trạng thái payment_submitted
                    if ($customRequest->status === 'payment_submitted') {
                        // Không thay đổi gì, giữ nguyên trạng thái
                    } else {
                        $customRequest->update(['status' => 'payment_submitted']);
                    }
                    break;
                    
                case 'processing':
                    // Đang sản xuất - xác nhận thanh toán
                    if ($customRequest->status === 'payment_submitted') {
                        $customRequest->confirmPayment();
                    } else {
                        $customRequest->update(['status' => 'paid']);
                    }
                    break;
                    
                case 'delivered':
                    // Hoàn thành
                    if ($customRequest->status === 'paid') {
                        $customRequest->markCompleted();
                    } else {
                        $customRequest->update(['status' => 'completed']);
                    }
                    break;
                    
                case 'cancelled':
                    // Hủy đơn
                    $customRequest->update([
                        'status' => 'cancelled',
                        'cancelled_reason' => 'Hủy từ trang quản lý đơn hàng'
                    ]);
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi cập nhật trạng thái: ' . $e->getMessage()
            ], 500);
        }
    }
}