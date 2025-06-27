<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Admin\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

       Admin::create([
            'name'       => 'Ahmed Salama',
            'email'      => 'ahmed.salama@wardlin.com',
            'password'   => '12345678',
            // 'api_token'  => Str::random(80),
            'status'     => 1,
            'role_id'    => 1,
        ]);


        Admin::create([
            'name'       => 'Mahmoud Khairy',
            'email'      => 'mahmoud.khairy@wardlin.com',
            'password'   => '12345678',
            // 'api_token'  => Str::random(80),
            'status'     => 1,
            'role_id'    => 1,
        ]);
    }
}
