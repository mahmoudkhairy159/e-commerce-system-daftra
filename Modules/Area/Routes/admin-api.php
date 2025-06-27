<?php

use Illuminate\Support\Facades\Route;
use Modules\Area\Http\Controllers\Admin\CityController;
use Modules\Area\Http\Controllers\Admin\CountryController;
use Modules\Area\Http\Controllers\Admin\StateController;

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



    // Countries routes
    /***********Trashed Countries SoftDeletes**************/
    Route::controller(CountryController::class)->prefix('countries')->as('countries.')->group(function () {
        Route::get('/trashed', 'getOnlyTrashed')->name('getOnlyTrashed');
        Route::delete('/force-delete/{id}', 'forceDelete')->name('forceDelete');
        Route::post('/restore/{id}', 'restore')->name('restore');
    });
    /***********Trashed Countries SoftDeletes**************/
    Route::apiResource('countries', CountryController::class);
    // Countries routes

    // States routes
    Route::get('/states/country/{country_id}', [StateController::class, 'getByCountryId'])->name('states.getByCountryId');
    /***********Trashed States SoftDeletes**************/
    Route::controller(StateController::class)->prefix('states')->as('states.')->group(function () {
        Route::get('/trashed', 'getOnlyTrashed')->name('getOnlyTrashed');
        Route::delete('/force-delete/{id}', 'forceDelete')->name('forceDelete');
        Route::post('/restore/{id}', 'restore')->name('restore');
    });
    /***********Trashed States SoftDeletes**************/
    Route::apiResource('states', StateController::class);
    // States routes

     // Cities routes
     Route::controller(CityController::class)->name('cities.')->prefix('/cities')->group(function () {
        Route::get('/country/{country_id}', 'getByCountryId')->name('getByCountryId');
        Route::get('/state/{state_id}', 'getByStateId')->name('getByStateId');
    });
     /***********Trashed Cities SoftDeletes**************/
     Route::controller(CityController::class)->prefix('cities')->as('cities.')->group(function () {
         Route::get('/trashed', 'getOnlyTrashed')->name('getOnlyTrashed');
         Route::delete('/force-delete/{id}', 'forceDelete')->name('forceDelete');
         Route::post('/restore/{id}', 'restore')->name('restore');
     });
     /***********Trashed States SoftDeletes**************/
     Route::apiResource('cities', CityController::class);
     // Cities routes

});
