<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\Http\Controllers\Api\CartController;

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

   //  cart routes
   Route::controller(CartController::class)
   ->name('cart.')
   ->prefix('cart')
   ->group(function () {
       // Display the cart
       Route::get('/cart-products', 'viewCart')->name('view');

       // Add a product to the cart
       Route::post('/cart-products', 'addToCart')->name('add');

       // Update the quantity of a specific product in the cart
       Route::put('/cart-products/{id}', 'updateProductCart')->name('update');

       // Remove a specific product from the cart
       Route::delete('/cart-products/{id}', 'removeFromCart')->name('remove');

       // Clear the entire cart
       Route::delete('/clear', 'clearCart')->name('clear');

   });
//  cart routes
});
