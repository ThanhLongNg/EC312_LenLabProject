<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Fix banner with invalid URL
$banner = \App\Models\Banner::where('position', 'products')->first();
if ($banner && strpos($banner->image, 'https://') === 0) {
    echo "Found banner with external URL: {$banner->image}" . PHP_EOL;
    
    // Set to null so it will use fallback
    $banner->image = null;
    $banner->save();
    
    echo "Fixed banner - set image to null" . PHP_EOL;
}

// Check all banners
echo "\nAll banners after fix:" . PHP_EOL;
$banners = \App\Models\Banner::all();
foreach ($banners as $banner) {
    echo "ID: {$banner->id}, Position: {$banner->position}, Image: " . ($banner->image ?? 'null') . ", Active: " . ($banner->is_active ? 'yes' : 'no') . PHP_EOL;
}