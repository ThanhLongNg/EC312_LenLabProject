<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProvinceWardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Read the JSON file
        $jsonPath = database_path('data/provinces_wards.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error('provinces_wards.json file not found!');
            return;
        }

        $data = json_decode(File::get($jsonPath), true);
        
        if (!$data) {
            $this->command->error('Invalid JSON data!');
            return;
        }

        $this->command->info('Importing provinces and wards...');

        foreach ($data as $provinceName => $wards) {
            // Create province
            $province = Province::create([
                'name' => $provinceName
            ]);

            $this->command->info("Created province: {$provinceName}");

            // Create wards for this province
            foreach ($wards as $wardName) {
                Ward::create([
                    'province_id' => $province->id,
                    'name' => $wardName
                ]);
            }

            $this->command->info("Created " . count($wards) . " wards for {$provinceName}");
        }

        $this->command->info('Province and ward import completed!');
    }
}