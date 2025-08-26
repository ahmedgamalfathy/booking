<?php
 namespace App\Services\Client;

use App\Enums\IsMainEnum;
use App\Models\Client\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;

    class ClientAddressService
    {
       public function allClientAddress(int $clientId)
    {
        $client = Client::find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        return $client->addresses;
    }

    public function createClientAddress(int $clientId,array $data)
    {
        $client = Client::find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $client->addresses()->create([
            'address' => $data['address'],
            'city' => $data['city'] ?? null,
            'is_main' =>IsMainEnum::from($data['isMain'])->value ,
        ]);
        if($data['isMain']== 1){
            $lastAddress = $client->addresses()->orderByDesc('id')->first();
            $client->addresses()->where('id','!=', $lastAddress ? $lastAddress->id : null)->update([
                'is_main'=>  0
            ]);
        }
        return 'Created';
    }

    public function editClientAddress(int $clientId,int $addressId)
    {
        $client = Client::find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $clientAddress = $client->addresses()->findOrFail($addressId);
        return $clientAddress;
    }
    public function updateClientAddress(int $clientId,int $addressId,array $data)
    {
        $client = Client::find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $clientAddress=$client->addresses()->findOrFail($addressId);
        $clientAddress->update([
            'address' => $data['address'],
            'city' => $data['city'] ?? null,
            'is_main' =>IsMainEnum::from($data['isMain'])->value ,
        ]);
        if($data['isMain']== 1){
            $client->addresses()->where('id','!=',$clientAddress->id)->update([
             'is_main'=>  0
            ]);
        }
        return $clientAddress;
    }

    public function deleteClientAddress(int $clientId,int $addressId)
    {
        $client = Client::find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $clientAddress=$client->addresses()->findOrFail($addressId);
        $clientAddress->delete();
    }
    public function restoreClientAddress(int $clientId,int $addressId)
    {
        $client = Client::withTrashed()->find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $clientAddress=$client->addresses()->withTrashed()->findOrFail($addressId);
        $clientAddress->restore();
    }
    public function forceDeleteClientAddress(int $clientId,int $addressId)
    {
        $client = Client::withTrashed()->find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $clientAddress=$client->addresses()->withTrashed()->findOrFail($addressId);
        $clientAddress->forceDelete();
    }
    }
