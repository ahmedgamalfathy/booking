<?php
namespace App\Services\Exception;
use Carbon\Carbon;
use App\Models\Exception\Exception;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExceptionService
{
   public function allExceptions()
   {
      $exceptions= QueryBuilder::for(Exception::class)->get();
       return $exceptions;
   }
    public function editExceptions($id)
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
}
