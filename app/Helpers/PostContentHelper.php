<?php

namespace App\Helpers;

use App\Models\Product;

class PostContentHelper
{
    public static function render(string $content): string
    {
        return preg_replace_callback('/\[product:(\d+)\]/', function ($m) {
            $id = (int) $m[1];
            $p = Product::find($id);

            if (!$p) {
                return '<div class="my-4 p-4 rounded-xl border border-red-200 text-red-600">Sản phẩm không tồn tại</div>';
            }

            $detailUrl = route('product.detail', ['id' => $p->id]);

            $name = e($p->name);

            // ✅ Bạn đổi 2 field này theo bảng products của bạn
            $imgPath = $p->image ?? $p->thumbnail ?? null; // ví dụ
            $priceVal = $p->price ?? $p->price_sale ?? null;

            $img = $imgPath ? asset('storage/products/' . $imgPath) : '';
            $price = $priceVal ? number_format((float)$priceVal, 0, ',', '.') . 'đ' : '';

            $imgHtml = $img
                ? "<img src=\"{$img}\" alt=\"{$name}\" class=\"w-24 h-24 rounded-xl object-cover\" />"
                : "<div class=\"w-24 h-24 rounded-xl bg-gray-100\"></div>";

            return "
                <div class=\"my-5 p-4 rounded-2xl border border-gray-200 bg-white flex items-center gap-4\">
                    {$imgHtml}
                    <div class=\"flex-1\">
                        <div class=\"font-semibold text-gray-900\">{$name}</div>
                        <div class=\"text-sm text-gray-500 mt-1\">{$price}</div>

                        <div class=\"mt-3 flex flex-wrap gap-2\">
                            <a href=\"{$detailUrl}\" class=\"px-4 py-2 rounded-xl border border-gray-300 text-sm font-semibold\">
                                Xem sản phẩm
                            </a>

                            <button
                                type=\"button\"
                                class=\"js-add-to-cart px-4 py-2 rounded-xl bg-black text-white text-sm font-semibold\"
                                data-product-id=\"{$p->id}\"
                                data-qty=\"1\"
                            >
                                Thêm vào giỏ
                            </button>
                        </div>

                        <div class=\"js-cart-msg text-sm text-green-600 mt-2\" style=\"display:none;\"></div>
                    </div>
                </div>
            ";
        }, $content);
    }
}
