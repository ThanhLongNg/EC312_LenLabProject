<?php

use App\Models\OrderItem;

if (!function_exists('order_item_img')) {
    function order_item_img($item) {
        if (!$item || empty($item->product_image)) return '';

        $img = $item->product_image;

        // full url
        if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) return $img;

        // already contains storage/
        if (str_starts_with($img, 'storage/')) return asset($img);

        // treat as storage path
        return asset('storage/'.$img);
    }
}
