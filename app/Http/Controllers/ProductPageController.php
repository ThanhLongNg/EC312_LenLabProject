<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;

class ProductPageController extends Controller
{
    // Trang danh sách sản phẩm
    public function index(Request $request)
    {
        $query = Product::active(); // Chỉ lấy sản phẩm active
        
        // Lọc theo danh mục
        if ($request->has('type')) {
            $categoryMap = [
                'nguyen-lieu' => 1,
                'trang-tri' => 2,
                'thoi-trang' => 3,
                'combo' => 4,
                'sach' => 5,
                'thu-bong' => 6,
            ];
            
            if (isset($categoryMap[$request->type])) {
                $query->where('category_id', $categoryMap[$request->type]);
            }
        }
        
        // Tìm kiếm
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $products = $query->paginate(12);
        
        return view('products', compact('products'));
    }
    
    // API cho danh sách sản phẩm
    public function apiIndex(Request $request)
    {
        try {
            $query = Product::where('is_active', true);
            
            // Filter theo category
            if ($request->has('category') && $request->category && $request->category !== 'all') {
                $query->where('category_id', $request->category);
            }
            
            // Tìm kiếm theo từ khóa
            if ($request->has('keyword') && $request->keyword) {
                $query->where('name', 'like', '%' . $request->keyword . '%');
            }
            
            // Sorting
            if ($request->has('sort')) {
                switch($request->sort) {
                    case 'price-asc':
                        $query->orderBy('price', 'asc');
                        break;
                    case 'price-desc':
                        $query->orderBy('price', 'desc');
                        break;
                    case 'name-asc':
                        $query->orderBy('name', 'asc');
                        break;
                    case 'name-desc':
                        $query->orderBy('name', 'desc');
                        break;
                    default:
                        $query->orderBy('id', 'desc');
                        break;
                }
            } else {
                $query->orderBy('id', 'desc');
            }
            
            $products = $query->get();
            
            $mappedProducts = $products->map(function($product) {
                // Map category_id to category name
                $categoryMap = [
                    1 => 'Nguyên phụ liệu',
                    2 => 'Đồ trang trí', 
                    3 => 'Thời trang len',
                    4 => 'Combo tự làm',
                    5 => 'Sách hướng dẫn móc len',
                    6 => 'Thú bông len'
                ];
                
                $category = 'Chưa phân loại';
                if (isset($product->category) && $product->category) {
                    $category = $product->category;
                } elseif (isset($product->category_id)) {
                    $category = $categoryMap[$product->category_id] ?? 'Chưa phân loại';
                }
                
                return [
                    'id' => $product->id,
                    'name' => $product->name ?? 'Sản phẩm',
                    'price' => (float) ($product->price ?? 0),
                    'category' => $category,
                    'image' => $product->image ?? 'default.jpg',
                    'updated_at' => time(),
                    'is_new' => isset($product->new) && ($product->new == 1 || $product->new == '1'),
                    'quantity' => $product->quantity ?? 0
                ];
            });
            
            return response()->json([
                'products' => $mappedProducts
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'products' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // API cho sản phẩm trang chủ
    public function landingProducts()
    {
        try {
            $products = Product::active() // Chỉ lấy sản phẩm active
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->take(8)
                ->get()
                ->map(function($product) {
                    // Map category_id to category name
                    $categoryMap = [
                        1 => 'Nguyên phụ liệu',
                        2 => 'Đồ trang trí', 
                        3 => 'Thời trang len',
                        4 => 'Combo tự làm',
                        5 => 'Sách hướng dẫn móc len',
                        6 => 'Thú bông len'
                    ];
                    
                    $category = 'Chưa phân loại';
                    if (isset($product->category) && $product->category) {
                        $category = $product->category;
                    } elseif (isset($product->category_id)) {
                        $category = $categoryMap[$product->category_id] ?? 'Chưa phân loại';
                    }
                    
                    return [
                        'id' => $product->id,
                        'name' => $product->name ?? 'Sản phẩm',
                        'price' => (float) ($product->price ?? 0),
                        'image' => $product->image ?? null,
                        'category' => $category,
                        'description' => $product->description ?? '',
                        'is_new' => isset($product->new) && ($product->new == 1 || $product->new == '1'),
                        'quantity' => $product->quantity ?? 0
                    ];
                });
            
            return response()->json([
                'products' => $products,
                'success' => true
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'products' => [],
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    // Chi tiết sản phẩm
    public function show($id)
    {
        $product = Product::active()->with('variants')->findOrFail($id); // Chỉ cho phép xem sản phẩm active
        
        // Lấy thông tin variants
        $availableVariants = $product->getAvailableVariants();
        $variantsWithId = $product->variants()->whereNotNull('variant_name')->get(['id', 'variant_name']);
        $hasVariants = $product->hasVariants();
        
        // Lấy thông tin hình ảnh
        $productImages = $product->getAllImages();
        $hasMultipleImages = $product->hasMultipleImages();
        
        // Lấy thông tin đánh giá
        $averageRating = \App\Models\Comment::where('product_id', $id)
            ->where('is_verified', 1)
            ->where('is_hidden', 0)
            ->avg('rating') ?? 0;
        
        $totalComments = \App\Models\Comment::where('product_id', $id)
            ->where('is_verified', 1)
            ->where('is_hidden', 0)
            ->count();
        
        return view('product', compact(
            'product', 
            'availableVariants', 
            'variantsWithId',
            'hasVariants',
            'productImages',
            'hasMultipleImages',
            'averageRating',
            'totalComments'
        ));
    }
    
    /**
     * Show product reviews
     */
    public function reviews($id)
    {
        $product = Product::active()->findOrFail($id);
        
        $comments = \App\Models\Comment::where('product_id', $id)
            ->where('is_verified', 1)
            ->where('is_hidden', 0)
            ->with(['user', 'images', 'replies'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $averageRating = \App\Models\Comment::where('product_id', $id)
            ->where('is_verified', 1)
            ->where('is_hidden', 0)
            ->avg('rating') ?? 0;
        
        $reviewCount = \App\Models\Comment::where('product_id', $id)
            ->where('is_verified', 1)
            ->where('is_hidden', 0)
            ->count();
        
        // Rating distribution
        $ratingDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingDistribution[$i] = \App\Models\Comment::where('product_id', $id)
                                                       ->where('is_verified', 1)
                                                       ->where('is_hidden', 0)
                                                       ->where('rating', $i)
                                                       ->count();
        }
        
        return view('product-reviews', compact('product', 'comments', 'averageRating', 'reviewCount', 'ratingDistribution'));
    }
    
    // API để lấy danh sách categories
    public function getCategories()
    {
        try {
            $categories = [
                ['id' => 1, 'name' => 'Nguyên phụ liệu'],
                ['id' => 2, 'name' => 'Đồ trang trí'],
                ['id' => 3, 'name' => 'Thời trang len'],
                ['id' => 4, 'name' => 'Combo tự làm'],
                ['id' => 5, 'name' => 'Sách hướng dẫn móc len'],
                ['id' => 6, 'name' => 'Thú bông len']
            ];

            return response()->json([
                'categories' => $categories,
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'categories' => [],
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    // API để lấy thông tin variants của sản phẩm
    public function getVariants($id)
    {
        try {
            $product = Product::with('variants')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'has_variants' => $product->variants->count() > 0
                ],
                'variants' => $product->variants->map(function($variant) {
                    return [
                        'id' => $variant->id,
                        'variant_name' => $variant->variant_name
                    ];
                }),
                'product_images' => $product->getAllImages(),
                'has_multiple_images' => $product->hasMultipleImages(),
                'available_variants' => $product->getAvailableVariants(),
                'has_variants' => $product->hasVariants()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }
}