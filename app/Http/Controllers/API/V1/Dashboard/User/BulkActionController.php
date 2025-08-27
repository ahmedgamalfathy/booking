<?php

namespace App\Http\Controllers\API\V1\Dashboard\User;

use App\Models\User;
use App\Enums\StatusEnum;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\User\BulkActionService;
use App\Http\Requests\User\BulkActionRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class BulkActionController extends Controller implements HasMiddleware
{
    public function __construct(public BulkActionService $bulkActionService)
    {
        $this->bulkActionService = $bulkActionService;
    }
    public static function middleware(): array
    {
            return [
                new Middleware('auth:api'),
                new Middleware('permission:bulk_action_user'),
            ];
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(BulkActionRequest $request)
    {
        DB::beginTransaction();
        $validated=$request->validated();
        $results= $this->bulkActionService->bulkAction($validated);
        DB::commit();
        return $results;
    }
}
