<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $status     = $request->get('status', 'all');      // all|pending|approved|hidden
        $rating     = $request->get('rating');             // 1..5
        $withImages = $request->boolean('with_images');    // 0/1
        $search     = $request->get('search');

        $query = Comment::query()
            ->with(['user', 'product', 'images'])
            ->orderByDesc('created_at');

        if ($status === 'pending')  $query->pending();
        if ($status === 'approved') $query->approved();
        if ($status === 'hidden')   $query->hidden();

        if ($rating) $query->withRating((int)$rating);

        if ($withImages) $query->withImages();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('product', fn($p) => $p->where('name', 'like', "%{$search}%"))
                  ->orWhere('comment', 'like', "%{$search}%");
            });
        }

        $reviews = $query->paginate(10)->withQueryString();

        // Counts cho tabs (đếm theo toàn bộ dữ liệu, không theo filter hiện tại)
        $counts = [
            'all'         => Comment::count(),
            'pending'     => Comment::pending()->count(),
            'approved'    => Comment::approved()->count(),
            'hidden'      => Comment::hidden()->count(),
            'five_star'   => Comment::withRating(5)->count(),
            'with_images' => Comment::withImages()->count(),
        ];

        return view('admin.reviews.index', [
            'reviews'        => $reviews,
            'counts'         => $counts,
            'currentStatus'  => $status,
            'currentRating'  => $rating ? (int)$rating : null,
            'currentSearch'  => $search ?? '',
        ]);
    }

    public function approve(Comment $comment)
    {
        // Duyệt = verified=1 và hiện = hidden=0
        $comment->update([
            'is_verified' => 1,
            'is_hidden'   => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã duyệt đánh giá.',
        ]);
    }

    public function hide(Comment $comment)
    {
        // Ẩn = hidden=1 (giữ is_verified tuỳ bạn)
        $comment->update([
            'is_hidden' => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã ẩn đánh giá.',
        ]);
    }
}
