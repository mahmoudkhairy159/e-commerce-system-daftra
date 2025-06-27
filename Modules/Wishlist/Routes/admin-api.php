<?php

use Illuminate\Support\Facades\Route;
use Modules\Wishlist\Http\Controllers\Admin\WishlistController;

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
    // wishlist routes
    Route::controller(WishlistController::class)->name('wishlist.')->prefix('wishlist')->group(function () {
        Route::get('/user/{userId}', 'viewUserWishlist')->name('viewUserWishlist');
    });
    // wishlist routes



});
