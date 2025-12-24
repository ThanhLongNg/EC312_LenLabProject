<?php

namespace App\Http\Controllers;

use App\Models\CustomProductRequest;
use App\Models\ChatSupportLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MyRequestsController extends Controller
{
    /**
     * Display user's custom product requests
     */
    public function index()
    {
        $requests = CustomProductRequest::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('my-requests', compact('requests'));
    }

    /**
     * Get unread message count for current user
     */
    public function getUnreadCount(): JsonResponse
    {
        try {
            $userId = auth()->id();
            
            // Get all custom requests for this user
            $requestIds = CustomProductRequest::where('user_id', $userId)
                ->pluck('id');
            
            // Count unread admin messages across all user's requests
            $unreadCount = ChatSupportLog::whereIn('custom_request_id', $requestIds)
                ->where('sender_type', 'admin')
                ->where('is_read', false)
                ->count();
            
            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi kiểm tra tin nhắn: ' . $e->getMessage()
            ], 500);
        }
    }
}