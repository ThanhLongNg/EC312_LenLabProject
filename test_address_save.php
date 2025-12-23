<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Province;
use App\Models\Ward;
use App\Models\Address;
use App\Models\User;

echo "=== ADDRESS SAVING TEST ===\n\n";

// Check if we have provinces and wards
$provinceCount = Province::count();
$wardCount = Ward::count();

echo "Provinces: {$provinceCount}\n";
echo "Wards: {$wardCount}\n\n";

if ($provinceCount == 0 || $wardCount == 0) {
    echo "Error: Missing provinces or wards data!\n";
    exit(1);
}

// Get first province and its first ward
$province = Province::first();
$ward = Ward::where('province_id', $province->id)->first();

echo "Test province: {$province->name} (ID: {$province->id})\n";
echo "Test ward: {$ward->name} (ID: {$ward->id})\n\n";

// Get first user
$user = User::first();
if (!$user) {
    echo "Error: No users found!\n";
    exit(1);
}

echo "Test user: {$user->name} (ID: {$user->id})\n\n";

// Try to create a test address
try {
    $address = new Address([
        'user_id' => $user->id,
        'full_name' => 'Test User',
        'phone' => '0123456789',
        'province_id' => $province->id,
        'ward_id' => $ward->id,
        'specific_address' => 'Test address 123',
        'is_default' => false
    ]);
    
    echo "Address model created successfully!\n";
    echo "Fillable fields: " . implode(', ', $address->getFillable()) . "\n\n";
    
    // Try to save
    $saved = $address->save();
    
    if ($saved) {
        echo "✅ Address saved successfully!\n";
        echo "Address ID: {$address->id}\n";
        echo "Full address: {$address->getFullAddressAttribute()}\n";
        
        // Clean up - delete the test address
        $address->delete();
        echo "Test address cleaned up.\n";
    } else {
        echo "❌ Failed to save address\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error creating/saving address: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";