<?php

namespace App\Http\Resources\Client\CustomSelect;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'clientId' => $this->id,
            'name' => $this->name,
           'phone' => optional(
            $this->phones?->where('is_main', 1)->first()
            ?? $this->phones?->first()
            )->phone ?? "",
        ];
    }
}
