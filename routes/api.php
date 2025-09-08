<?php

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\Select\SelectController;
use App\Http\Controllers\API\V1\Dashboard\Time\TimeController;
use App\Http\Controllers\API\V1\Dashboard\User\UserController;
use App\Http\Controllers\API\V1\Dashboard\Auth\LoginController;
use App\Http\Controllers\API\V1\Dashboard\Auth\LogoutController;
use App\Http\Controllers\API\V1\Dashboard\Client\ClientController;
use App\Http\Controllers\API\V1\Dashboard\Service\ServiceController;
use App\Http\Controllers\API\V1\Dashboard\User\BulkActionController;
use App\Http\Controllers\Api\V1\Dashboard\User\UserProfileController;
use App\Http\Controllers\API\V1\Dashboard\Client\ClientEmailController;
use App\Http\Controllers\API\V1\Dashboard\Client\ClientPhoneController;
use App\Http\Controllers\API\V1\Dashboard\Exception\ExceptionController;
use App\Http\Controllers\API\V1\Dashboard\Setting\Param\ParamController;
use App\Http\Controllers\API\V1\Dashboard\Client\ClientAddressController;
use App\Http\Controllers\API\V1\Dashboard\Appointment\AppointmentController;
use App\Http\Controllers\API\V1\Dashboard\Client\BulkActionClientController;
use App\Http\Controllers\API\V1\Dashboard\ForgotPassword\SendCodeController;
use App\Http\Controllers\API\V1\Dashboard\Appointment\AvailableDaysController;
use App\Http\Controllers\API\V1\Dashboard\ForgotPassword\ResendCodeController;
use App\Http\Controllers\API\V1\Dashboard\ForgotPassword\VerifyCodeController;
use App\Http\Controllers\API\V1\Dashboard\Appointment\AvailableSlotsController;
use App\Http\Controllers\API\V1\Dashboard\User\ChangeCurrentPasswordController;
use App\Http\Controllers\API\V1\Dashboard\Appointment\BulkActionAppoiController;
use App\Http\Controllers\API\V1\Dashboard\ForgotPassword\ChangePasswordController;

//middleware('auth:sanctum')
Route::prefix('v1/admin')->group(function () {
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

        Route::apiResource('users', UserController::class);
        Route::apiSingleton('profile', UserProfileController::class);
        Route::put('profile/change-password', ChangeCurrentPasswordController::class);
        Route::post('users/bulk-action',BulkActionController::class);

        Route::apiResource('clients', ClientController::class);
        Route::post('clients/bulk-action',BulkActionClientController::class);
        Route::post('clients/{id}/restore', [ClientController::class, 'restore']);
        Route::delete('clients/{id}/force', [ClientController::class, 'forceDelete']);
        Route::prefix('clients/{client}')->group(function () {
            Route::post('/phones/{id}/restore', [ClientPhoneController::class, 'restore']);
            Route::delete('/phones/{id}/force', [ClientPhoneController::class, 'forceDelete']);

            Route::post('/emails/{id}/restore', [ClientEmailController::class, 'restore']);
            Route::delete('/emails/{id}/force', [ClientEmailController::class, 'forceDelete']);

             Route::post('/addresses/{id}/restore', [ClientAddressController::class, 'restore']);
            Route::delete('/addresses/{id}/force', [ClientAddressController::class, 'forceDelete']);

            Route::apiResource('emails', ClientEmailController::class);
            Route::apiResource('phones', ClientPhoneController::class);
            Route::apiResource('addresses', ClientAddressController::class);
        });

        Route::apiResource('services', ServiceController::class);
        Route::apiResource('times', TimeController::class);
        Route::apiResource('exceptions', ExceptionController::class);
        Route::apiResource('appointments', AppointmentController::class);
        Route::prefix('/appointments')->group(function () {
            Route::get('service/{serviceId}/Monthly-availability', [BulkActionAppoiController::class, 'getMonthlyAvailability']);
            Route::get('service/{serviceId}/time-availability', [BulkActionAppoiController::class, 'getAvailableSlots']);
        });

        Route::prefix('settings')->group(function () {
            Route::apiResource('params', ParamController::class);
        });

        Route::prefix('selects')->group(function(){
            Route::get('', [SelectController::class, 'getSelects']);
        });


});

