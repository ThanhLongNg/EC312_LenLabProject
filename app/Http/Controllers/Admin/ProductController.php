<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // 💚 1) Trang danh sách sản phẩm
    public function index()
    {
        $products = Product::with('variants')->get();
        return view('admin.products.index_simple', compact('products'));
    }

    // 💚 API load danh sách
    public function list()
    {
        $products = Product::with('variants')->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    // 💚 2) Form thêm sản phẩm
    public function create()
    {
        return view('admin.products.create');
    }

    // 💚 Form sửa sản phẩm
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.edit_simple', compact('product'));
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
            'image'      => "/storage/" . $imagePath
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    // 💚 4) Xóa sản phẩm
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return response()->json(['success' => true]);
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
                'status'     => $request->status
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
}
