<?php

namespace App\Http\Controllers\API\V1\Dashboard\Exception;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\EXception\Exception;
use App\Http\Controllers\Controller;
use App\Enums\ResponseCode\HttpStatusCode;
use App\Services\Exception\ExceptionService;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Requests\Exception\CreateExceptionRequest;
use App\Http\Requests\Exception\UpdateExceptionRequest;
use App\Http\Resources\Exception\AllExceptionCollection;
use App\Http\Resources\Exception\AllExceptionResource;
use App\Http\Resources\Exception\ExceptionResource;
use App\Utils\PaginateCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExceptionController extends Controller implements HasMiddleware

{
    protected $exceptionService;

    public function __construct(ExceptionService $exceptionService)
    {
        $this->exceptionService = $exceptionService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth:api'),
            // new Middleware('permission:all_exceptions', only:['index']),
            // new Middleware('permission:create_exception', only:['create']),
            // new Middleware('permission:edit_exception', only:['edit']),
            // new Middleware('permission:update_exception', only:['update']),
            // new Middleware('permission:destroy_exception', only:['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $exceptions = $this->exceptionService->allExceptions();
        return ApiResponse::success(new AllExceptionCollection (PaginateCollection::paginate($exceptions,$request->pageSize?$request->pageSize:10)));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateExceptionRequest $createExceptionRequest)
    {
        try {
        $this->exceptionService->createException($createExceptionRequest->validated());
        return ApiResponse::success([], __('crud.created'));
        }catch (\Throwable $th) {
            return ApiResponse::error($th->getMessage(), [], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show( int $eXceptionId)
    {
        try {
          $exception=  $this->exceptionService->editExceptions($eXceptionId);
            return ApiResponse::success(new ExceptionResource($exception) );
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(__('crud.not_found'), [], HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
            return ApiResponse::error($th->getMessage(), [], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExceptionRequest $updateExceptionRequest,int $exceptionId)
    {
        try {
        $this->exceptionService->updateException($exceptionId,$updateExceptionRequest->validated());
        return ApiResponse::success([], __('crud.updated'));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(__('crud.not_found'), [], HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
            return ApiResponse::error($th->getMessage(), [], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $eXceptionId)
    {
        try {
        $this->exceptionService->deleteException($eXceptionId);
        return ApiResponse::success([], __('crud.deleted'));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(__('crud.not_found'), [], HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
            return ApiResponse::error($th->getMessage(), [], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

    }
}
