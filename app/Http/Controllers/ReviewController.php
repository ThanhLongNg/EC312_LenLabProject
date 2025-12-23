<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Product;
use App\Models\DigitalProduct;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // Hiển thị trang comment
    public function showPage()
    {
        return view('comment');
    }
    
    // Hiển thị trang đánh giá sản phẩm
    public function show($id)
    {
        $product = Product::findOrFail($id);
        
        $comments = Comment::where('product_id', $id)
            ->where('is_verified', true)
            ->where('is_hidden', false)
            ->with('user:id,name', 'images', 'replies.admin:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $averageRating = Comment::where('product_id', $id)
            ->where('is_verified', true)
            ->where('is_hidden', false)
            ->avg('rating') ?? 0;
            
        $totalComments = Comment::where('product_id', $id)
            ->where('is_verified', true)
            ->where('is_hidden', false)
            ->count();
            
        // Calculate rating statistics
        $ratingStats = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = Comment::where('product_id', $id)
                ->where('is_verified', true)
                ->where('is_hidden', false)
                ->where('rating', $i)
                ->count();
            $ratingStats[$i] = [
                'count' => $count,
                'percentage' => $totalComments > 0 ? ($count / $totalComments) * 100 : 0
            ];
        }
        
        // Check if user can review and get eligible orders
        $canReview = false;
        $eligibleOrders = collect();
        
        if (auth()->check()) {
            $eligibleOrders = Comment::getUserEligibleOrders(auth()->id(), $id);
            $canReview = $eligibleOrders->isNotEmpty();
        }
        
        return view('product-reviews', compact('product', 'comments', 'averageRating', 'totalComments', 'ratingStats', 'canReview', 'eligibleOrders'));
    }

    // API: Lấy danh sách review theo product_id
    public function getReviews($product_id)
    {
        $reviews = Comment::where('product_id', $product_id)
            ->where('is_verified', true)
            ->where('is_hidden', false)
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reviews);
    }

    // API: Gửi đánh giá
    public function submitReview(Request $request)
    {
        $request->validate([
            'product_id' => 'required|numeric',
            'order_id' => 'required|string',
            'rating'     => 'required|numeric|min:1|max:5',
            'comment'    => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        try {
            Comment::createComment([
                'user_id'    => $user->id,
                'product_id' => $request->product_id,
                'order_id'   => $request->order_id,
                'rating'     => $request->rating,
                'comment'    => $request->comment,
                'is_verified' => false, // Cần admin duyệt
                'is_hidden'   => false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đánh giá của bạn đã được gửi và đang chờ duyệt.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // API: lấy 4 sản phẩm liên quan
    public function getRelatedProducts()
    {
        $products = Product::select('id', 'name', 'image')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        return response()->json([
            'products' => $products
        ]);
    }
    
    // Hiển thị trang đánh giá sản phẩm số
    public function showDigital($id)
    {
        $digitalProduct = DigitalProduct::findOrFail($id);
        
        $comments = Comment::where('digital_product_id', $id)
            ->where('is_verified', true)
            ->where('is_hidden', false)
            ->with('user:id,name', 'images', 'replies.admin:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $averageRating = Comment::where('digital_product_id', $id)
            ->where('is_verified', true)
            ->where('is_hidden', false)
            ->avg('rating') ?? 0;
            
        $totalComments = Comment::where('digital_product_id', $id)
            ->where('is_verified', true)
            ->where('is_hidden', false)
            ->count();
            
        // Calculate rating statistics
        $ratingStats = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = Comment::where('digital_product_id', $id)
                ->where('is_verified', true)
                ->where('is_hidden', false)
                ->where('rating', $i)
                ->count();
            $ratingStats[$i] = [
                'count' => $count,
                'percentage' => $totalComments > 0 ? ($count / $totalComments) * 100 : 0
            ];
        }
        
        // Check if user can review and get eligible purchases
        $canReview = false;
        $eligiblePurchases = collect();
        
        if (auth()->check()) {
            $eligiblePurchases = Comment::getUserEligibleDigitalPurchases(auth()->id(), $id);
            $canReview = $eligiblePurchases->isNotEmpty();
        }
        
        return view('digital-product-reviews', compact('digitalProduct', 'comments', 'averageRating', 'totalComments', 'ratingStats', 'canReview', 'eligiblePurchases'))->with(['reviewCount' => $totalComments, 'ratingDistribution' => $ratingStats]);
    }

    // API: Gửi đánh giá sản phẩm số
    public function submitDigitalReview(Request $request)
    {
        $request->validate([
            'digital_product_id' => 'required|numeric',
            'digital_purchase_id' => 'required|numeric',
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        try {
            Comment::createComment([
                'user_id' => $user->id,
                'digital_product_id' => $request->digital_product_id,
                'digital_purchase_id' => $request->digital_purchase_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'is_verified' => false, // Cần admin duyệt
                'is_hidden' => false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đánh giá của bạn đã được gửi và đang chờ duyệt.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
