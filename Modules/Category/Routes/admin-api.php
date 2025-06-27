<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\Admin\CategoryController;

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



    /***********Trashed categories SoftDeletes**************/
    Route::controller(CategoryController::class)->prefix('categories')->as('categories.')->group(function () {
        Route::get('trashed', 'getOnlyTrashed')->name('getOnlyTrashed');
        Route::delete('force-delete/{id}', 'forceDelete')->name('forceDelete');
        Route::post('restore/{id}', 'restore')->name('restore');
    });
    /***********Trashed categories SoftDeletes**************/
    Route::controller(CategoryController::class)->prefix('categories')->name('categories.')->group(function () {
        Route::get('/slugs/{slug}', 'showBySlug')->name('showBySlug');
        Route::get('/get-without-pagination', 'getWithoutPagination')->name('getWithoutPagination');
        Route::get('/parents', 'getMainCategories')->name('getMainCategories');
        Route::get('/parent/{id}', 'getByParentId')->name('getByParentId');
        Route::get('/tree-structure', 'getTreeStructure')->name('getTreeStructure');
        Route::put('/{id}/change-status', 'changeStatus')->name('changeStatus');
        Route::put('/{id}/update-position', 'updatePosition')->name('updatePosition');
        Route::put('/bulk-update-status', 'bulkUpdateStatus')->name('bulkUpdateStatus');


    });
    Route::apiResource('categories', CategoryController::class);
    //categories routes


});
