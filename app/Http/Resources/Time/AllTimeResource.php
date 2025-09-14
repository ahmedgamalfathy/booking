<?php

namespace App\Http\Resources\Time;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllTimeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
     return[
          'timeId'=> $this->id,
        //   'serviceId'=> $this->service_id,
          'startTime'=> Carbon::parse($this->start_time)->format('H:i'),
          'endTime'=> Carbon::parse($this->end_time)->format('H:i'),
        //   'dayOfWeek'=>$this->day_of_week,
          'sessionTime'=>$this->session_time
        ];
    }
}
