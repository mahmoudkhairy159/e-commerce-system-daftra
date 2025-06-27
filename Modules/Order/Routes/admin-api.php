<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\Admin\OrderController;
use Modules\Order\Http\Controllers\Admin\OrderProductController;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::prefix('v1')->name('admin-api.')->group(function () {

/// orders routes
Route::controller(OrderController::class)->prefix('orders')->as('orders.')->group(function () {
    Route::get('/status/{status}', 'getAllByStatus')->name('getAllByStatus');
    Route::get('/user/{id}', 'getByUserId')->name('getByUserId');
    Route::put('/{id}/update-status', 'updateStatus')->name('updateStatus');
});
Route::apiResource('orders', OrderController::class)->except(['store', 'update']);
// orders routes

//order-products
Route::controller(OrderProductController::class)->prefix('order-products')->as('order-products.')->group(function () {
    Route::get('/order/{id}', 'getByOrderId')->name('getByOrderId');
});
//order-products


});

