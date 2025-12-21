<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CheckoutController;

// ✅ ADMIN controllers
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;

// ✅ Site controllers
use App\Http\Controllers\ProductPageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReviewController;

Route::get('/', fn () => view('landingpage'));

// ---------------- USER PAGES ----------------
Route::get('/san-pham', [ProductPageController::class, 'index'])->name('products');
Route::get('/san-pham/{id}', [ProductPageController::class, 'show'])->name('product.detail');

Route::get('/gioi-thieu', fn () => view('intro'))->name('about');

Route::get('/cart', [CartController::class, 'show'])->name('cart');

Route::get('/vouchers', [App\Http\Controllers\VoucherController::class, 'index'])->name('vouchers');

// Dashboard (user) -> redirect home
Route::get('/dashboard', fn () => redirect('/'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
            }
            return view('profile', compact('user'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->name('profile');
    
    Route::get('/addresses', function() {
        return view('addresses');
    })->name('addresses.index');
    Route::get('/addresses/create', function() {
        return view('address-form');
    })->name('addresses.create');
    Route::get('/addresses/{id}/edit', function($id) {
        $address = \App\Models\Address::where('user_id', Auth::id())->findOrFail($id);
        return view('address-form', compact('address'));
    })->name('addresses.edit');
    
    // Profile edit routes
    Route::get('/profile/edit', function() {
        return view('profile-edit');
    })->name('profile.edit');
    Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Google Auth
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// ---------------- ADMIN AUTH ----------------
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// ---------------- ADMIN AREA ----------------
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('admin.customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/list', [OrderController::class, 'list'])->name('admin.orders.list');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('admin.orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('admin.orders.store');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('admin.orders.delete');
    Route::delete('/orders', [OrderController::class, 'bulkDelete'])->name('admin.orders.bulkDelete');
    Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::get('/products/{id}/price', [OrderController::class, 'productPrice'])->name('admin.product.price');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/list', [ProductController::class, 'list'])->name('admin.products.list');
    Route::get('/products/quick-search', [ProductController::class, 'quickSearch'])->name('admin.products.quickSearch');
    Route::get('/products/stats', [ProductController::class, 'getStats'])->name('admin.products.stats');
    Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::delete('/products/bulk-delete', [ProductController::class, 'bulkDelete'])->name('admin.products.bulkDelete');
    Route::delete('/products', [ProductController::class, 'bulkDelete'])->name('admin.products.bulkDelete');
    Route::post('/products/{id}/toggle-active', [ProductController::class, 'toggleActive'])->name('admin.products.toggleActive');
});

// ---------------- MARKETING AREA ----------------
Route::prefix('marketing')->middleware(['admin', 'admin.role:marketing'])->group(function () {
    Route::get('/dashboard', fn () => 'MARKETING DASHBOARD')->name('marketing.dashboard');
});

// Checkout routes
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout');
    Route::get('/checkout/confirm', [App\Http\Controllers\CheckoutController::class, 'confirm'])->name('checkout.confirm');
    Route::get('/checkout/payment', [App\Http\Controllers\CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::get('/checkout/bank-transfer', [App\Http\Controllers\CheckoutController::class, 'bankTransfer'])->name('checkout.bank-transfer');
    Route::get('/order-success', [App\Http\Controllers\CheckoutController::class, 'orderSuccess'])->name('order.success');
});

// Policy page
Route::get('/chinh-sach', function () {
    return view('policy');
})->name('policy');

// Order detail routes
Route::middleware('auth')->group(function () {
    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderId}', [App\Http\Controllers\OrderController::class, 'show'])->name('order.detail');
    Route::post('/orders/{orderId}/cancel', [App\Http\Controllers\OrderController::class, 'cancel'])->name('order.cancel');
});

// Public post detail
Route::get('/bai-viet/{id}', [PostController::class, 'show'])->name('post.show');

// API Routes for user-facing website
Route::prefix('api')->group(function () {
    Route::get('/products', [App\Http\Controllers\ProductPageController::class, 'apiIndex']);
    Route::get('/products/{id}/variants', [App\Http\Controllers\ProductPageController::class, 'getVariants']);
    Route::get('/landing/products', [App\Http\Controllers\ProductPageController::class, 'landingProducts']);
    Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'apiIndex']);
    Route::get('/categories/{id}/products', [App\Http\Controllers\CategoryController::class, 'getProductsByCategory']);
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index']);
    Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->middleware('web');
    Route::post('/cart/update', [App\Http\Controllers\CartController::class, 'updateQuantity'])->middleware('web');
    Route::post('/cart/delete', [App\Http\Controllers\CartController::class, 'delete'])->middleware('web');
    Route::post('/cart/voucher', [App\Http\Controllers\CartController::class, 'applyVoucher'])->middleware('web');
    
    // Voucher APIs
    Route::get('/vouchers', [App\Http\Controllers\VoucherController::class, 'getVouchers']);
    Route::post('/vouchers/apply', [App\Http\Controllers\VoucherController::class, 'applyVoucher'])->middleware('web');
    Route::post('/vouchers/remove', [App\Http\Controllers\VoucherController::class, 'removeVoucher'])->middleware('web');
    
    // Location APIs
    Route::get('/provinces', [App\Http\Controllers\LocationController::class, 'getProvinces']);
    Route::get('/provinces/{id}/wards', [App\Http\Controllers\LocationController::class, 'getWardsByProvince']);
    Route::get('/provinces/{slug}/wards', [App\Http\Controllers\LocationController::class, 'getWardsByProvinceSlug']);
    Route::get('/shipping-fee/{provinceId}', function($provinceId) {
        $province = \App\Models\Province::find($provinceId);
        if (!$province) {
            return response()->json(['success' => false, 'message' => 'Tỉnh không tồn tại']);
        }
        
        $shippingFee = \App\Helpers\ShippingHelper::calculateShippingFee($province->name);
        $zone = \App\Helpers\ShippingHelper::getZone($province->name);
        
        return response()->json([
            'success' => true,
            'shipping_fee' => $shippingFee,
            'zone' => $zone,
            'province_name' => $province->name
        ]);
    });
    
    // Address & Checkout APIs
    Route::middleware('auth')->group(function () {
        Route::get('/user/addresses', [App\Http\Controllers\AddressController::class, 'index']);
        Route::post('/user/addresses', [App\Http\Controllers\AddressController::class, 'store']);
        Route::put('/user/addresses/{id}', [App\Http\Controllers\AddressController::class, 'update']);
        Route::delete('/user/addresses/{id}', [App\Http\Controllers\AddressController::class, 'destroy']);
        Route::post('/user/addresses/{id}/default', [App\Http\Controllers\AddressController::class, 'setDefault']);
        
        Route::post('/checkout/set-selected-items', [App\Http\Controllers\CheckoutController::class, 'setSelectedItems']);
        Route::post('/checkout/set-address', [App\Http\Controllers\CheckoutController::class, 'setAddress']);
        Route::post('/checkout/set-note', [App\Http\Controllers\CheckoutController::class, 'setNote']);
        Route::post('/checkout/set-payment-method', [App\Http\Controllers\CheckoutController::class, 'setPaymentMethod']);
        Route::post('/checkout/prepare-bank-transfer', [App\Http\Controllers\CheckoutController::class, 'prepareBankTransfer']);
        Route::post('/checkout/complete-bank-transfer', [App\Http\Controllers\CheckoutController::class, 'completeBankTransfer']);
        Route::post('/checkout/create-order', [App\Http\Controllers\CheckoutController::class, 'createOrder']);
    });

    // Reviews
    Route::get('/reviews/{product_id}', [ReviewController::class, 'getReviews']);
    Route::post('/reviews', [ReviewController::class, 'submitReview'])->middleware('auth:api');
    Route::get('/products/related', [ReviewController::class, 'getRelatedProducts']);
});