<?php

use Illuminate\Support\Facades\Route;
use App\Models\Province;
use App\Models\Ward;

// Test route to check provinces and wards data
Route::get('/test-location-data', function() {
    $provincesCount = Province::count();
    $wardsCount = Ward::count();
    
    $sampleProvinces = Province::take(5)->get(['id', 'name']);
    $sampleWards = Ward::with('province')->take(5)->get(['id', 'name', 'province_id']);
    
    return response()->json([
        'provinces_count' => $provincesCount,
        'wards_count' => $wardsCount,
        'sample_provinces' => $sampleProvinces,
        'sample_wards' => $sampleWards
    ]);
});

// Test route to simulate address validation
Route::post('/test-address-validation', function(\Illuminate\Http\Request $request) {
    try {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province_id' => 'required|integer|min:1',
            'ward_id' => 'required|integer|min:1',
            'specific_address' => 'required|string|max:500',
            'save_address' => 'sometimes|boolean',
            'selected_address_id' => 'nullable|integer|min:1'
        ]);
        
        return response()->json([
            'success' => true,
            'validated_data' => $validated,
            'message' => 'Validation passed'
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors(),
            'message' => 'Validation failed'
        ], 422);
    }
});