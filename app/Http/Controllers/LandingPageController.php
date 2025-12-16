<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        return view('landingpage');
    }

    public function getProducts()
    {
        $products = Product::orderBy('created_at', 'desc')
            ->take(4)
            ->get(['id', 'name', 'image']);

        return response()->json([
            'products' => $products
        ]);
    }
}
