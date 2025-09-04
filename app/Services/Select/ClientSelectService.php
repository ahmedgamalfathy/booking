<?php
namespace App\Services\Select;

use App\Models\Address\Address;
use App\Models\Email\Email;
use App\Models\Phone\Phone;
use App\Models\Client\Client;
use App\Models\Client\ClientEmail;
use App\Models\Client\ClientPhone;
use App\Models\Client\ClientAdrress;


class ClientSelectService{
    public function getClients(){
        $clients = Client::all(['id as value', 'name as label']);
        return $clients;
    }
    public function getClientEmails($clientId){
         $clientEmails = Email::where('model_id',$clientId)->where('model_type','App\Models\Client\Client')->get(['id as value', 'email as label']);
        return $clientEmails;
    }
    public function getClientPhones($clientId){
         $clientPhones = Phone::where('model_id',$clientId)->where('model_type','App\Models\Client\Client')->get(['id as value', 'phone as label']);;
    return $clientPhones;
    }
    public function getClientAddress($clientId){
         $clientAddress = Address::where('model_id',$clientId)->where('model_type','App\Models\Client\Client')->get(['id as value', 'address as label']);;
    return $clientAddress;
}
}

