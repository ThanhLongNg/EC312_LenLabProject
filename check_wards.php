<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Province;
use App\Models\Ward;

echo "=== WARDS TABLE DATA CHECK ===\n\n";

// Check total count
$totalWards = Ward::count();
echo "Total wards in database: {$totalWards}\n\n";

// Check first 5 wards
echo "First 5 wards:\n";
$firstWards = Ward::with('province')->limit(5)->get();
foreach ($firstWards as $ward) {
    $provinceName = $ward->province ? $ward->province->name : 'Unknown Province';
    echo "- ID: {$ward->id}, Name: {$ward->name}, Province: {$provinceName}\n";
}

echo "\n";

// Check wards by province (first 3 provinces)
echo "Wards by province (first 3 provinces):\n";
$provinces = Province::limit(3)->get();
foreach ($provinces as $province) {
    $wardCount = Ward::where('province_id', $province->id)->count();
    echo "- {$province->name} (ID: {$province->id}): {$wardCount} wards\n";
    
    // Show first 3 wards of this province
    $sampleWards = Ward::where('province_id', $province->id)->limit(3)->get();
    foreach ($sampleWards as $ward) {
        echo "  * {$ward->name}\n";
    }
    echo "\n";
}

echo "=== CHECK COMPLETED ===\n";