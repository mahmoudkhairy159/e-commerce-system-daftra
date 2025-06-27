<?php

use Illuminate\Support\Facades\Route;
use Modules\Wishlist\Http\Controllers\Api\WishlistController;

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

   //  wishlist routes
   Route::controller(WishlistController::class)
   ->name('wishlist.')
   ->prefix('wishlist')
   ->group(function () {
       // Display the wishlist
       Route::get('/wishlist-products', 'viewWishlist')->name('view');

       // Add a product to the wishlist
       Route::post('/wishlist-products', 'addToWishlist')->name('add');

     
       // Remove a specific product from the wishlist
       Route::delete('/wishlist-products/{id}', 'removeFromWishlist')->name('remove');

       // Clear the entire wishlist
       Route::delete('/clear', 'clearWishlist')->name('clear');

   });
//  wishlist routes
});
