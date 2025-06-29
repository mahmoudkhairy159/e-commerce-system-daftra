<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

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


            RolesSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ShippingMethodSeederTableSeeder::class,

        ]);
    }
}