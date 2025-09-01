<?php

namespace App\Http\Resources\Service;


use Illuminate\Http\Request;

use App\Http\Resources\Time\RelationServiceAll\TimeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AllServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
     return[
          'serviceId'=>$this->id,
          'path'=> $this->path,
          'name'=> $this->name,
          'color'=> $this->color,
          'price'=>$this->price>0 ?$this->price:"",
          'status'=>$this->status,
          'type'=>$this->type,
          'times'=> TimeResource::collection($this->times->unique('day_of_week')->values())
        ];
    }
}
