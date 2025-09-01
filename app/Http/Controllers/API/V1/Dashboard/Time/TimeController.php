<?php

namespace App\Http\Controllers\API\V1\Dashboard\Time;

use App\Models\Time;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Utils\PaginateCollection;
use App\Services\Time\TimeService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Time\TimeResource;
use App\Enums\ResponseCode\HttpStatusCode;
use App\Http\Requests\Time\CreateTimeRequest;
use App\Http\Requests\Time\UpdateTimeRequest;
use App\Http\Resources\Time\AllTimeCollection;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class TimeController extends Controller implements HasMiddleware
{
    protected $timeService;

    public function __construct(TimeService $timeService)
    {
        $this->timeService = $timeService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth:api'),
            // new Middleware('permission:all_times', only:['index']),
            // new Middleware('permission:create_time', only:['create']),
            // new Middleware('permission:edit_time', only:['edit']),
            // new Middleware('permission:update_time', only:['update']),
            // new Middleware('permission:destroy_time', only:['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $times = $this->timeService->allTimes();
        return ApiResponse::success(new AllTimeCollection (PaginateCollection::paginate($times,$request->pageSize?$request->pageSize:10)));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTimeRequest $createTimeRequest)
    {
        try {
            DB::beginTransaction();
            $this->timeService->createTime($createTimeRequest->validated());
            DB::commit();
            return ApiResponse::success([], __('crud.created'));
        }catch(QueryException $e){
           return ApiResponse::error('', $e->getMessage(), HttpStatusCode::NOT_FOUND);
        } catch (\Throwable $th) {
        return ApiResponse::error('', $th->getMessage(), HttpStatusCode::NOT_FOUND);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $time = $this->timeService->editTimes($id);
            return ApiResponse::success(new TimeResource($time));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(__('crud.not_found'), [], HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
            return ApiResponse::error($th->getMessage(), [], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(int $id, UpdateTimeRequest $updateTimeRequest)
    {
        try {
            $this->timeService->updateTime($id, $updateTimeRequest->validated());
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
    public function destroy(int $id)
    {
        try {
            $this->timeService->deleteTime($id);
            return ApiResponse::success([], __('crud.deleted'));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(__('crud.not_found'), [], HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
           return ApiResponse::error($th->getMessage(), [], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
