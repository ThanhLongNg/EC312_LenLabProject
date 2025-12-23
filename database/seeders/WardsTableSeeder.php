<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Province;
use App\Models\Ward;

class WardsTableSeeder extends Seeder
{
    public function run()
    {
        // Read the JSON file
        $jsonPath = database_path('data/provinces_wards.json');
        
        if (!file_exists($jsonPath)) {
            $this->command->error('provinces_wards.json file not found!');
            return;
        }
        
        $data = json_decode(file_get_contents($jsonPath), true);
        
        if (!$data) {
            $this->command->error('Invalid JSON data!');
            return;
        }
        
        $this->command->info('Starting to seed wards data...');
        
        // Clear existing wards
        Ward::truncate();
        
        $totalWards = 0;
        
        foreach ($data as $provinceName => $wards) {
            // Find the province by name
            $province = Province::where('name', $provinceName)->first();
            
            if (!$province) {
                $this->command->warn("Province not found: {$provinceName}");
                continue;
            }
            
            $this->command->info("Processing {$provinceName} (ID: {$province->id})...");
            
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
                $this->command->info("Inserted " . count($wardsData) . " wards for {$provinceName}");
            }
        }
        
        $this->command->info("Successfully seeded {$totalWards} wards!");
    }
}