<?php

namespace App\Http\Controllers\API\V1\Dashboard\Appointment;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Utils\PaginateCollection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Enums\ResponseCode\HttpStatusCode;
use Illuminate\Routing\Controllers\Middleware;
use App\Services\Appointment\AppointmentService;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Resources\Appointment\AppointmentResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Appointment\CreateAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Http\Resources\Appointment\AllAppointmentCollection;

class AppointmentController extends Controller implements HasMiddleware
{
     protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
      $this->appointmentService = $appointmentService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth:api'),
            // new Middleware('permission:all_appointments', only:['index']),
            // new Middleware('permission:create_appointment', only:['create']),
            // new Middleware('permission:edit_appointment', only:['edit']),
            // new Middleware('permission:update_appointment', only:['update']),
            // new Middleware('permission:destroy_appointment', only:['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $appointments = $this->appointmentService->allAppointments();
        return  ApiResponse::success(new AllAppointmentCollection(PaginateCollection::paginate($appointments, $request->pageSize?$request->pageSize:10)));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAppointmentRequest $createAppointmentRequest)
    {
         try {
            DB::beginTransaction();
            $this->appointmentService->createAppointment($createAppointmentRequest->validated());
            DB::commit();
            return ApiResponse::success([], __('crud.created'));
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
            $appointment = $this->appointmentService->editAppointments($id);
            return ApiResponse::success( new AppointmentResource ($appointment));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(__('crud.not_found'), [], HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
            return ApiResponse::error($th->getMessage(), [], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $updateAppointmentRequest, int $id)
    {
        try {
            $this->appointmentService->updateAppointment($id, $updateAppointmentRequest->validated());
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
            $this->appointmentService->deleteAppointment($id);
            return ApiResponse::success([], __('crud.deleted'));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(__('crud.not_found'), [], HttpStatusCode::NOT_FOUND);
        }catch (\Throwable $th) {
           return ApiResponse::error($th->getMessage(), [], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
