<?php
namespace App\Services\Client;

use App\Enums\IsMainEnum;
use App\Models\Client\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ClientPhoneService
{
   public function allClientPhones(int $clientId)
    {
        $client = Client::find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        return $client->phones;
    }

    public function createClientPhone(int $clientId,array $data)
    {
        $client = Client::find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $client->phones()->create([
            'phone' => $data['phone'],
            'country_code' => $data['countryCode'],
            'is_main' =>IsMainEnum::from($data['isMain'])->value ,
        ]);
        if($data['isMain']== 1){
            $lastPhone= $client->phones()->orderByDesc('id')->first();
            $client->phones()->where('id','!=',$lastPhone?$lastPhone->id: null)->update([
             'is_main'=>  0
            ]);
        }
        return 'Created';
    }

    public function editClientPhone(int $clientId,int $phoneId)
    {
        $client = Client::find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $clientPhone = $client->phones()->findOrFail($phoneId);
        if(!$clientPhone){
            throw new ModelNotFoundException();
        }
        return $clientPhone;
    }
    public function updateClientPhone(int $clientId,int $phoneId,array $data)
    {
        $client = Client::find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $clientPhone = $client->phones()->findOrFail($phoneId);
        $clientPhone->update([
            'phone' => $data['phone'],
            'country_code' => $data['countryCode'],
            'is_main' =>IsMainEnum::from($data['isMain'])->value ,
        ]);
        if($data['isMain']== 1){
            $client->phones()->where('id','!=',$clientPhone->id)->update([
             'is_main'=>  0
            ]);
        }
        return $clientPhone;
    }

    public function deleteClientPhone(int $clientId,int $phoneId)
    {
        $client = Client::find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $clientPhone=$client->phones()->findOrFail($phoneId);
        $clientPhone->delete();
    }
    public function restoreClientPhone(int $clientId,int $phoneId)
    {
        $client = Client::withTrashed()->find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $clientPhone=$client->phones()->withTrashed()->findOrFail($phoneId);
        $clientPhone->restore();
    }
    public function forceDeleteClientPhone(int $clientId,int $phoneId)
    {
        $client = Client::withTrashed()->find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $clientPhone=$client->phones()->withTrashed()->findOrFail($phoneId);
        $clientPhone->forceDelete();
    }
}
