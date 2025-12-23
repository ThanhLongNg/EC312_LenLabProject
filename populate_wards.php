<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Province;
use App\Models\Ward;

echo "Starting to populate wards data...\n";

// Read the JSON file
$jsonPath = 'database/data/provinces_wards.json';

if (!file_exists($jsonPath)) {
    echo "Error: provinces_wards.json file not found!\n";
    exit(1);
}

$data = json_decode(file_get_contents($jsonPath), true);

if (!$data) {
    echo "Error: Invalid JSON data!\n";
    exit(1);
}

// Clear existing wards
echo "Clearing existing wards...\n";
Ward::truncate();

$totalWards = 0;

foreach ($data as $provinceName => $wards) {
    // Find the province by name
    $province = Province::where('name', $provinceName)->first();
    
    if (!$province) {
        echo "Warning: Province not found: {$provinceName}\n";
        continue;
    }
    
    echo "Processing {$provinceName} (ID: {$province->id})...\n";
    
    $wardsData = [];
    foreach ($wards as $wardName) {
        $wardsData[] = [
            'name' => $wardName,
            'province_id' => $province->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
    // Insert wards in batches for better performance
    if (!empty($wardsData)) {
        Ward::insert($wardsData);
        $totalWards += count($wardsData);
        echo "Inserted " . count($wardsData) . " wards for {$provinceName}\n";
    }
}

echo "Successfully populated {$totalWards} wards!\n";
echo "Done!\n";