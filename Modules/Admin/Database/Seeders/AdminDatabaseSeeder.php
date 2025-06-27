<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Area\Database\Seeders\CitySeeder;
use Modules\Area\Database\Seeders\CountrySeeder;
use Modules\Area\Database\Seeders\StateSeeder;
use Modules\Category\Database\Seeders\CategorySeeder;
use Modules\Product\Database\Seeders\ProductSeeder;
use Modules\Shipping\Database\Seeders\ShippingMethodSeederTableSeeder;
use Modules\User\Database\Seeders\UserSeeder;

class AdminDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([

            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            RolesSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ShippingMethodSeederTableSeeder::class,

        ]);
    }
}