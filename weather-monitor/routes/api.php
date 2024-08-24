<?php

use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\WeatherController as AdminWeatherController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);

        Route::get('email-verification/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
            ->middleware('throttle:6,1')
            ->name('verification.verify');

        Route::post('forgot-password/send', [AuthController::class, 'sendResetPasswordEmail'])
            ->name('password.email');

        Route::get('forgot-password/verify', [AuthController::class, 'resetPassword'])
            ->name('password.email');
    });

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('change-password', [AuthController::class, 'changePassword']);

            Route::post('email-verification/send', [AuthController::class, 'sendVerificationEmail'])
                ->middleware(['throttle:6,1'])
                ->name('verification.send');
        });

        Route::group(['prefix' => 'users/me'], function () {
            Route::get('/', [UserController::class, 'getMe']);
            Route::put('/', [UserController::class, 'updateMe']);
            Route::delete('/', [UserController::class, 'deleteMe']);
        });

		Route::group(['prefix' => 'admin', 'middleware' => 'role:admin'], function () {
			Route::get('stats', [StatsController::class, 'get']);

			Route::group(['prefix' => 'users'], function () {
				Route::get('/', [AdminUserController::class, 'index']);
				Route::post('/', [AdminUserController::class, 'create']);
				Route::put('{user}', [AdminUserController::class, 'update']);
				Route::delete('{user}', [AdminUserController::class, 'delete']);
			});

			Route::group(['prefix' => 'weather'], function () {
				Route::get('/', [AdminWeatherController::class, 'index']);
				Route::post('/', [AdminWeatherController::class, 'create']);
				Route::put('{weather}', [AdminWeatherController::class, 'update']);
				Route::delete('{weather}', [AdminWeatherController::class, 'delete']);
			});

			Route::group(['prefix' => 'settings'], function () {
				Route::get('/', [SettingController::class, 'index']);
				Route::post('/', [SettingController::class, 'update']);
			});
		});
    });

    Route::group(['prefix' => 'weather'], function () {
        Route::get('{location}', [WeatherController::class, 'get']);
        Route::get('{location}/export', [WeatherController::class, 'export'])->middleware('throttle:6,1');
    });

    Route::group(['prefix' => 'locations'], function () {
        Route::get('/', [LocationController::class, 'index']);
		Route::put('/{location}/favourite', [LocationController::class, 'toggleFavourite']);
		Route::get('/{location}/reviews', [LocationController::class, 'getReviews']);
		Route::post('/{location}/reviews', [LocationController::class, 'createReview']);
		Route::delete('/{location}/reviews/{review}', [LocationController::class, 'deleteReview']);
    });
});
