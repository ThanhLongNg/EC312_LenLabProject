<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Banner;

class LandingPageController extends Controller
{
    public function index()
    {
        $heroBanner = Banner::where('position', 'home')->where('is_active', 1)->first();
        $campaignBanner = Banner::where('position', 'campaign')->where('is_active', 1)->first();

        return view('landingpage', compact('heroBanner', 'campaignBanner'));
    }

    public function getProducts()
    {
        $products = Product::where('is_active', true)
            ->orderBy('id', 'desc')
            ->take(4)
            ->get(['id', 'name', 'image', 'price'])
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->image,
                    'price' => $product->price,
                    'updated_at' => time() // Sử dụng timestamp hiện tại
                ];
            });

        return response()->json([
            'products' => $products
        ]);
    }
    public function home()
{
    $heroBanner = Banner::where('position', 'home')->where('is_active', 1)->first();
    $campaignBanner = Banner::where('position', 'campaign')->where('is_active', 1)->first();

    return view('home', compact('heroBanner', 'campaignBanner'));
}
}
