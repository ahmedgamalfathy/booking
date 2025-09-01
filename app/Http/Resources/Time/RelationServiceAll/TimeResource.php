<?php

namespace App\Http\Resources\Time\RelationServiceAll;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'timeId' => $this->id,
            'dayOfWeek' => $this->day_of_week,
        ];
    }
}
