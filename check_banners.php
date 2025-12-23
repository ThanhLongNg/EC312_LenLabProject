<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Banner count: " . \App\Models\Banner::count() . PHP_EOL;

$banners = \App\Models\Banner::all();
foreach ($banners as $banner) {
    echo "ID: {$banner->id}, Position: {$banner->position}, Image: " . ($banner->image ?? 'null') . ", Active: " . ($banner->is_active ? 'yes' : 'no') . ", Updated: {$banner->updated_at}" . PHP_EOL;
}

// Check if storage link exists
$storagePath = public_path('storage');
echo "Storage link exists: " . (is_link($storagePath) ? 'yes' : 'no') . PHP_EOL;

// Check if banner directory exists
$bannerDir = storage_path('app/public/uploads/banners');
echo "Banner directory exists: " . (is_dir($bannerDir) ? 'yes' : 'no') . PHP_EOL;

if (is_dir($bannerDir)) {
    $files = scandir($bannerDir);
    echo "Files in banner directory: " . implode(', ', array_filter($files, function($f) { return $f !== '.' && $f !== '..'; })) . PHP_EOL;
}