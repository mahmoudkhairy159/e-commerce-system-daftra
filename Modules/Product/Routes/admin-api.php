<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\Admin\ProductAccessoryController;
use Modules\Product\Http\Controllers\Admin\ProductController;
use Modules\Product\Http\Controllers\Admin\ProductImageController;
use Modules\Product\Http\Controllers\Admin\ProductReviewController;
use Modules\Product\Http\Controllers\Admin\RelatedProductController;

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



    // products routes
    /***********Trashed products SoftDeletes**************/
    Route::controller(ProductController::class)->prefix('products')->as('products.')->group(function () {
        Route::get('trashed', 'getOnlyTrashed')->name('getOnlyTrashed');
        Route::delete('force-delete/{id}', 'forceDelete')->name('forceDelete');
        Route::post('restore/{id}', 'restore')->name('restore');
    });
    /***********Trashed products SoftDeletes**************/
    Route::controller(ProductController::class)->prefix('products')->name('products.')->group(function () {
        Route::get('/slugs/{slug}', 'showBySlug')->name('showBySlug');
        Route::get('/pending', 'getAllPendingProducts')->name('getAllPendingProducts');
        Route::get('/statistics', 'getStatistics')->name('getStatistics');
        Route::put('/{id}/change-status', 'changeStatus')->name('changeStatus');
        Route::put('/{id}/change-approval-status', 'changeApprovalStatus')->name('changeApprovalStatus');
        Route::put('/{id}/update-product-type', 'updateProductType')->name('updateProductType');
        Route::put('/{id}/update-position', 'updatePosition')->name('updatePosition');
        Route::put('/bulk-update-status', 'bulkUpdateStatus')->name('bulkUpdateStatus');
        Route::delete('/{id}/delete-image', 'deleteImage')->name('deleteImage');
    });
    Route::apiResource('products', ProductController::class);
    // products routes

    // product-images routes
    Route::controller(ProductImageController::class)->prefix('product-images')->as('product-images.')->group(function () {
        Route::get('/product/{id}', 'getByProductId')->name('getByProductId');
    });
    Route::apiResource('product-images', ProductImageController::class)->except(['index']);
    // product-images routes

    /* product-accessories routes*/
    Route::controller(ProductAccessoryController::class)->prefix('product-accessories')->as('product-accessories.')->group(function () {
        Route::get('/product/{id}', 'getProductAccessories')->name('getProductAccessories');
    });
    Route::apiResource('product-accessories', ProductAccessoryController::class)
        ->only(['store', 'update', 'destroy']);
    /* product-accessories routes*/


    /*related-products routes*/
    Route::controller(RelatedProductController::class)->prefix('related-products')->as('related-products.')->group(function () {
        Route::get('/product/{id}', 'getRelatedProducts')->name('getRelatedProducts');
    });
    Route::apiResource('related-products', RelatedProductController::class)
        ->only(['store', 'update', 'destroy']);
    /* related-products routes*/


    // productReviews routes
    Route::controller(ProductReviewController::class)->name('product-reviews.')->prefix('product-reviews')->group(function () {
        Route::get('/product/{productId}', 'getByProductId')->name('getByProductId');
        Route::get('/user/{userId}', 'getByUserId')->name('getByUserId');
    });
    Route::apiResource('product-reviews', ProductReviewController::class)->only(['show', 'destroy']);
    // productReviews routes




});