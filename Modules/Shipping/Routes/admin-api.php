<?php
use Illuminate\Support\Facades\Route;
use Modules\Shipping\Http\Controllers\Admin\ShippingMethodController;


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




    Route::prefix('shipping-methods')->name('shipping-methods.')->controller(ShippingMethodController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('{shipping_method}', 'show')->name('show');
        Route::put('{shipping_method}', 'update')->name('update');
        Route::delete('{shipping_method}', 'destroy')->name('destroy');
    });


});