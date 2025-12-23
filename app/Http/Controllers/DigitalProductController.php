<?php

namespace App\Http\Controllers;

use App\Models\DigitalProduct;
use App\Models\DigitalProductPurchase;
use App\Models\Comment;
use App\Models\CommentImage;
use Illuminate\Http\Request;

class DigitalProductController extends Controller
{
    /**
     * Display the digital products page
     */
    public function index(Request $request)
    {
        $query = DigitalProduct::where('is_active', true);
        
        // Filter by type if specified
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }
        
        // Sort by price or popularity
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->withCount('purchases')->orderBy('purchases_count', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
        
        $products = $query->paginate(12);
        
        return view('digital-products', compact('products'));
    }
    
    /**
     * Show a specific digital product
     */
    public function show($id)
    {
        $product = DigitalProduct::with('purchases')->findOrFail($id);
        
        // Get related products
        $relatedProducts = DigitalProduct::where('type', $product->type)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();
        
        return view('digital-product-detail', compact('product', 'relatedProducts'));
    }
    
    /**
     * Show digital order confirmation page
     */
    public function orderConfirm($id)
    {
        $product = DigitalProduct::findOrFail($id);
        
        // Generate unique order code
        $orderCode = 'ORD-' . strtoupper(substr(md5(uniqid()), 0, 4));
        
        return view('digital-order-confirm', compact('product', 'orderCode'));
    }
    
    /**
     * API endpoint for digital products
     */
    public function apiIndex(Request $request)
    {
        $query = DigitalProduct::where('is_active', true);
        
        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        // Search
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }
        
        // Pagination
        $perPage = $request->get('per_page', 12);
        $products = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'products' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'has_more' => $products->hasMorePages()
            ]
        ]);
    }
    
    /**
     * Show digital order success page
     */
    public function orderSuccess($id)
    {
        $purchase = DigitalProductPurchase::with('digitalProduct')->findOrFail($id);
        
        return view('digital-order-success', compact('purchase'));
    }
    
    /**
     * Process digital product order
     */
    public function processOrder(Request $request)
    {
        try {
            // Log incoming request for debugging
            \Log::info('Digital order request received', [
                'data' => $request->except(['transfer_image']),
                'has_file' => $request->hasFile('transfer_image')
            ]);

            $request->validate([
                'digital_product_id' => 'required|exists:digital_products,id',
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'required|email|max:255',
                'order_code' => 'required|string',
                'amount_paid' => 'required|numeric',
                'transfer_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Handle file upload
            $transferImagePath = null;
            if ($request->hasFile('transfer_image')) {
                $file = $request->file('transfer_image');
                $filename = 'transfer_' . $request->order_code . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('transfer-images'), $filename);
                $transferImagePath = 'transfer-images/' . $filename;
                \Log::info('File uploaded successfully', ['path' => $transferImagePath]);
            }

            // Get digital product for access days
            $digitalProduct = DigitalProduct::find($request->digital_product_id);
            
            // Create digital product purchase record
            $purchaseData = [
                'user_id' => auth()->id(), // Add user_id if user is logged in
                'digital_product_id' => $request->digital_product_id,
                'customer_email' => $request->customer_email,
                'customer_name' => $request->customer_name,
                'order_code' => $request->order_code,
                'amount_paid' => $request->amount_paid,
                'purchase_price' => $request->amount_paid,
                'purchased_at' => now(),
                'expires_at' => now()->addDays($digitalProduct->access_days),
                'downloads_count' => 0,
                'download_count' => 0,
                'email_sent' => false,
                'download_history' => [],
                'transfer_image' => $transferImagePath,
                'status' => 'pending'
            ];
            
            \Log::info('Creating purchase with data', $purchaseData);
            
            $purchase = DigitalProductPurchase::create($purchaseData);

            \Log::info('Purchase created successfully', ['id' => $purchase->id]);

            return response()->json([
                'success' => true,
                'message' => 'Đơn hàng đã được tạo thành công!',
                'order_id' => $purchase->id
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in digital order', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Digital order processing error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show user's digital orders
     */
    public function myOrders()
    {
        $purchases = DigitalProductPurchase::with('digitalProduct')
            ->where('user_id', auth()->id())
            ->orderBy('purchased_at', 'desc')
            ->paginate(10);
            
        return view('digital-orders', compact('purchases'));
    }
    
    /**
     * Show digital order detail
     */
    public function orderDetail($id)
    {
        $purchase = DigitalProductPurchase::with('digitalProduct')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
            
        return view('digital-order-detail', compact('purchase'));
    }
    
    /**
     * Show digital product reviews
     */
    public function reviews($id)
    {
        $digitalProduct = DigitalProduct::with(['verifiedComments.user', 'verifiedComments.images'])->findOrFail($id);
        
        $comments = $digitalProduct->verifiedComments()
                                  ->orderBy('created_at', 'desc')
                                  ->paginate(10);
        
        $averageRating = $digitalProduct->average_rating;
        $reviewCount = $digitalProduct->review_count;
        
        // Rating distribution
        $ratingDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingDistribution[$i] = $digitalProduct->verifiedComments()
                                                   ->where('rating', $i)
                                                   ->count();
        }
        
        return view('digital-product-reviews', compact('digitalProduct', 'comments', 'averageRating', 'reviewCount', 'ratingDistribution'));
    }
    
    /**
     * Show create review form for digital product
     */
    public function createReview($digitalProductId, $purchaseId)
    {
        $digitalProduct = DigitalProduct::findOrFail($digitalProductId);
        $purchase = DigitalProductPurchase::where('id', $purchaseId)
                                         ->where('user_id', auth()->id())
                                         ->where('digital_product_id', $digitalProductId)
                                         ->firstOrFail();
        
        // Check if user can comment
        if (!Comment::canUserCommentDigitalProduct(auth()->id(), $digitalProductId, $purchaseId)) {
            return redirect()->back()->with('error', 'Bạn không thể đánh giá sản phẩm này hoặc đã đánh giá rồi.');
        }
        
        return view('create-digital-review', compact('digitalProduct', 'purchase'));
    }
    
    /**
     * Store digital product review
     */
    public function storeReview(Request $request)
    {
        $request->validate([
            'digital_product_id' => 'required|exists:digital_products,id',
            'digital_purchase_id' => 'required|exists:digital_product_purchases,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // Create comment
            $comment = Comment::createComment([
                'user_id' => auth()->id(),
                'digital_product_id' => $request->digital_product_id,
                'digital_purchase_id' => $request->digital_purchase_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'is_verified' => true, // Auto-verify for digital products
                'is_hidden' => false
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = 'comment_' . $comment->id . '_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('comment-images'), $filename);
                    
                    CommentImage::create([
                        'comment_id' => $comment->id,
                        'image_path' => 'comment-images/' . $filename
                    ]);
                }
            }

            return redirect()->route('digital-products.reviews', $request->digital_product_id)
                           ->with('success', 'Đánh giá của bạn đã được gửi thành công!');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Get featured digital products for homepage
     */
    public function getFeatured()
    {
        $featured = DigitalProduct::where('is_active', true)
            ->where('price', 0) // Free products
            ->orWhere('created_at', '>=', now()->subDays(30)) // New products
            ->limit(6)
            ->get();
            
        return response()->json([
            'success' => true,
            'products' => $featured
        ]);
    }
}