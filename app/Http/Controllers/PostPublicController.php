<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;

class PostPublicController extends Controller
{
    public function index()
    {
        $posts = Post::where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('blog.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        // Extract product IDs from shortcodes to show as related products
        $relatedProducts = $this->extractRelatedProducts($post->content ?? '');
        
        // Remove shortcodes from content completely, don't render them
        $contentHtml = preg_replace('/\[product:\d+\]/', '', $post->content ?? '');

        return view('blog.show', compact('post', 'contentHtml', 'relatedProducts'));
    }
    
    private function extractRelatedProducts(string $content): array
    {
        $productIds = [];
        preg_match_all('/\[product:(\d+)\]/', $content, $matches);
        
        if (!empty($matches[1])) {
            $productIds = array_unique(array_map('intval', $matches[1]));
            return Product::whereIn('id', $productIds)
                ->where('status', 1)
                ->take(5) // Limit to 5 products
                ->get()
                ->toArray();
        }
        
        return [];
    }

    private function renderProductShortcodes(string $content): string
    {
        return preg_replace_callback('/\[product:(\d+)\]/', function ($m) {
            $id = (int) $m[1];

            $product = Product::find($id);
            if (!$product) {
                return '<div class="my-4 p-4 rounded-xl border border-red-200 bg-red-50 text-red-700">
                            Sản phẩm #' . $id . ' không tồn tại.
                        </div>';
            }

            $img = $product->image ? asset('storage/products/' . $product->image) : asset('placeholder.png');
            $price = $product->price ? number_format($product->price, 0, ',', '.') . '₫' : 'Liên hệ';

            return '
                <div class="my-6 p-4 rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex gap-4 items-start">
                        <img src="'.$img.'" class="w-28 h-28 rounded-xl object-cover border" alt="'.e($product->name).'">
                        <div class="flex-1">
                            <div class="font-bold text-lg">'.e($product->name).'</div>
                            <div class="text-primary font-semibold mt-1">'.$price.'</div>

                            <div class="flex gap-2 mt-3 flex-wrap">
                                <a href="/san-pham/'.$product->id.'"
                                   class="px-4 py-2 rounded-xl border font-semibold hover:bg-gray-50">
                                    Xem sản phẩm
                                </a>

                                <button type="button"
                                        class="px-4 py-2 rounded-xl bg-black text-white font-semibold js-add-to-cart"
                                        data-product-id="'.$product->id.'">
                                    Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            ';
        }, $content);
    }
}
