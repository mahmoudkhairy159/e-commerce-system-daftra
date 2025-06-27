<?php

use Illuminate\Support\Facades\Route;
use Modules\Shipping\Http\Controllers\Api\ShippingMethodController;

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



Route::prefix('v1')->name('user-api.')->group(function () {




    Route::prefix('shipping-methods')->name('shipping-methods.')->controller(ShippingMethodController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('{shipping_method}', 'show')->name('show');
    });


});