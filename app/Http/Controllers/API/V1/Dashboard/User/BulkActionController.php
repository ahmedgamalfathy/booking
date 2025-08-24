<?php

namespace App\Http\Controllers\API\V1\Dashboard\User;

use App\Models\User;
use App\Enums\StatusEnum;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\BulkActionRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class BulkActionController extends Controller implements HasMiddleware
{
        public static function middleware(): array
    {
        return [
            new Middleware('auth:api'),
            new Middleware('permission:bulk_action'),
        ];
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(BulkActionRequest $request)
    {
        $validated=$request->validated();
        $query = User::query();
        if ($validated['scope'] === 'selected') {
            $query->whereIn('id', $validated['ids']);
        }
        switch ($validated['action']) {
            case 'active':
                $query->update(['is_active' => StatusEnum::ACTIVE]);
                break;
            case 'inActive':
                $query->update(['is_active' => StatusEnum::INACTIVE]);
                $query->each(function ($user) {
                    $user->tokens()->delete();
                });
                break;
            case 'delete':
                $query->each(function ($user) {
                    $user->tokens()->delete();
                });
                $query->delete();
                break;
        }
        return ApiResponse::success([],"Users {$validated['action']}d successfully");
    }
}
