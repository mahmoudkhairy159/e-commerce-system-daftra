<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminController;
use Modules\Admin\Http\Controllers\AuthController;
use Modules\Admin\Http\Controllers\PermissionsController;
use Modules\Admin\Http\Controllers\RoleController;

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
    // Auth routes
    Route::controller(AuthController::class)->name('auth.')->prefix('/auth')->group(function () {
        Route::post('/login', 'create')->name('login');
        Route::post('/logout', 'destroy')->name('logout');
        Route::post('/refresh-token', 'refresh')->name('refresh-token');
        Route::post('/update-info', 'update')->name('update-info');
        Route::post('/update-password', 'updatePassword')->name('update-password');
        Route::get('/get-info', 'get')->name('get-info');
    });
    // Auth routes


    // Permissions routes
    Route::controller(PermissionsController::class)->name('permissions.')->prefix('/permissions')->group(function () {
        Route::get('/', 'index')->name('index');
    });
    // Permissions routes


    // Role routes
    Route::apiResource('roles',RoleController::class);
    // Role routes


    // Admin routes
    Route::apiResource('admins',AdminController::class);
    // Admin routes

});