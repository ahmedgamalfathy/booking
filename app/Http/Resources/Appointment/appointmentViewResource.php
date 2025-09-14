<?php

namespace App\Http\Resources\Appointment;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class appointmentViewResource extends JsonResource
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
          'serviceName'=>$this->service->name,
          'client'=>[
              'clientName'=>$this->client->name,
              'clientPhone'=>$this->client->phones()->findOrFail($this->phone_id)->phone??"" ,
              'clienEmail'=>$this->client->emails()->findOrFail($this->email_id)->email??"",
            ],
          'startAt'=>Carbon::parse($this->start_at)->format('H:i'),
          'endAt'=>Carbon::parse($this->end_at)->format('H:i'),
          'note'=>$this->note??""
        ];
    }
}
