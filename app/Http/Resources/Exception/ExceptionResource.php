<?php

namespace App\Http\Resources\Exception;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExceptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
          'exceptionId'=> $this->id,
          'serviceId'=> $this->service_id,
          'isAvailable'=> $this->is_available,
          'startTime'=> Carbon::parse($this->start_time)->format('H:i'),
          'endTime'=> Carbon::parse($this->end_time)->format('H:i'),
          'date'=>$this->date,
        ];
    }
}
