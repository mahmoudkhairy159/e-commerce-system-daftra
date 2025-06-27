<?php

namespace Modules\Area\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Area\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path('data/countries.json');
        $batchSize = 100; // Adjust this to a suitable batch size

        // Read the file and decode the JSON
        $countries = json_decode(file_get_contents($filePath), true);

        collect($countries)
            ->chunk($batchSize)
            ->each(function ($chunk) {
                foreach ($chunk as $country) {
                    Country::create([
                        'code' => $country['iso2'],
                        'phone_code' => $country['phone_code'],
                        'longitude' => $country['longitude'],
                        'latitude' => $country['latitude'],
                        'geometry' => $country['geometry'],
                        'ar' => [
                            'name' => $country['translations']['ar'] ?? $country['name']
                        ],
                        'en' => [
                            'name' => $country['translations']['en'] ?? $country['name']
                        ],
                       
                    ]);
                }
            });
    }

}
