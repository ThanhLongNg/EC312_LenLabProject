<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('landingpage');
});

// Routes cho user
Route::get('/san-pham', [App\Http\Controllers\ProductPageController::class, 'index'])->name('products');
Route::get('/san-pham/{id}', [App\Http\Controllers\ProductPageController::class, 'show'])->name('product.detail');

Route::get('/gioi-thieu', function () {
    return view('intro');
})->name('about');

Route::get('/gio-hang', [App\Http\Controllers\CartController::class, 'show'])->name('cart');

Route::get('/dashboard', function () {
    // Redirect user về trang chủ thay vì dashboard
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

// Google Auth
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Admin logout
Route::post('/admin/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');

// Admin Dashboard
Route::middleware(['admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
});

// Admin Customers
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('admin.customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');
});

// Admin Orders & Products
// API Routes for user-facing website
Route::prefix('api')->group(function () {
    Route::get('/products', [App\Http\Controllers\ProductPageController::class, 'apiIndex']);
    Route::get('/landing/products', [App\Http\Controllers\ProductPageController::class, 'landingProducts']);
    Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'apiIndex']);
    Route::get('/categories/{id}/products', [App\Http\Controllers\CategoryController::class, 'getProductsByCategory']);
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index']);
    Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->middleware('web');
    Route::post('/cart/update', [App\Http\Controllers\CartController::class, 'updateQuantity'])->middleware('web');
    Route::post('/cart/delete', [App\Http\Controllers\CartController::class, 'delete'])->middleware('web');
    Route::post('/cart/voucher', [App\Http\Controllers\CartController::class, 'applyVoucher'])->middleware('web');
});

Route::prefix('admin')->middleware('admin')->group(function () {
    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/list', [OrderController::class, 'list'])->name('admin.orders.list');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('admin.orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('admin.orders.store');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('admin.orders.delete');
    Route::delete('/orders', [OrderController::class, 'bulkDelete'])->name('admin.orders.bulkDelete');
    Route::get('/products/{id}/price', [OrderController::class, 'productPrice'])->name('admin.product.price');
    Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/list', [ProductController::class, 'list'])->name('admin.products.list');
    Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
});
