<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Admin\UserAddressController;
use Modules\User\Http\Controllers\Admin\UserController;

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



     // Users routes
     Route::get('/users/slugs/{slug}', [UserController::class, 'showBySlug'])->name('users.showBySlug');
     Route::put('/users/{id}/change-status', [UserController::class, 'changeStatus'])->name('changeStatus');
     Route::apiResource('users', UserController::class);
     // Users routes
      //user-addresses
    Route::controller(UserAddressController::class)->prefix('user-addresses')->as('user-addresses.')->group(function () {
        Route::get('/user/{id}', 'getByUserId')->name('getByUserId');
    });
    Route::apiResource('user-addresses', UserAddressController::class);
    Route::put('/user-addresses/{id}/set-default-address/{user}', [UserAddressController::class, 'setDefaultAddress'])->name('user-addresses.setDefaultAddress');
    //user-addresses

});