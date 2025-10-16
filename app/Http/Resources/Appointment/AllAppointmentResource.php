<?php

namespace App\Http\Resources\Appointment;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllAppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
          'appointmentId'=>$this->id,
          'appointmentDate'=>$this->date,
          'serviceName'=>$this->service->name,
          'serviceColor'=>$this->service->color,
          'clientName'=>$this->client->name??"",
          'startAt'=>Carbon::parse($this->start_at)->format('H:i'),
          'endAt'=>Carbon::parse($this->end_at)->format('H:i'),
        ];
    }
}
