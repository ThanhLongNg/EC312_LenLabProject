<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
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

    // API: Lấy danh sách review theo product_id
    public function getReviews($product_id)
    {
        $reviews = Review::where('product_id', $product_id)
            ->with('user:id,name')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($reviews);
    }

    // API: Gửi đánh giá
    public function submitReview(Request $request)
    {
        $request->validate([
            'product_id' => 'required|numeric',
            'rating'     => 'required|numeric|min:1|max:5',
            'comment'    => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        Review::create([
            'user_id'    => $user->id,
            'product_id' => $request->product_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        return response()->json(['success' => true]);
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
}
