<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$banners = \App\Models\Banner::where('is_active', 1)->get();

foreach ($banners as $banner) {
    if ($banner->image) {
        $url = asset('storage/' . $banner->image);
        echo "Banner {$banner->position}: {$url}" . PHP_EOL;
        
        // Check if file exists
        $filePath = public_path('storage/' . $banner->image);
        echo "File exists: " . (file_exists($filePath) ? 'yes' : 'no') . PHP_EOL;
        echo "File size: " . (file_exists($filePath) ? filesize($filePath) . ' bytes' : 'N/A') . PHP_EOL;
        echo "---" . PHP_EOL;
    }
}