<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Province;

return new class extends Migration
{
    public function up(): void
    {
        // Read the JSON file
        $jsonPath = database_path('data/provinces_wards.json');
        
        if (!file_exists($jsonPath)) {
            echo "provinces_wards.json file not found!\n";
            return;
        }
        
        $data = json_decode(file_get_contents($jsonPath), true);
        
        if (!$data) {
            echo "Invalid JSON data!\n";
            return;
        }
        
        echo "Populating wards data...\n";
        
        foreach ($data as $provinceName => $wards) {
            // Find the province by name
            $province = Province::where('name', $provinceName)->first();
            
            if (!$province) {
                echo "Province not found: {$provinceName}\n";
                continue;
            }
            
            echo "Processing {$provinceName} (ID: {$province->id})...\n";
            
            foreach ($wards as $wardName) {
                DB::table('wards')->insert([
                    'name' => $wardName,
                    'province_id' => $province->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            echo "Inserted " . count($wards) . " wards for {$provinceName}\n";
        }
        
        echo "Wards population completed!\n";
    }

    public function down(): void
    {
        DB::table('wards')->truncate();
    }
};