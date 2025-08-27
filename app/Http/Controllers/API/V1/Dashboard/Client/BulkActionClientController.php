<?php

namespace App\Http\Controllers\API\V1\Dashboard\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\BulkActionRequest;
use App\Services\Client\BulkActionService;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class BulkActionClientController extends Controller implements HasMiddleware
{
      public function __construct(public BulkActionService $bulkActionService)
    {
        $this->bulkActionService = $bulkActionService;
    }
    public static function middleware(): array
    {
            return [
                new Middleware('auth:api'),
                // new Middleware('p    ermission:bulk_action_client'),
            ];
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(BulkActionRequest $request)
    {
        $validated = $request->validated();
        $results = $this->bulkActionService->bulkAction($validated);
        return $results;
    }
}
