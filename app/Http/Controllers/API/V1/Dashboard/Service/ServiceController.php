<?php

namespace App\Http\Controllers\API\V1\Dashboard\Service;


use Exception;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Utils\PaginateCollection;
use App\Services\Time\TimeService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\ResponseCode\HttpStatusCode;
use App\Services\Exception\ExceptionService;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Resources\Service\ServiceResource;
use App\Services\ServiceHandler\ServiceHandler;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Requests\Service\CreateServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Http\Resources\Service\ServiceViewResource;
use App\Http\Resources\Service\AllServiceCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ServiceController extends Controller implements HasMiddleware
{
           protected $serviceHandler;
           protected $timeService;
           protected $exceptionService;

    public function __construct(ServiceHandler $serviceHandle,TimeService $timeService ,ExceptionService $exceptionService)
    {
        $this->serviceHandler = $serviceHandle;
        $this->timeService = $timeService;
        $this->exceptionService = $exceptionService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth:api'),
            // new Middleware('permission:all_services', only:['index']),
            // new Middleware('permission:create_service', only:['create']),
            // new Middleware('permission:edit_service', only:['edit']),
            // new Middleware('permission:update_service', only:['update']),
            // new Middleware('permission:destroy_service', only:['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $servcies = $this->serviceHandler->allServices();
        return ApiResponse::success(new AllServiceCollection (PaginateCollection::paginate($servcies,$request->pageSize?$request->pageSize:10)));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateServiceRequest $createServiceRequest)
    {
        DB::beginTransaction();
        try{
            $data = $createServiceRequest->validated();
            $service=$this->serviceHandler->createService($data);
            if (!empty($data['days'])) {
                foreach ($data['days'] as $day) {
                    foreach ($day['times'] as $time) {
                        $time['serviceId']  = $service->id;
                        $time['dayOfWeek']  = $day['dayOfWeek'];
                        $this->timeService->createTime($time);
                    }
                }
            }
           if (!empty($data['exceptions'])) {
                foreach ($data['exceptions'] as $exception) {
                    $exception['serviceId']=$service->id;
                    $this->exceptionService->createException($exception);
                }
            }
        DB::commit();
            return ApiResponse::success([], __('crud.created'));
        }catch(ModelNotFoundException $e){
            return ApiResponse::error(__('crud.not_found'),[],HttpStatusCode::NOT_FOUND);
        }catch(\Exception $e){
            return ApiResponse::error($e->getMessage(),[],HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try{
            $service = $this->serviceHandler->editServices($id);
            return ApiResponse::success(new ServiceResource($service));
        }catch(ModelNotFoundException $e){
            return ApiResponse::error(__('crud.not_found'),[],HttpStatusCode::NOT_FOUND);
        }catch(\Exception $e){
            return ApiResponse::error($e->getMessage(),[],HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(int $id,UpdateServiceRequest $updateUserRequest)
    {
        DB::beginTransaction();
        try{
            $data = $updateUserRequest->validated();
            $this->serviceHandler->updateService($id,$data);
            DB::commit();
            return ApiResponse::success([], __('crud.updated'));
        }catch(ModelNotFoundException $e){
            return ApiResponse::error(__('crud.not_found'),[],HttpStatusCode::NOT_FOUND);
        }catch(\Exception $e){
            return ApiResponse::error($e->getMessage(),[],HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try{
             $this->serviceHandler->deleteService($id);
             return ApiResponse::success([], __('crud.deleted'));
        }catch(ModelNotFoundException $e){
            return ApiResponse::error(__('crud.not_found'),[],HttpStatusCode::NOT_FOUND);
        }catch(\Exception $e){
            return ApiResponse::error($e->getMessage(),[],HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

    }
    public function serviceView(int $id)
    {
         try{
            $service = $this->serviceHandler->serviceView($id);
            return ApiResponse::success(new ServiceViewResource($service));
        }catch(ModelNotFoundException $e){
            return ApiResponse::error(__('crud.not_found'),[],HttpStatusCode::NOT_FOUND);
        }catch(\Exception $e){
            return ApiResponse::error($e->getMessage(),[],HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
