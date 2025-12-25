<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ListingPageController extends Controller
{
    public function index()
    {
        return view('listing');
    }

    public function getProducts(Request $request)
    {
        $query = Product::query();

        // Tìm kiếm
        if ($request->keyword) {
            $query->where('name', 'LIKE', '%' . $request->keyword . '%');
        }

        $products = $query
            ->select('id', 'name', 'image', 'price', 'category', 'updated_at')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'products' => $products
        ]);
    }
}
