<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== HOMEPAGE PERFORMANCE TEST ===\n\n";

// Test 1: LandingPageController performance
echo "1. Testing LandingPageController::index()\n";
$start = microtime(true);

$controller = new \App\Http\Controllers\LandingPageController();
$response = $controller->index();

$end = microtime(true);
$time = ($end - $start) * 1000;
echo "Controller execution time: " . round($time, 2) . "ms\n\n";

// Test 2: API endpoints performance
echo "2. Testing API endpoints:\n";

// Categories API
$start = microtime(true);
$categoryController = new \App\Http\Controllers\CategoryController();
$categoryResponse = $categoryController->apiIndex();
$end = microtime(true);
$categoryTime = ($end - $start) * 1000;
echo "Categories API: " . round($categoryTime, 2) . "ms\n";

// Products API
$start = microtime(true);
$productController = new \App\Http\Controllers\ProductPageController();
$productResponse = $productController->landingProducts();
$end = microtime(true);
$productTime = ($end - $start) * 1000;
echo "Products API: " . round($productTime, 2) . "ms\n\n";

// Test 3: Database queries
echo "3. Database query performance:\n";

// Banner queries
$start = microtime(true);
$heroBanner = \App\Models\Banner::where('position', 'home')
    ->where('is_active', 1)
    ->select(['id', 'image', 'link', 'updated_at'])
    ->first();
$end = microtime(true);
$bannerTime = ($end - $start) * 1000;
echo "Banner query: " . round($bannerTime, 2) . "ms\n";

// Posts query
$start = microtime(true);
$latestPosts = \App\Models\Post::where('is_published', true)
    ->orderByDesc('published_at')
    ->select(['id', 'title', 'slug', 'excerpt', 'thumbnail', 'category', 'published_at'])
    ->take(6)
    ->get();
$end = microtime(true);
$postsTime = ($end - $start) * 1000;
echo "Posts query: " . round($postsTime, 2) . "ms\n";

// Products query
$start = microtime(true);
$products = \App\Models\Product::where('status', 1)
    ->select(['id', 'name', 'price', 'image', 'category_id', 'category'])
    ->orderBy('id', 'desc')
    ->take(8)
    ->get();
$end = microtime(true);
$productsQueryTime = ($end - $start) * 1000;
echo "Products query: " . round($productsQueryTime, 2) . "ms\n\n";

// Summary
$totalTime = $time + $categoryTime + $productTime + $bannerTime + $postsTime + $productsQueryTime;
echo "=== PERFORMANCE SUMMARY ===\n";
echo "Total estimated load time: " . round($totalTime, 2) . "ms\n";

if ($totalTime < 500) {
    echo "✅ EXCELLENT: Homepage should load very fast\n";
} elseif ($totalTime < 1000) {
    echo "✅ GOOD: Homepage should load reasonably fast\n";
} elseif ($totalTime < 2000) {
    echo "⚠️  MODERATE: Homepage might feel a bit slow\n";
} else {
    echo "❌ SLOW: Homepage needs further optimization\n";
}

echo "\n=== RECOMMENDATIONS ===\n";
if ($bannerTime > 50) {
    echo "- Consider adding database index on banners(position, is_active)\n";
}
if ($postsTime > 100) {
    echo "- Consider adding database index on posts(is_published, published_at)\n";
}
if ($productsQueryTime > 100) {
    echo "- Consider adding database index on products(status, id)\n";
}
echo "- Enable browser caching for static assets\n";
echo "- Consider using Redis cache for frequently accessed data\n";
echo "- Optimize images with WebP format\n";