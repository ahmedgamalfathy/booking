<?php

namespace App\Http\Controllers\API\V1\Dashboard\Setting\Param;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Enums\ResponseCode\HttpStatusCode;
use App\Services\Settings\Param\ParamService;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Param\CreateParamRequest;
use App\Http\Requests\Param\UpdateParamRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ParamController extends Controller implements HasMiddleware
{
    protected $paramService;
    public function __construct(ParamService $paramService)
    {
        $this->paramService = $paramService;
    }
        public static function middleware(): array
    {
        return [
            new Middleware('auth:api'),
            // new Middleware('permission:all_params', only:['index']),
            // new Middleware('permission:create_param', only:['create']),
            // new Middleware('permission:edit_param', only:['edit']),
            // new Middleware('permission:update_param', only:['update']),
            // new Middleware('permission:destroy_param', only:['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $param = $this->paramService->allParams();
        return  ApiResponse::success($param);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateParamRequest $createParamRequest)
    {
        $this->paramService->createParam($createParamRequest->validated());
        return ApiResponse::success([],__("created successfully"));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try{
        $param = $this->paramService->getParamById($id);
        return ApiResponse::success($param);
        } catch (ModelNotFoundException $th) {
        return ApiResponse::error('Param not found', [], HttpStatusCode::NOT_FOUND);
        }catch (\Exception $e) {
        return ApiResponse::error($e->getMessage(), [], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParamRequest $updateParamRequest,int $id)
    {
        try{
        $this->paramService->updateParam($id,$updateParamRequest->validated());
        return ApiResponse::success([],"updated successfully");
        } catch (ModelNotFoundException $th) {
        return ApiResponse::error('Param not found', [], HttpStatusCode::NOT_FOUND);
        }catch (\Exception $e) {
        return ApiResponse::error($e->getMessage(), [], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try{
        $this->paramService->deleteParam($id);
        return ApiResponse::success("deleted successfully");
        } catch (ModelNotFoundException $th) {
        return ApiResponse::error('Param not found', [], HttpStatusCode::NOT_FOUND);
        }catch (\Exception $e) {
        return ApiResponse::error($e->getMessage(), [], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
