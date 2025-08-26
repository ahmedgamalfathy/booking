<?php

namespace App\Http\Resources\Client\ClientAddress;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllClientAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


        return [
            'clientAddressId' => $this->id,
            'address' => $this->address,
            'isMain' => $this->is_main,
            'city' =>$this->city??"",

        ];
    }
}
