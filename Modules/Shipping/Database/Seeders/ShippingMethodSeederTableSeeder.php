<?php

namespace Modules\Shipping\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Shipping\Enums\ShippingMethodType;
use Modules\Shipping\Models\ShippingMethod;

class ShippingMethodSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shippingMethods = [
            [
                'type' => ShippingMethodType::HYBRID,
                'flat_rate' => 10.00,
                'per_km_rate' => 1.50,
                'max_distance' => 100,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'en' => [
                    'title' => 'Standard Shipping',
                    'description' => 'Fast and reliable standard shipping.',
                ],
                'ar' => [
                    'title' => 'الشحن العادي',
                    'description' => 'شحن عادي موثوق وسريع.',
                ],
            ],
            [
                'type' => ShippingMethodType::HYBRID,
                'flat_rate' => 20.00,
                'per_km_rate' => 2.50,
                'max_distance' => 50,
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'en' => [
                    'title' => 'Express Shipping',
                    'description' => 'Super-fast express shipping service.',
                ],
                'ar' => [
                    'title' => 'الشحن السريع',
                    'description' => 'خدمة الشحن السريع فائقة السرعة.',
                ],
            ],
        ];

        foreach ($shippingMethods as $method) {

            ShippingMethod::create($method);
        }
    }
}
