<?php
namespace App\Http\Controllers\API\V1\Dashboard\Appointment;

use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Enums\AppointmentStatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendStatusAppointToClient;
use App\Models\Appointment\Appointment;
use App\Enums\ResponseCode\HttpStatusCode;
use App\Http\Resources\Appointment\AppointmentPendingResource;


class BendingController extends Controller
{


    public function  index()
    {
        // dd(now()->toDateTimeString());
       $appointmentService = Appointment::where(function($query) {
        $query->whereNotIn('status', [AppointmentStatusEnum::APPROVED, AppointmentStatusEnum::CANCELLED])
              ->orWhere(function($subQuery) {
                  $subQuery->where('status', AppointmentStatusEnum::PENDING)
                           ->where('created_at', '>=', now()->subHours(24));
              });
    })->get();
       return ApiResponse::success( AppointmentPendingResource::collection($appointmentService));
    }
    public function changeStatus(Request $request,$id){
        $data = $request->validate([
            'action'=>['required',Rule::in('approved','cancelled')]
        ]);
         $appointment = Appointment::find($id);
        if(!$appointment){
            return ApiResponse::error( __('crud.not_found'),[],HttpStatusCode::NOT_FOUND);
        }
        if( $appointment->status != AppointmentStatusEnum::PENDING->value ){
            return ApiResponse::error( "Appointment Status not pending",[],HttpStatusCode::NOT_FOUND);
        }
        if( $appointment->created_at <= now()->subHours(24)){
            return ApiResponse::error( "The appointment has expired.",[],HttpStatusCode::NOT_FOUND);
        }
        switch ($data['action']) {
            case 'approved':
                $this->changeStatusToApproved( $id);
                break;

            case 'cancelled':
                $this->changeStatusToCancelled($id);
                break;
        }
         return ApiResponse::success([], __('crud.updated'));
    }
    public function changeStatusToApproved(int $id)
    {
        $appointmentService = Appointment::find($id);
        if(!$appointmentService & $appointmentService->status != AppointmentStatusEnum::PENDING & $appointmentService->created_at >= now()->subHours(24)){
            return ApiResponse::error( __('crud.not_found'),[],HttpStatusCode::NOT_FOUND);
        }
      $appointmentService->status = AppointmentStatusEnum::APPROVED;
        $appointmentService->save();
        if ($appointmentService->email) {
        Mail::to($appointmentService->email)->send(new SendStatusAppointToClient($appointmentService->client, "approved"));
        }
        $users = User::role('super admin')->get();
        foreach($users as $user){
            Mail::to($user->email)->send(new SendStatusAppointToClient($appointmentService->client, "approved"));
        }
        return ApiResponse::success([], __('crud.updated'));
    }
    public function changeStatusToCancelled(int $id)
    {
        $appointmentService = Appointment::find($id);
        $appointmentService->status = AppointmentStatusEnum::CANCELLED;
        if(!$appointmentService && $appointmentService->status != AppointmentStatusEnum::PENDING && $appointmentService->created_at >= now()->subHours(24)){
            return ApiResponse::error( __('crud.not_found'),[],HttpStatusCode::NOT_FOUND);
        }
        $appointmentService->save();
        if ($appointmentService->email) {
        Mail::to($appointmentService->email)->send(new SendStatusAppointToClient($appointmentService->client, "cancelled"));
        }
        $users = User::role('super admin')->get();
        foreach($users as $user){
            Mail::to($user->email)->send(new SendStatusAppointToClient($appointmentService->client, "cancelled"));
        }
        return ApiResponse::success([], __('crud.updated'));
    }
    public function bulkAction(Request $request)
    {
        // $data = $request->validate(['ids' => 'required|array|exists:appointments,id']);
        // $appointmentService = Appointment::whereIn('id',$data['ids'])->get();
        $appointmentService = Appointment::where(function($query) {
        $query->whereNotIn('status', [AppointmentStatusEnum::APPROVED, AppointmentStatusEnum::CANCELLED])
              ->orWhere(function($subQuery) {
                  $subQuery->where('status', AppointmentStatusEnum::PENDING)
                  ->where('created_at', '>=', now()->subHours(24));
        });
      })->get();
        foreach($appointmentService as $appointment){
            if(!$appointment && $appointment->status != AppointmentStatusEnum::PENDING && $appointment->created_at > now()->subHours(24)){
                return ApiResponse::error( __('crud.not_found'),[],HttpStatusCode::NOT_FOUND);
            }
            $appointment->status = AppointmentStatusEnum::APPROVED;
            $appointment->save();
            if ($appointment->email) {
            Mail::to($appointment->email)->send(new SendStatusAppointToClient($appointment->client, "approved"));
            }
            $users = User::role('super admin')->get();
            foreach($users as $user){
                Mail::to($user->email)->send(new SendStatusAppointToClient($appointment->client, "approved"));
            }
        }
        return ApiResponse::success([], __('crud.updated'));
    }
}
