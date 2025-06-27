<?php
return [

    "categories" => [
        "name" => __('category::app.categories.categories'),
        "sort" => 6,
        "permissions" => [
            [
                'key' => 'categories.show',
                'name' => __('category::app.categories.show'),
            ],
            [
                'key' => 'categories.create',
                'name' => __('category::app.categories.create'),
            ],
            [
                'key' => 'categories.update',
                'name' => __('category::app.categories.update'),
            ],
            [
                'key' => 'categories.destroy',
                'name' => __('category::app.categories.destroy'),
            ],
        ]
    ],



];
