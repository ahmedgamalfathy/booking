<?php

namespace App\Http\Resources\Appointment;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {//appointments(service_id, client_id , phone_id, email_id , date, start_at , end_at)
        return[
          'appointmentId'=>$this->id,
          'appointmentDate'=>$this->date,
          'serviceId'=>$this->service->id,
          'serviceName'=>$this->service->name,
          'serviceColor'=>$this->service->color,
          'client'=>[
            'clientId'=>$this->client->id,
            'clientName'=>$this->client->name,
            'clientPhone' => optional($this->client->phones()->find($this->phone_id))->phone ?? "",
            'clientEmail' => optional($this->client->emails()->find($this->email_id))->email ?? "",
            ],
          'startAt'=>Carbon::parse($this->start_at)->format('H:i'),
          'endAt'=>Carbon::parse($this->end_at)->format('H:i'),
          'note'=>$this->note??""
        ];
    }
}
