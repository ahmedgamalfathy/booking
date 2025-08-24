<?php

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\Dashboard\User\UserController;
use App\Http\Controllers\API\V1\Dashboard\Auth\LoginController;
use App\Http\Controllers\API\V1\Dashboard\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Dashboard\User\UserProfileController;
use App\Http\Controllers\API\V1\Dashboard\ForgotPassword\SendCodeController;
use App\Http\Controllers\API\V1\Dashboard\ForgotPassword\ResendCodeController;
use App\Http\Controllers\API\V1\Dashboard\ForgotPassword\VerifyCodeController;
use App\Http\Controllers\API\V1\Dashboard\User\ChangeCurrentPasswordController;
use App\Http\Controllers\API\V1\Dashboard\ForgotPassword\ChangePasswordController;
use App\Http\Controllers\API\V1\Dashboard\User\BulkActionController;

//middleware('auth:sanctum')
Route::prefix('v1/admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiSingleton('profile', UserProfileController::class);
        Route::post('users/bulk-action',BulkActionController::class);
        Route::put('profile/change-password', ChangeCurrentPasswordController::class);
        Route::prefix('auth')->group(function () {
            //login , logout , forgot password , reset password
            Route::post('/login ', LoginController::class);
            Route::post('/logout',LogoutController::class);
        });
        Route::prefix('forgot-password')->group(function () {
            Route::post('/sendCode', SendCodeController::class);
            Route::post('/verifyCode', VerifyCodeController::class);
            Route::post('/resendCode', ResendCodeController::class);
            Route::post('/changePassword', ChangePasswordController::class);
        });


});

