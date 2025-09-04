<?php
namespace App\Services\Exception;

use App\Enums\IsAailableEnum;
use Carbon\Carbon;
use App\Models\Time\Time;
use App\Models\Service\Service;
use App\Models\Exception\Exception;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Appointment\Appointment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rules\Enum;

class ExceptionService
{
   public function allExceptions()
   {
      $exceptions= QueryBuilder::for(Exception::class)->get();
       return $exceptions;
   }
    public function editExceptions(int $id)
    {
        $exception= Exception::find($id);
        if(!$exception){
            throw new ModelNotFoundException("Time with ID {$id} not found.");
        }
        return $exception;
    }
    public function createException(array $data)
    {
       if($this->isExceptionConflict($data['serviceId'],$data['date'],$data['startTime'],$data['endTime']))
        {
              throw new \Exception('There is already a conflicting time on this day.');
        }
         $this->isExceptionConflictWithTime($data['serviceId'],$data['date'],$data['startTime'],$data['endTime'],$data['isAvailable']);
         return Exception::create([
            'is_available' => $data['isAvailable'],
            'service_id' => $data['serviceId'],
            'start_time' => $data['startTime'],
            'end_time' => $data['endTime'],
            'date' => $data['date'],
            'session_time' => $data['sessionTime'],
         ]);
    }
    public function updateException(int $id, array $data)
    {
        $exception = Exception::find($id);
        if(!$exception){
            throw new ModelNotFoundException("Time with ID {$id} not found.");
        }
       if($this->isExceptionConflict($data['serviceId'],$data['date'],$data['startTime'],$data['endTime']))
        {
              throw new \Exception('There is already a conflicting time on this day.');
        }
        $this->isExceptionConflictWithTime($data['serviceId'],$data['date'],$data['startTime'],$data['endTime'] ,$data['isAvailable']);
        $exception->is_available = $data['isAvailable'];
        $exception->service_id = $data['serviceId'];
        $exception->start_time = $data['startTime'];
        $exception->end_time = $data['endTime'];
        $exception->date = $data['date'];
        $exception->session_time = $data['sessionTime'];
        $exception->save();
        return $exception;
    }
    public function deleteException(int $id)
    {
        $exception = Exception::find($id);
        if(!$exception){
            throw new ModelNotFoundException("Time with ID {$id} not found.");
        }
        return $exception->delete();
    }

    public function formatTime($timeString, $format = 'H:i')
    {
        return Carbon::createFromFormat('H:i', $timeString)->format($format);
    }
    public function isTimeInRange($time, $start, $end)
    {
        $time = Carbon::createFromFormat('H:i:s', $time);
        $start = Carbon::createFromFormat('H:i:s', $start);
        $end = Carbon::createFromFormat('H:i:s', $end);
        return $time->between($start, $end);
    }
    public function getCurrentTime($format = 'H:i:s')
    {
        return Carbon::now()->format($format);
    }
        public function isExceptionConflict($serviceId, $date, $startTime, $endTime)
    {
        return Exception::where('service_id', $serviceId)
            ->where('date', $date)
            ->where(function($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->exists();
    }
    public function isExceptionConflictWithTime(int $serviceId, string $date, string $startTime, string $endTime ,  $isAvailable){
   $service = Service::find($serviceId);
    if (!$service) {
        throw new ModelNotFoundException("Service with ID {$serviceId} not found.");
    }

    // Check for time slot conflicts in appointments
    $conflict = Appointment::where('service_id', $serviceId)
        ->where('date', $date)
        ->where(function ($query) use ($startTime, $endTime) {
            $query->whereBetween('start_at', [$startTime, $endTime])
                  ->orWhereBetween('end_at', [$startTime, $endTime]);
        })->exists();

    if ($conflict) {
        throw new \Exception("This time slot is already booked for the selected service.");
    }
    // Check for exceptions (closed times)

    if ($isAvailable == 1){
            $dateToDayOfWeek= Carbon::parse($date);
                $time = Time::where('service_id', $serviceId)
                    ->where('day_of_week', $dateToDayOfWeek->dayOfWeek)
                    ->where(function ($query) use ($startTime, $endTime) {
                        $query->whereBetween('start_time', [$startTime, $endTime])
                            ->OrWhereBetween('end_time',[$startTime, $endTime]);
                    })
                    ->exists();

                if ($time) {
                    throw new \Exception("This time slot Exception is not available find to service Time.");
            }
    }
    return true;
    }
}
