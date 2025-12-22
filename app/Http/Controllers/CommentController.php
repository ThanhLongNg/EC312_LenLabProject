<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentImage;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    /**
     * Show product reviews page
     */
    public function index($productId)
    {
        $product = Product::findOrFail($productId);
        
        // Get all verified and visible comments for this product
        $comments = Comment::where('product_id', $productId)
            ->verified()
            ->visible()
            ->with(['user', 'images', 'replies.admin'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Calculate rating statistics
        $totalComments = Comment::where('product_id', $productId)->verified()->visible()->count();
        $averageRating = Comment::where('product_id', $productId)->verified()->visible()->avg('rating') ?? 0;
        
        // Rating distribution
        $ratingStats = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = Comment::where('product_id', $productId)
                ->verified()
                ->visible()
                ->where('rating', $i)
                ->count();
            $ratingStats[$i] = [
                'count' => $count,
                'percentage' => $totalComments > 0 ? round(($count / $totalComments) * 100) : 0
            ];
        }
        
        // Check if current user can review this product
        $canReview = false;
        $eligibleOrders = [];
        
        if (Auth::check()) {
            // Get delivered orders that contain this product and haven't been reviewed yet
            $eligibleOrders = Order::where('user_id', Auth::id())
                ->where('status', 'delivered')
                ->whereHas('orderItems', function($query) use ($productId) {
                    $query->where('product_id', $productId);
                })
                ->whereDoesntHave('comments', function($query) use ($productId) {
                    $query->where('product_id', $productId);
                })
                ->with('orderItems')
                ->get();
            
            $canReview = $eligibleOrders->count() > 0;
        }
        
        return view('product-reviews', compact(
            'product', 
            'comments', 
            'totalComments', 
            'averageRating', 
            'ratingStats',
            'canReview',
            'eligibleOrders'
        ));
    }
    
    /**
     * Show review form
     */
    public function create(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $productId = $request->product_id;
        $orderId = $request->order_id;
        
        $product = Product::findOrFail($productId);
        $order = Order::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->where('status', 'delivered')
            ->firstOrFail();
        
        // Check if user can review this product for this order
        if (!Comment::canUserComment(Auth::id(), $productId, $orderId)) {
            return redirect()->back()->with('error', 'Bạn không thể đánh giá sản phẩm này.');
        }
        
        return view('create-review', compact('product', 'order'));
    }
    
    /**
     * Store a new review
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }
        
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120' // Max 5MB per image
        ]);
        
        try {
            // Create comment with business rule validation
            $comment = Comment::createComment([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'order_id' => $request->order_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'is_verified' => 1,
                'is_hidden' => 0
            ]);
            
            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageName = 'comment_' . $comment->id . '_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('comment-images'), $imageName);
                    
                    CommentImage::create([
                        'comment_id' => $comment->id,
                        'image_path' => $imageName
                    ]);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Đánh giá của bạn đã được gửi thành công!',
                'redirect' => route('product.reviews', $request->product_id)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Get reviews for a product (API)
     */
    public function getReviews($productId)
    {
        $comments = Comment::where('product_id', $productId)
            ->verified()
            ->visible()
            ->with(['user', 'images', 'replies.admin'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $totalComments = Comment::where('product_id', $productId)->verified()->visible()->count();
        $averageRating = Comment::where('product_id', $productId)->verified()->visible()->avg('rating') ?? 0;
        
        return response()->json([
            'success' => true,
            'comments' => $comments,
            'total_comments' => $totalComments,
            'average_rating' => round($averageRating, 1)
        ]);
    }
}