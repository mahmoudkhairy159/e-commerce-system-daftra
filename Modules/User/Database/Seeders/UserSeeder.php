<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
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
        }

    }
}
