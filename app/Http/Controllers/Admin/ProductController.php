<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;

class ProductController extends BaseAdminController
{
    // 💚 1) Trang danh sách sản phẩm với search + filter + paginate
    public function index(Request $request)
    {
        $query = Product::query()->with('variants');

        /**
         * Backward-compatible inputs:
         * - HEAD dùng: keyword, category_id, is_active (giá trị trực tiếp)
         * - main dùng: search, category, status, is_active (all/1/0), new, sort_by, sort_order, per_page
         */
        $search = $request->filled('search')
            ? $request->search
            : ($request->filled('keyword') ? $request->keyword : null);

        $category = $request->filled('category')
            ? $request->category
            : ($request->filled('category_id') ? $request->category_id : null);

        // Search by name
        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Filter by category (main: 'all' | id)
        if (!empty($category) && $category !== 'all') {
            $query->where('category_id', $category);
        }

        // Filter by status (main: 'all' | value)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        /**
         * Filter by is_active
         * - main: is_active = 'all' | '1' | '0'
         * - HEAD: is_active = 1/0 (hoặc true/false)
         */
        if ($request->filled('is_active')) {
            if ($request->is_active !== 'all') {
                // Nếu là '1'/'0' thì convert bool, nếu là số/bool thì vẫn ok
                $isActive = ($request->is_active === '1' || $request->is_active === 1 || $request->is_active === true || $request->is_active === 'true');
                if ($request->is_active === '0' || $request->is_active === 0 || $request->is_active === false || $request->is_active === 'false') {
                    $isActive = false;
                }
                $query->where('is_active', $isActive);
            }
        }

        // Filter by new products (main: new = 'all' | '1' | '0')
        if ($request->filled('new') && $request->new !== 'all') {
            $query->where('new', $request->new == '1');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSorts = ['id', 'name', 'price', 'quantity', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('id', 'desc');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage)->withQueryString();

        // Categories for filter dropdown
        $categories = [
            1 => 'Nguyên phụ liệu',
            2 => 'Đồ trang trí',
            3 => 'Thời trang len',
            4 => 'Combo tự làm',
            5 => 'Sách hướng dẫn',
            6 => 'Thú bông len'
        ];

        return $this->view('admin.products.index_simple', compact('products', 'categories'));
    }

    // 💚 API load danh sách với search + filter + paginate
    public function list(Request $request)
    {
        $query = Product::with('variants');

        // Search by name or description
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by category
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by is_active
        if ($request->filled('is_active') && $request->is_active !== 'all') {
            $query->where('is_active', $request->is_active == '1');
        }

        // Filter by new products
        if ($request->filled('new') && $request->new !== 'all') {
            $query->where('new', $request->new == '1');
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Quantity filter
        if ($request->filled('min_quantity')) {
            $query->where('quantity', '>=', $request->min_quantity);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSorts = ['id', 'name', 'price', 'quantity', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('id', 'desc');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'has_more_pages' => $products->hasMorePages(),
                'prev_page_url' => $products->previousPageUrl(),
                'next_page_url' => $products->nextPageUrl()
            ],
            'filters' => [
                'search' => $request->search,
                'category' => $request->category,
                'status' => $request->status,
                'is_active' => $request->is_active,
                'new' => $request->new,
                'min_price' => $request->min_price,
                'max_price' => $request->max_price,
                'min_quantity' => $request->min_quantity,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'per_page' => $perPage
            ]
        ]);
    }

    // 💚 2) Form thêm sản phẩm
    public function create()
    {
        return $this->view('admin.products.create');
    }

    // 💚 Form sửa sản phẩm
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return $this->view('admin.products.edit_simple', compact('product'));
    }

    // 💚 3) Lưu sản phẩm
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required',
            'price'      => 'required|numeric|min:0',
            'quantity'   => 'required|numeric|min:0',
            'image'      => 'required|image',
            'status'     => 'required',
            'category_id' => 'required'
        ]);

        // Upload ảnh
        $imagePath = $request->file('image')->store('products', 'public');

        // Lưu sản phẩm
        $product = Product::create([
            'name'       => $request->name,
            'price'      => $request->price,
            'quantity'   => $request->quantity,
            'new'        => $request->new ? 1 : 0,
            'color'      => $request->color,
            'size'       => $request->size,
            'description' => $request->description,
            'status'     => $request->status,
            'category_id' => $request->category_id,
            'image'      => "/storage/" . $imagePath,
            'is_active'  => $request->has('is_active') ? 1 : 0
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    // 💚 4) Xóa sản phẩm
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    // 💚 Bulk delete - Xóa nhiều sản phẩm
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:products,id'
        ]);

        try {
            Product::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa ' . count($request->ids) . ' sản phẩm thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // 💚 Toggle active status
    public function toggleActive($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->is_active = !$product->is_active;
            $product->save();

            return response()->json([
                'success' => true,
                'is_active' => $product->is_active,
                'message' => $product->is_active ? 'Đã bật hiển thị sản phẩm' : 'Đã tắt hiển thị sản phẩm'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // 💚 5) Update sản phẩm
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name'       => 'required|string|max:255',
                'price'      => 'required|numeric|min:0',
                'quantity'   => 'required|numeric|min:0',
                'image'      => 'nullable|image|max:2048',
                'status'     => 'required|in:còn hàng,hết hàng',
                'category_id' => 'required|integer|between:1,6'
            ]);

            $product = Product::findOrFail($id);

            // Chuẩn bị data để update
            $updateData = [
                'name'       => $request->name,
                'price'      => $request->price,
                'quantity'   => $request->quantity,
                'color'      => $request->color,
                'size'       => $request->size,
                'new'        => $request->has('new') ? 1 : 0,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'status'     => $request->status,
                'is_active'  => $request->has('is_active') ? 1 : 0
            ];

            // Upload ảnh mới nếu có
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $updateData['image'] = "/storage/" . $imagePath;
            }

            $product->update($updateData);

            return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    // 💚 Quick search API (for autocomplete)
    public function quickSearch(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $products = Product::where('name', 'like', '%' . $query . '%')
            ->select('id', 'name', 'price', 'image')
            ->limit(10)
            ->get();

        return response()->json([
            'results' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'text' => $product->name,
                    'price' => number_format($product->price) . ' đ',
                    'image' => $product->image
                ];
            })
        ]);
    }

    // 💚 Get product statistics
    public function getStats()
    {
        $stats = [
            'total' => Product::count(),
            'active' => Product::where('is_active', true)->count(),
            'inactive' => Product::where('is_active', false)->count(),
            'in_stock' => Product::where('status', 'còn hàng')->count(),
            'out_of_stock' => Product::where('status', 'hết hàng')->count(),
            'new_products' => Product::where('new', 1)->count(),
            'low_stock' => Product::where('quantity', '<=', 5)->count(),
            'categories' => Product::selectRaw('category_id, COUNT(*) as count')
                ->groupBy('category_id')
                ->get()
                ->pluck('count', 'category_id')
        ];

        return response()->json($stats);
    }
}
