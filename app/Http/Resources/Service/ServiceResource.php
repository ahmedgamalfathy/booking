<?php

namespace App\Http\Resources\Service;

use App\Http\Resources\Exception\AllExceptionResource;
use Illuminate\Http\Request;
use App\Http\Resources\Time\AllTimeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
          'serviceId' => $this->id,
          'path'=> $this->path,
          'name'=> $this->name,
          'color'=> $this->color,
          'price'=>$this->price>0 ?$this->price:"",
          'status'=>$this->status,
          'type'=>$this->type,
         'times' => $this->whenLoaded('times', function () {
                return $this->times
                    ->groupBy('day_of_week')
                    ->map(function ($group, $day) {
                        return [
                            'dayOfWeek' => $day,
                            'times' => AllTimeResource::collection($group),
                        ];
                    })->values();
         }),
          'exceptions'=> AllExceptionResource::collection($this->whenLoaded('exceptions'))
        ];
    }
}
