<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\Http\Controllers\Admin\CartController;

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
    // cart routes
    Route::controller(CartController::class)->name('cart.')->prefix('cart')->group(function () {
        Route::get('/user/{userId}', 'viewUserCart')->name('viewUserCart');
    });
    // cart routes



});
