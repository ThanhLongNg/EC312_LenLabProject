<?php
use App\Http\Controllers\IntroController;

Route::get('/intro/products', [IntroController::class, 'getProducts']);

use App\Http\Controllers\LandingPageController;

Route::get('/landing/products', [LandingPageController::class, 'getProducts']);

use App\Http\Controllers\ListingPageController;

Route::get('/products', [ListingPageController::class, 'getProducts']);

use App\Http\Controllers\ProductPageController;

Route::get('/products/{id}', [ProductPageController::class, 'getProduct']);

// Cart routes moved to web.php to handle authentication properly

use App\Http\Controllers\CheckoutController;

// Trang checkout
Route::get('/checkout', [CheckoutController::class, 'showCheckout'])->middleware('auth');

// Trang xem chi tiết đơn hàng (sau khi tạo xong)
Route::get('/order/{code}', [CheckoutController::class, 'showOrderDetail'])->name('order.detail');

use App\Http\Controllers\ReviewController;

Route::get('/reviews/{product_id}', [ReviewController::class, 'getReviews']);
Route::post('/reviews', [ReviewController::class, 'submitReview'])->middleware('auth:api');
Route::get('/products/related', [ReviewController::class, 'getRelatedProducts']);