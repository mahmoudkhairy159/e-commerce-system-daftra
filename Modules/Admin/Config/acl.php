<?php
return [
    "roles" => [
        "name" => __('admin::app.roles.roles'),
        "sort" => 1,
        "permissions" =>  [
            [
                'key' => 'roles.show',
                'name' => __('admin::app.roles.show'),
            ],
            [
                'key' => 'roles.create',
                'name' => __('admin::app.roles.create'),
            ],
            [
                'key' => 'roles.update',
                'name' => __('admin::app.roles.update'),
            ],
            [
                'key' => 'roles.destroy',
                'name' => __('admin::app.roles.destroy'),
            ],
        ]
    ],
    "admins" => [
        "name" => __('admin::app.admins.admins'),
        "sort" => 2,
        "permissions" =>  [
            [
                'key' => 'admins.show',
                'name' => __('admin::app.admins.show'),
            ],
            [
                'key' => 'admins.create',
                'name' => __('admin::app.admins.create'),
            ],
            [
                'key' => 'admins.update',
                'name' => __('admin::app.admins.update'),
            ],
            [
                'key' => 'admins.destroy',
                'name' => __('admin::app.admins.destroy'),
            ],
        ]
    ]


];
