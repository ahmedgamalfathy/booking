<?php

namespace App\Http\Controllers\API\V1\Dashboard\User;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Enums\ResponseCode\HttpStatusCode;
use App\Http\Requests\User\ChangePasswordRequest;

class ChangeCurrentPasswordController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ChangePasswordRequest $request)
    {
        $authUser = $request->user();

        if (!Hash::check($request->currentPassword, $authUser->password)) {
            return ApiResponse::error(__('auth.current_password'), HttpStatusCode::UNPROCESSABLE_ENTITY);
        }

        // Update password securely
        $authUser->update([
            'password' => Hash::make(value: $request->password),
        ]);

        $authUser->tokens()->delete();

        return ApiResponse::success([], __('auth.change_password'));
    }
}
