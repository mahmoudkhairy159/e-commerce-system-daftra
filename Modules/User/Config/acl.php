<?php
return [
    "users" => [
        "name" => __('user::app.users.users'),
        "sort" => 27,
        "permissions" =>  [
            [
                'key' => 'users.show',
                'name' => __('user::app.users.show'),
            ],
            [
                'key' => 'users.create',
                'name' => __('user::app.users.create'),
            ],
            [
                'key' => 'users.update',
                'name' => __('user::app.users.update'),
            ],
            [
                'key' => 'users.destroy',
                'name' => __('user::app.users.destroy'),
            ],
        ]
    ],
    "userProfiles" => [
        "name" => __('user::app.userProfiles.userProfiles'),
        "sort" => 28,
        "permissions" =>  [
            [
                'key' => 'userProfiles.show',
                'name' => __('user::app.userProfiles.show'),
            ],
            [
                'key' => 'userProfiles.create',
                'name' => __('user::app.userProfiles.create'),
            ],
            [
                'key' => 'userProfiles.update',
                'name' => __('user::app.userProfiles.update'),
            ],
            [
                'key' => 'userProfiles.destroy',
                'name' => __('user::app.userProfiles.destroy'),
            ],
        ]
        ],
        "userAddresses" => [
            "name" => __('user::app.userAddresses.userAddresses'),
            "sort" => 28,
            "permissions" =>  [
                [
                    'key' => 'userAddresses.show',
                    'name' => __('user::app.userAddresses.show'),
                ],
                [
                    'key' => 'userAddresses.create',
                    'name' => __('user::app.userAddresses.create'),
                ],
                [
                    'key' => 'userAddresses.update',
                    'name' => __('user::app.userAddresses.update'),
                ],
                [
                    'key' => 'userAddresses.destroy',
                    'name' => __('user::app.userAddresses.destroy'),
                ],
            ]
        ]


];
