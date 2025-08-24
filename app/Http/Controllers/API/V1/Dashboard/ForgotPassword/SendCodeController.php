<?php

namespace App\Http\Controllers\API\V1\Dashboard\ForgotPassword;

use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordSendCode;
use Illuminate\Support\Facades\Mail;
use App\Enums\ResponseCode\HttpStatusCode;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\User\LoginProfileResource;

class SendCodeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        DB::beginTransaction();
        try{
            $data=$request->validate([
                'email' => 'required|email|exists:users,email',
            ]);
            $user =User::where("email", $data['email'])->first();
            if(!$user){
                return response()->json([
                    "message"=>__('messages.error.not_found')
                ],404);
            }
            $user->update([
                "code"=>rand(100000,999999),
                'expired_at' => now()->addMinutes(5),
            ]);
            Mail::to($data['email'])->send(new ForgotPasswordSendCode($user, $user->code));
            DB::commit();
            return ApiResponse::success(new LoginProfileResource($user) );
            }catch(ValidationException $e){
                DB::rollBack();
                return ApiResponse::error($e->getMessage(), [], HttpStatusCode::UNPROCESSABLE_ENTITY);
            }
    }
}
