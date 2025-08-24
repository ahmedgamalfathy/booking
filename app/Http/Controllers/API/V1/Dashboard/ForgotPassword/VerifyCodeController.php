<?php

namespace App\Http\Controllers\API\V1\Dashboard\ForgotPassword;

use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\ResponseCode\HttpStatusCode;

class VerifyCodeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
         DB::beginTransaction();
        try {
            $data= $request->validate([
                'code' => 'required',
                'userId'=>['required','exists:users,id']
            ]);
           $user = User::findOrFail($data['userId']);
           if($user->code != $data['code']){
                return ApiResponse::error(__('messages.error.invaild_cod'), [], HttpStatusCode::UNPROCESSABLE_ENTITY);
           }
           if($user->expired_at < now()){
                return ApiResponse::error('Time of code is expired ,please resend code again!', [], HttpStatusCode::UNPROCESSABLE_ENTITY);
            }
            DB::commit();
            return ApiResponse::success([],    __('messages.success.created'));
        } catch (\Throwable $th) {
           DB::rollBack();
            return ApiResponse::error($th->getMessage(), [], HttpStatusCode::UNPROCESSABLE_ENTITY);
        }
    }
}
