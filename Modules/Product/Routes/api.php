<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\Api\RelatedProductController;
use Modules\Product\Http\Controllers\Api\ProductAccessoryController;
use Modules\Product\Http\Controllers\Api\ProductController;
use Modules\Product\Http\Controllers\Api\ProductImageController;
use Modules\Product\Http\Controllers\Api\ProductReviewController;

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
    // products routes
    Route::controller(ProductController::class)->prefix('products')->name('products.')->group(function () {
        Route::get('/slugs/{slug}', 'showBySlug')->name('showBySlug');
        Route::get('/featured', 'getFeaturedProducts')->name('getFeaturedProducts');
        Route::get('/new-arrivals', 'getNewArrivals')->name('getNewArrivals');
        Route::get('/best-sellers', 'getBestSellers')->name('getBestSellers');
        Route::get('/top-products', 'getTopProducts')->name('getTopProducts');
        Route::get('/category/{categoryId}', 'getByCategory')->name('getByCategory');
        Route::get('/type/{type}', 'getByType')->name('getByType');
    });
    Route::apiResource('products', ProductController::class)->only(['index', 'show']);
    // products routes

    // product-images routes
    Route::controller(ProductImageController::class)->prefix('product-images')->as('product-images.')->group(function () {
        Route::get('/product/{id}', 'getByProductId')->name('getByProductId');
    });
    Route::apiResource('product-images', ProductImageController::class)->only(['show']);
    // product-images routes

    // product reviews routes
    Route::controller(ProductReviewController::class)->name('product-reviews.')->prefix('/product-reviews')->group(function () {
        Route::get('/product/{productId}', 'getByProductId')->name('getByProductId');
        Route::get('/user/{userId}', 'getByUserId')->name('getByUserId');
    });
    Route::apiResource('product-reviews', ProductReviewController::class)->except(['index']);
    // product reviews routes
    // related-products routes
    Route::controller(RelatedProductController::class)->prefix('related-products')->as('related-products.')->group(function () {
        Route::get('/product/{id}', 'getRelatedProducts')->name('getRelatedProducts');
    });
    // related-products routes
    // product-accessories routes
    Route::controller(ProductAccessoryController::class)->prefix('product-accessories')->as('related-products.')->group(function () {
        Route::get('/product/{id}', 'getProductAccessories')->name('getProductAccessories');
    });
    // product-accessories routes



});