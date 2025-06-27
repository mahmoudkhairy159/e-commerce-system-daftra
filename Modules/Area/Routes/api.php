<?php

use Illuminate\Support\Facades\Route;
use Modules\Area\Http\Controllers\Api\CityController;
use Modules\Area\Http\Controllers\Api\CountryController;
use Modules\Area\Http\Controllers\Api\StateController;

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

    // Countries routes
    Route::apiResource('countries', CountryController::class)->only(['index', 'show']);
    // Countries routes


    // States routes
    Route::controller(StateController::class)->name('states.')->prefix('/states')->group(function () {
        Route::get('/country/{country_id}', 'getByCountryId')->name('getByCountryId');
        Route::get('/{id}', 'show')->name('show');
    });
    // States routes


     // cities routes
     Route::controller(CityController::class)->name('cities.')->prefix('/cities')->group(function () {
        Route::get('/country/{country_id}', 'getByCountryId')->name('getByCountryId');
        Route::get('/state/{state_id}', 'getByStateId')->name('getByStateId');
        Route::get('/{id}', 'show')->name('show');
    });
    // cities routes
});
