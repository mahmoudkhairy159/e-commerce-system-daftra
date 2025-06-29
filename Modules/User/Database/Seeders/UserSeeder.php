<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Enums\UserAddressEnum;
use Modules\User\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [

            [
                'name' => 'Ahmed Eissa ',
                'email' => 'ahmedsalamacode@gmail.com',
                'password' => '12345678',
                'email_verified_at' =>'2023-10-07 19:22:09',

            ],

            [
                'name' => 'Mahmoud Khairy',
                'email' => 'mahmoudkhairy159@gmail.com',
                'password' => '12345678',
                'email_verified_at' => '2023-10-07 19:22:09',

            ],

        ];
        foreach ($items as $item) {
            $user = User::Create($item);
            $user->profile()->create();

            // Generate random address for each user
            $address=$user->userAddresses()->create([
                'zip_code' => rand(10000, 99999),
                'address' => fake()->address(),
                'type' => UserAddressEnum::getConstants()[array_rand(UserAddressEnum::getConstants())],
                'phone_code' => '+20',
                'phone' => '1' . rand(000000000, 999999999), // Egyptian mobile format
                'title' => 'Home Address',
                'longitude' => fake()->longitude(29, 33), // Egypt longitude range
                'latitude' => fake()->latitude(24, 32), // Egypt latitude range
            ]);
            $user->default_address_id=$address->id;
            $user->save();
        }

    }
}