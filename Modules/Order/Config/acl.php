<?php
return [

    "orders" => [
        "name" => __('order::app.orders.orders'),
        "sort" => 7,
        "permissions" => [
            [
                'key' => 'orders.show',
                'name' => __('order::app.orders.show'),
            ],
            [
                'key' => 'orders.create',
                'name' => __('order::app.orders.create'),
            ],
            [
                'key' => 'orders.update',
                'name' => __('order::app.orders.update'),
            ],
            [
                'key' => 'orders.destroy',
                'name' => __('order::app.orders.destroy'),
            ],
        ]
    ],

    




];
