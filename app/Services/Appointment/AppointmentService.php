<?php
namespace App\Services\Appointment;

use App\Enums\AppointmentStatusEnum;
use App\Models\User;
use App\Enums\IsAailableEnum;
use App\Models\Service\Service;
use App\Models\Exception\Exception;
use Illuminate\Support\Facades\Mail;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Mail\SendStatusAppointToClient;
use App\Models\Appointment\Appointment;
use App\Filters\Appointment\FilterByPeriod;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AppointmentService {

public function allAppointments(){
     $appointment= QueryBuilder::for(Appointment::class) ->allowedFilters([
            AllowedFilter::custom('day', new FilterByPeriod),
            AllowedFilter::custom('week', new FilterByPeriod),
            AllowedFilter::custom('month', new FilterByPeriod),
            AllowedFilter::custom('custom', new FilterByPeriod),
            AllowedFilter::exact('clientId', 'client_id'),
            AllowedFilter::exact('serviceId', 'service_id'),
     ])->where('status',AppointmentStatusEnum::APPROVED)->get();
     return $appointment;
}
public function editAppointments(int $id){
     $appointment= Appointment::find($id);
        if(!$appointment){
            throw new ModelNotFoundException("Time with ID {$id} not found.");
        }
        return $appointment;
}

public function createAppointment(array $data){
     $this->checkServiceAvailability( $data['serviceId'],$data['date'],$data['startTime'],$data['endTime']);
         $appointment = Appointment::create([
            'service_id' => $data['serviceId'],
            'client_id' => $data['clientId'],
            'phone_id'=>$data['phoneId']??null,
            'email_id'=>$data['emailId']??null,
            'start_at' => $data['startTime'],
            'end_at' => $data['endTime'],
            'date' => $data['date'],
            'note'=>$data['note']??null
         ]);
         if ($appointment->email) {
         Mail::to($appointment->email)->send(new SendStatusAppointToClient($appointment->client, "pending"));
         }
         $users = User::role('super admin')->get();
         foreach($users as $user){
            Mail::to($user->email)->send(new SendStatusAppointToClient($appointment->client, "pending"));
         }
         return $appointment;
}
public function updateAppointment(int $id , array $data){
     $this->checkServiceAvailability( $data['serviceId'],$data['date'],$data['startTime'],$data['endTime'],$id);
         $appointment= Appointment::find($id);
        if(!$appointment){
            throw new ModelNotFoundException("Time with ID {$id} not found.");
        }
        $appointment->service_id = $data['serviceId'];
        $appointment->client_id = $data['clientId'];
        $appointment->phone_id = $data['phoneId']??null;
        $appointment->email_id = $data['emailId']??null;
        $appointment->start_at = $data['startTime'];
        $appointment->end_at = $data['endTime'];
        $appointment->date = $data['date'];
        $appointment->note = $data['note']??null;
        $appointment->save();
        return $appointment;
}
public function deleteAppointment(int $id){
      $appointment= Appointment::find($id);
        if(!$appointment){
            throw new ModelNotFoundException("Time with ID {$id} not found.");
        }
      $appointment->delete();
}
public function restoreAppointment($id)
    {
        $client = Appointment::withTrashed()->findOrFail($id);
        $client->restore();
    }

    public function forceDeleteAppointment($id)
    {
        $client = Appointment::withTrashed()->findOrFail($id);
        $client->forceDelete();
    }
public function appointmentView(int $id){
         $appointment= Appointment::find($id);
        if(!$appointment){
            throw new ModelNotFoundException("Time with ID {$id} not found.");
        }
        return $appointment;
}
public function checkServiceAvailability(int $serviceId, string $date, string $startTime, string $endTime , $ignoreAppointmentId = null)
{
    $service = Service::find($serviceId);
    if (!$service) {
        throw new ModelNotFoundException("Service with ID {$serviceId} not found.");
    }

    // Check for time slot conflicts in appointments
    $conflictQuery = Appointment::where('service_id', $serviceId)
        ->where('date', $date)
        ->where(function ($query) use ($startTime, $endTime) {
            // $query->whereBetween('start_at', [$startTime, $endTime])
            //       ->orWhereBetween('end_at', [$startTime, $endTime]);
        $query->where('start_at', '<', $endTime)
            ->where('end_at', '>', $startTime);
        });
    if ($ignoreAppointmentId) {
        $conflictQuery->where('id', '!=', $ignoreAppointmentId);
    }
   $conflict = $conflictQuery->exists();
    if ($conflict) {
        throw new \Exception("This time slot is already booked for the selected service.");
    }

    // Check for exceptions (closed times)
    $exception = Exception::where('service_id', $serviceId)
        ->where('date', $date)
        ->where(function ($query) use ($startTime, $endTime) {
                // $query->whereBetween('start_time', [$startTime, $endTime])
                //   ->orWhereBetween('end_time', [$startTime, $endTime]);
            $query->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
        })
        ->where('is_available', IsAailableEnum::UNAVAILABLE)
        ->exists();

    if ($exception) {
        throw new \Exception("This time slot is not available due to service exception.");
    }

    return true;
}


}
