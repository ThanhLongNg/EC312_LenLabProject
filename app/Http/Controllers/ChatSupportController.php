<?php

namespace App\Http\Controllers;

use App\Models\CustomProductRequest;
use App\Models\ChatSupportLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatSupportController extends Controller
{
    /**
     * Display chat support page for user
     */
    public function show($requestId)
    {
        $request = CustomProductRequest::with('user')->findOrFail($requestId);
        
        // Check if user owns this request
        if (!auth()->check() || $request->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền truy cập yêu cầu này');
        }
        
        // Get chat history
        $chatHistory = ChatSupportLog::where('custom_request_id', $requestId)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat-support', compact('request', 'chatHistory'));
    }

    /**
     * Send message from user to admin
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'custom_request_id' => 'required|integer|exists:custom_product_requests,id',
            'message' => 'required|string|max:1000'
        ]);

        try {
            $customRequest = CustomProductRequest::findOrFail($request->custom_request_id);
            
            // Check if user owns this request
            if (!auth()->check() || $customRequest->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền truy cập yêu cầu này'
                ], 403);
            }

            // Save message
            ChatSupportLog::create([
                'custom_request_id' => $request->custom_request_id,
                'sender_type' => 'customer',
                'sender_id' => auth()->id(),
                'message' => $request->message,
                'is_read' => false
            ]);

            // Update request status if needed
            if ($customRequest->status === 'pending_admin_response') {
                $customRequest->update(['status' => 'in_discussion']);
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
     * Check for new messages from admin
     */
    public function checkMessages($requestId): JsonResponse
    {
        try {
            $customRequest = CustomProductRequest::findOrFail($requestId);
            
            // Check if user owns this request
            if (!auth()->check() || $customRequest->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền truy cập yêu cầu này'
                ], 403);
            }

            // Get unread admin messages
            $newMessages = ChatSupportLog::where('custom_request_id', $requestId)
                ->where('sender_type', 'admin')
                ->where('is_read', false)
                ->orderBy('created_at', 'asc')
                ->get();

            if ($newMessages->isNotEmpty()) {
                // Mark as read
                ChatSupportLog::where('custom_request_id', $requestId)
                    ->where('sender_type', 'admin')
                    ->where('is_read', false)
                    ->update(['is_read' => true]);

                return response()->json([
                    'success' => true,
                    'has_new_messages' => true,
                    'messages' => $newMessages->map(function($msg) {
                        return [
                            'message' => $msg->message,
                            'created_at' => $msg->created_at->format('H:i d/m/Y')
                        ];
                    })
                ]);
            }

            return response()->json([
                'success' => true,
                'has_new_messages' => false,
                'messages' => []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi kiểm tra tin nhắn: ' . $e->getMessage()
            ], 500);
        }
    }
}