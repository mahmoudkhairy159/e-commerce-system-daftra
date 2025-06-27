<?php

namespace Modules\Area\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Area\Models\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


     /*run for production */
     public function run()
     {
         $filePath = database_path('data/cities.json');
         $batchSize = 10; // Adjust this to a suitable batch size

         // Read the file and decode the JSON
         $cities = json_decode(file_get_contents($filePath), true);

         collect($cities)
             ->chunk($batchSize)
             ->each(function ($chunk) {
                 // Extract unique country codes and state names from the chunk
                 $countryCodes = $chunk->pluck('country_code')->unique();
                 $stateNames = $chunk->pluck('state_name')->unique();

                 // Fetch the country IDs using country codes
                 $countryMap = DB::table('countries')
                     ->whereIn('code', $countryCodes)
                     ->pluck('id', 'code')
                     ->toArray();

                 // Fetch the state IDs using state names
                 $stateMap = DB::table('state_translations')
                     ->whereIn('name', $stateNames) // Make sure 'name' field matches exactly
                     ->pluck('state_id', 'name')
                     ->toArray();

                 // Iterate over the cities and create them
                 foreach ($chunk as $city) {
                     // Get country_id and state_id from the maps
                     $countryId = $countryMap[$city['country_code']] ?? null;
                     $stateId = $stateMap[$city['state_name']] ?? null;

                    

                     // Create the city record
                     City::create([
                         'longitude' => $city['longitude'],
                         'latitude' => $city['latitude'],
                         'country_id' => $countryId,
                         'state_id' => $stateId, // Ensure state_id is set properly
                         'ar' => [
                             'name' => $city['name_ar']
                         ],
                         'en' => [
                             'name' => $city['name']
                         ],
                     ]);
                 }
             });
     }

         /*********************************end run for production **********************************************************/

    //  /*run for development */
    // public function run()
    // {
    //     $filePath = database_path('data/cities.json');
    //     $batchSize = 100; // Adjust this to a suitable batch size

    //     // Read the file and decode the JSON
    //     $cities = json_decode(file_get_contents($filePath), true);

    //     collect($cities)
    //         ->chunk($batchSize)
    //         ->each(function ($chunk) {
    //             $countryCodes = $chunk->pluck('country_code')->unique();
    //             $stateNames = $chunk->pluck('state_name')->unique();
    //             $countryMap =  DB::table('countries')
    //                 ->whereIn('code', $countryCodes)
    //                 ->pluck('id', 'code')
    //                 ->toArray();
    //             $stateMap =  DB::table('state_translations')
    //                 ->whereIn('name', $stateNames)
    //                 ->where('locale', 'en') // Adjust locale as necessary
    //                 ->pluck('state_id', 'name')
    //                 ->toArray();

    //             $cityCountPerCountry = [];
    //             foreach ($chunk as $city) {
    //                 $countryId = $countryMap[$city['country_code']] ?? null;
    //                 $stateId = $stateMap[$city['state_name']] ?? null;

    //                 if ($countryId) {
    //                     // Initialize or increment the count for the country
    //                     if (!isset($cityCountPerCountry[$countryId])) {
    //                         $cityCountPerCountry[$countryId] = 0;
    //                     }

    //                     // Only insert if the country hasn't reached 5 cities
    //                     if ($cityCountPerCountry[$countryId] < 5) {
    //                         City::create([
    //                             'longitude' => $city['longitude'],
    //                             'latitude' => $city['latitude'],
    //                             'country_id' => $countryId,
    //                             'state_id' => $stateId,
    //                             'ar' => [
    //                                 'name' => $city['name']
    //                             ],
    //                             'en' => [
    //                                 'name' => $city['name']
    //                             ],
    //
    //                         ]);

    //                         // Increment the count for the country
    //                         $cityCountPerCountry[$countryId]++;
    //                     }
    //                 }
    //             }
    //         });
    // }
    //      /*************************************end run for development*********************** */



}
