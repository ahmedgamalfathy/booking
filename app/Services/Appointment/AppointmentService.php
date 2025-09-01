<?php
namespace App\Services\Appointment;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Models\Appointment\Appointment;
use App\Filters\Appointment\FilterAppointment;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AppointmentService {

public function allAppointments(){
     $perPage = request()->get('pageSize', 10);
     $appointment= QueryBuilder::for(Appointment::class) ->allowedFilters([
            // AllowedFilter::custom('search', new FilterAppointment()),
            // AllowedFilter::exact('type', 'param_id')
     ])->paginate($perPage);
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
         return Appointment::create([
            'service_id' => $data['serviceId'],
            'client_id' => $data['clientId'],
            'phone_id'=>$data['phoneId'],
            'email_id'=>$data['emailId']??null,
            'start_at' => $data['startTime'],
            'end_at' => $data['endTime'],
            'date' => $data['date'],
         ]);
}
public function updateAppointment(int $id , array $data){
         $appointment= Appointment::find($id);
        if(!$appointment){
            throw new ModelNotFoundException("Time with ID {$id} not found.");
        }
        $appointment->service_id = $data['serviceId'];
        $appointment->client_id = $data['clientId'];
        $appointment->phone_id = $data['phoneId'];
        $appointment->email_id = $data['emailId']??null;
        $appointment->start_at = $data['startTime'];
        $appointment->end_at = $data['endTime'];
        $appointment->date = $data['date'];
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

}
