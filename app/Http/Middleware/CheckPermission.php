<?php

namespace App\Http\Middleware;

use App\Enums\ResponseCode\HttpStatusCode;
use Closure;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next , $permission): Response
    {
     if (! $request->user() || ! $request->user()->can($permission)) {
           return ApiResponse::error("You do not have permission",[],HttpStatusCode::FORBIDDEN);
        }

        return $next($request);
    }
}
