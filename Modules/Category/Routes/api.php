<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\Api\CategoryController;

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
  //categories
  Route::controller(CategoryController::class)->prefix('categories')->name('categories.')->group(function () {
    Route::get('/slugs/{slug}', 'showBySlug')->name('showBySlug');
    Route::get('/get-without-pagination', 'getWithoutPagination')->name('getWithoutPagination');
    Route::get('/parents', 'getMainCategories')->name('getMainCategories');
    Route::get('/parent/{id}', 'getByParentId')->name('getByParentId');
    Route::get('/tree-structure', 'getTreeStructure')->name('getTreeStructure');
});
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
//categories
});
