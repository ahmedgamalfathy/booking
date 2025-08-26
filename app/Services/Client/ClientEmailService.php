<?php
namespace App\Services\Client;

use App\Enums\IsMainEnum;
use App\Models\Client\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ClientEmailService {
    public function allClientEmails(int $clientId)
    {
        $client = Client::find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        return $client->emails;
    }

    public function createClientEmail($clientId,array $data)
    {
        $client = Client::find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $client->emails()->create([
            'email' => $data['email'],
            'is_main' =>IsMainEnum::from($data['isMain'])->value ,
        ]);
        if($data['isMain']== 1){
            $client->emails()->where('id','!=',$client->emails->last()->id)->update([
             'is_main'=>  0
            ]);
        }
        return 'Created';
    }

    public function editClientEmail(int $clientId,int $emailId)
    {
        $client = Client::find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $clientEmail = $client->emails()->findOrFail($emailId);
        return $clientEmail;
    }
    public function updateClientEmail(int $clientId,int $emailId,array $data)
    {
        $client = Client::find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $ClientEmail=$client->emails()->findOrFail($emailId);
        $ClientEmail->update([
            'email' => $data['email'],
            'is_main' =>IsMainEnum::from($data['isMain'])->value ,
        ]);
        if($data['isMain']== 1){
            $client->emails()->where('id','!=',$ClientEmail->id)->update([
             'is_main'=>  0
            ]);
        }
        return $ClientEmail;
    }

    public function deleteClientEmail(int $clientId,int $emailId)
    {
        $client = Client::find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $clientEmail=$client->emails()->findOrFail($emailId);
        $clientEmail->delete();
    }
    public function restoreClientEmail(int $clientId,int $emailId)
    {
        $client = Client::withTrashed()->find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $clientEmail=$client->emails()->withTrashed()->findOrFail($emailId);
        $clientEmail->restore();
    }
    public function forceDeleteClientEmail(int $clientId,int $emailId)
    {
        $client = Client::withTrashed()->find($clientId);
        if(!$client){
            throw new ModelNotFoundException();
        }
        $clientEmail=$client->emails()->withTrashed()->findOrFail($emailId);
        $clientEmail->forceDelete();
    }

}


