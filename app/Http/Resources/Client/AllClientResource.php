<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Client\ClientEmails\AllClientEmailResource;
use App\Http\Resources\Client\ClientContact\Relation\AllClientContactResource;


class AllClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//name ,notes
        return [
            'clientId' => $this->id,
            'name' => $this->name,
            'phone' =>$this->phones?($this->phones->where('is_main', 1)->first()?$this->phones->where('is_main', 1)->first()->phone:$this->phones->first()->phone) :"",
            'email'=> $this->emails?($this->emails->where('is_main', 1)->first()?$this->emails->where('is_main', 1)->first()->email:$this->emails->first()->email) :"",
        ];
    }
}
