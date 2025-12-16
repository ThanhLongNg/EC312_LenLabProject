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
        $query = Product::query();
        
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
        
        return view('listing', compact('products'));
    }
    
    // API cho danh sách sản phẩm
    public function apiIndex(Request $request)
    {
        try {
            $query = Product::query();
            
            // Tìm kiếm theo từ khóa
            if ($request->has('keyword') && $request->keyword) {
                $query->where('name', 'like', '%' . $request->keyword . '%');
                
                // Kiểm tra xem có cột category không
                if (\Schema::hasColumn('products', 'category')) {
                    $query->orWhere('category', 'like', '%' . $request->keyword . '%');
                }
            }
            
            $products = $query->get()->map(function($product) {
                // Lấy category từ cột category hoặc từ quan hệ category_id
                $category = 'Chưa phân loại';
                if (isset($product->category) && $product->category) {
                    $category = $product->category;
                } elseif (isset($product->category_id)) {
                    // Map category_id to category name
                    $categoryMap = [
                        1 => 'Nguyên phụ liệu',
                        2 => 'Đồ trang trí', 
                        3 => 'Thời trang len',
                        4 => 'Combo tự làm',
                        5 => 'Sách hướng dẫn',
                        6 => 'Thú bông len'
                    ];
                    $category = $categoryMap[$product->category_id] ?? 'Chưa phân loại';
                }
                
                return [
                    'id' => $product->id,
                    'name' => $product->name ?? 'Sản phẩm',
                    'price' => (float) ($product->price ?? 0),
                    'category' => $category,
                    'image' => $product->image ?? 'default.jpg',
                    'is_new' => isset($product->new) && ($product->new == 1 || $product->new == '1')
                ];
            });
            
            return response()->json([
                'products' => $products
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'products' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
    
    // API cho sản phẩm trang chủ
    public function landingProducts()
    {
        try {
            $products = Product::take(6)->get()->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name ?? 'Sản phẩm',
                    'image' => $product->image ?? 'default.jpg'
                ];
            });
            
            return response()->json([
                'products' => $products
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'products' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
    
    // Chi tiết sản phẩm
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('product', compact('product'));
    }
}