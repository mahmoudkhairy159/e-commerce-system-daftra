<?php
return [

    "shippingMethods" => [
        "name" => __('shipping::app.shippingMethods.shippingMethods'),
        "sort" => 6,
        "permissions" => [
            [
                'key' => 'shippingMethods.show',
                'name' => __('shipping::app.shippingMethods.show'),
            ],
            [
                'key' => 'shippingMethods.create',
                'name' => __('shipping::app.shippingMethods.create'),
            ],
            [
                'key' => 'shippingMethods.update',
                'name' => __('shipping::app.shippingMethods.update'),
            ],
            [
                'key' => 'shippingMethods.destroy',
                'name' => __('shipping::app.shippingMethods.destroy'),
            ],
        ]
    ],




];
