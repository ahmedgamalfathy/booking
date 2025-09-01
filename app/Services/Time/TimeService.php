<?php
namespace App\Services\Time;
use Carbon\Carbon;
use App\Models\Time\Time;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TimeService
{
   public function allTimes()
   {
     $times= QueryBuilder::for(Time::class)->get();
     return $times;
   }
    public function editTimes($id)
    {
        $time= Time::find($id);
        if(!$time){
            throw new ModelNotFoundException("Time with ID {$id} not found.");
        }
        return $time;
    }
    public function createTime(array $data)
    {
        if($this->isTimeConflict($data['serviceId'],$data['dayOfWeek'],$data['startTime'],$data['endTime']))
        {
              throw new \Exception('There is already a conflicting time on this day.');
        }
         return Time::create([
            'service_id' => $data['serviceId'],
            'start_time' => $data['startTime'],
            'end_time' => $data['endTime'],
            'day_of_week' => $data['dayOfWeek'],
            'session_time' => $data['sessionTime'],
         ]);
    }
    public function updateTime(int $id, array $data)
    {
        $time = Time::find($id);
        if(!$time){
            throw new ModelNotFoundException("Time with ID {$id} not found.");
        }
       if($this->isTimeConflict($data['serviceId'],$data['dayOfWeek'],$data['startTime'],$data['endTime']))
        {
              throw new \Exception('There is already a conflicting time on this day.');
        }
        $time->service_id = $data['serviceId'];
        $time->start_time = $data['startTime'];
        $time->end_time = $data['endTime'];
        $time->day_of_week = $data['dayOfWeek'];
        $time->session_time = $data['sessionTime'];
        $time->save();
        return $time;
    }
    public function deleteTime(int $id)
    {
        $time = Time::find($id);
        if(!$time){
            throw new ModelNotFoundException("Time with ID {$id} not found.");
        }
        return $time->delete();
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
    public function isTimeConflict($serviceId, $dayOfWeek, $startTime, $endTime)
    {
        return Time::where('service_id', $serviceId)
            ->where('day_of_week', $dayOfWeek)
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
