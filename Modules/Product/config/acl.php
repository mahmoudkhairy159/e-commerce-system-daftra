<?php
return [

    "products" => [
        "name" => __('product::app.products.products'),
        "sort" => 6,
        "permissions" => [
            [
                'key' => 'products.show',
                'name' => __('product::app.products.show'),
            ],
            [
                'key' => 'products.create',
                'name' => __('product::app.products.create'),
            ],
            [
                'key' => 'products.update',
                'name' => __('product::app.products.update'),
            ],
            [
                'key' => 'products.destroy',
                'name' => __('product::app.products.destroy'),
            ],
        ]
    ],
    "flashSales" => [
        "name" => __('product::app.flashSales.flashSales'),
        "sort" => 6,
        "permissions" => [
            [
                'key' => 'flashSales.show',
                'name' => __('product::app.flashSales.show'),
            ],
            [
                'key' => 'flashSales.create',
                'name' => __('product::app.flashSales.create'),
            ],
            [
                'key' => 'flashSales.update',
                'name' => __('product::app.flashSales.update'),
            ],
            [
                'key' => 'flashSales.destroy',
                'name' => __('product::app.flashSales.destroy'),
            ],
        ]
    ],

    "productReviews" => [
        "name" => __('product::app.productReviews.productReviews'),
        "sort" => 6,
        "permissions" => [
            [
                'key' => 'products.show',
                'name' => __('product::app.productReviews.show'),
            ],
            [
                'key' => 'products.destroy',
                'name' => __('product::app.productReviews.destroy'),
            ],
        ]
    ],



];
