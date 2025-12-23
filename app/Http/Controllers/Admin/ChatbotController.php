<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatLog;
use App\Models\CustomProductRequest;
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
        
        $chatHistory = ChatLog::where('session_id', $request->session_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.chatbot.chat-support-detail', compact('request', 'chatHistory'));
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
            'session_id' => 'required|string',
            'message' => 'required|string|max:1000',
            'custom_request_id' => 'nullable|integer|exists:custom_product_requests,id'
        ]);

        try {
            // If this is related to a custom request, update the request
            if ($request->custom_request_id) {
                $customRequest = CustomProductRequest::findOrFail($request->custom_request_id);
                
                // Update admin response
                $customRequest->update([
                    'admin_response' => $request->message,
                    'admin_responded_at' => now(),
                    'status' => 'admin_responded'
                ]);
            }

            // Log the admin message as a bot reply
            ChatLog::create([
                'session_id' => $request->session_id,
                'user_id' => null, // Admin message
                'user_message' => '[ADMIN MESSAGE]',
                'bot_reply' => $request->message,
                'intent' => 'ADMIN_RESPONSE',
                'context' => [
                    'admin_message' => true,
                    'custom_request_id' => $request->custom_request_id
                ]
            ]);

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
}