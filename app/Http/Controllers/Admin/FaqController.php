<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FaqController extends Controller
{
    /**
     * Hiển thị danh sách FAQ
     */
    public function index()
    {
        $faqs = FaqItem::byPriority()->paginate(20);
        $categories = FaqItem::getCategories();
        
        // For testing, use simple view first
        if (request()->has('test')) {
            return view('admin.faq.test', compact('faqs', 'categories'));
        }
        
        return view('admin.faq.index', compact('faqs', 'categories'));
    }

    /**
     * API: Lấy danh sách FAQ
     */
    public function list(Request $request): JsonResponse
    {
        $query = FaqItem::query();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%")
                  ->orWhere('keywords', 'like', "%{$search}%");
            });
        }

        $faqs = $query->byPriority()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $faqs
        ]);
    }

    /**
     * Hiển thị form tạo FAQ mới
     */
    public function create()
    {
        $categories = FaqItem::getCategories();
        return view('admin.faq.create', compact('categories'));
    }

    /**
     * Lưu FAQ mới
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'required|string|max:50',
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'keywords' => 'required|array|min:1',
            'keywords.*' => 'required|string|max:100',
            'priority' => 'nullable|integer|min:0|max:100',
            'is_active' => 'boolean'
        ]);

        try {
            $faq = FaqItem::create([
                'category' => $request->category,
                'question' => $request->question,
                'answer' => $request->answer,
                'keywords' => $request->keywords,
                'priority' => $request->priority ?? 0,
                'is_active' => $request->boolean('is_active', true)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo FAQ thành công!',
                'data' => $faq
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tạo FAQ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hiển thị form chỉnh sửa FAQ
     */
    public function edit($id)
    {
        $faq = FaqItem::findOrFail($id);
        
        // Return JSON for AJAX request
        if (request()->wantsJson()) {
            return response()->json($faq);
        }
        
        $categories = FaqItem::getCategories();
        return view('admin.faq.edit', compact('faq', 'categories'));
    }

    /**
     * Cập nhật FAQ
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'category' => 'required|string|max:50',
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'keywords' => 'required|array|min:1',
            'keywords.*' => 'required|string|max:100',
            'priority' => 'nullable|integer|min:0|max:100',
            'is_active' => 'boolean'
        ]);

        try {
            $faq = FaqItem::findOrFail($id);
            
            $faq->update([
                'category' => $request->category,
                'question' => $request->question,
                'answer' => $request->answer,
                'keywords' => $request->keywords,
                'priority' => $request->priority ?? 0,
                'is_active' => $request->boolean('is_active', true)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật FAQ thành công!',
                'data' => $faq
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi cập nhật FAQ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa FAQ
     */
    public function destroy($id): JsonResponse
    {
        try {
            $faq = FaqItem::findOrFail($id);
            $faq->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa FAQ thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xóa FAQ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa nhiều FAQ
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:faq_items,id'
        ]);

        try {
            FaqItem::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa ' . count($request->ids) . ' FAQ thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xóa FAQ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bật/tắt trạng thái FAQ
     */
    public function toggleActive($id): JsonResponse
    {
        try {
            $faq = FaqItem::findOrFail($id);
            $faq->update(['is_active' => !$faq->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công!',
                'is_active' => $faq->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi cập nhật trạng thái: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thống kê FAQ
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total' => FaqItem::count(),
                'active' => FaqItem::where('is_active', true)->count(),
                'inactive' => FaqItem::where('is_active', false)->count(),
                'by_category' => FaqItem::selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->pluck('count', 'category')
                    ->toArray(),
                'most_used' => FaqItem::orderBy('usage_count', 'desc')
                    ->limit(5)
                    ->get(['id', 'question', 'usage_count'])
            ];

            return response()->json([
                'success' => true,
                'statistics' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi lấy thống kê: ' . $e->getMessage()
            ], 500);
        }
    }
}
