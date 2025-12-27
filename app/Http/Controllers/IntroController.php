<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class IntroController extends Controller
{
    public function index()
    {
        return view('intro');
    }

    public function getProducts()
    {
        $products = Product::orderBy('created_at', 'desc')
            ->take(4)
            ->get(['id', 'name', 'image', 'updated_at', 'price']);

        return response()->json([
            'products' => $products
        ]);
    }
}
