<?php

use Illuminate\Support\Facades\Route;

use Modules\User\Http\Controllers\Api\UserController;
use Modules\User\Http\Controllers\Api\Auth\ForgotPasswordController;
use Modules\User\Http\Controllers\Api\Auth\LoginController;
use Modules\User\Http\Controllers\Api\Auth\LogoutController;
use Modules\User\Http\Controllers\Api\Auth\RegisterController;
use Modules\User\Http\Controllers\Api\Auth\ResetPasswordController;
use Modules\User\Http\Controllers\Api\Auth\SocialiteController;
use Modules\User\Http\Controllers\Api\Auth\VerificationController;
use Modules\User\Http\Controllers\Api\UserAddressController;

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

    // Auth routes
    Route::group(['prefix' => '/auth', 'name' => 'auth.'], function () {
        Route::controller(SocialiteController::class)->as('socialite.')->group(function () {
            Route::get('login/{provider}', 'redirect')->name('redirect');
            Route::get('login/{provider}/callback', 'callback')->name('callback');
            Route::post('/social-login', 'login')->name('login');

        });

        Route::post('/register', [RegisterController::class, 'create'])->name('create');

        Route::controller(LoginController::class)->group(function () {
            Route::post('/login', 'login')->name('login');
            Route::post('/refresh-token', 'refresh')->name('refresh-token');
        });

        Route::controller(VerificationController::class)->prefix('verification')->group(function () {
            Route::post('/verify', 'verify')->name('verification.verify');
            Route::post('/resend', 'resend')->name('verification.resend');
        });
        Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
        // forgot Password

        Route::controller(ForgotPasswordController::class)->group(function () {
            Route::post('/forgot-password', 'forgot')->name('forgot-password');
            Route::post('/forgot-password/resend-otp-code', 'resendCode')->name('forgot-password.resend-otp-code');
        });
        // forgot Password

        // Reset Password

        Route::controller(ResetPasswordController::class)->group(function () {
            Route::post('/reset-password', 'reset')->name('reset');
            Route::post('/verify-otp-code', 'verify')->name('verify');
        });
        // Reset Password

    });

    //users routes
    Route::controller(UserController::class)->prefix('users')->as('users.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::put('/update-info', 'update')->name('update');
        Route::put('/update-user-image', 'updateUserProfileImage')->name('updateUserProfileImage');
        Route::delete('/delete-user-image', 'deleteUserProfileImage')->name('deleteUserProfileImage');
        Route::put('/change-account-activity', 'changeAccountActivity')->name('changeAccountActivity');
        Route::put('/update-general-Preferences', 'updateGeneralPreferences')->name('updateGeneralPreferences');
        Route::put('/change-password', 'changePassword')->name('changePassword');
        Route::get('/get', 'get')->name('get');
        Route::get('/{id}', 'getOneByUserId')->name('getOneByUserId');
        Route::get('/slugs/{slug}', 'showBySlug')->name('showBySlug');
    });
    //users routes
    //user-addresses
    Route::apiResource('user-addresses', UserAddressController::class);
    Route::put('/user-addresses/{id}/set-default-address', [UserAddressController::class, 'setDefaultAddress'])->name('user-addresses.setDefaultAddress');
    //user-addresses
});