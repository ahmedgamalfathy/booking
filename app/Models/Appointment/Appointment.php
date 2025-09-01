<?php

namespace App\Models\Appointment;

use App\Models\Email\Email;
use App\Models\Phone\Phone;
use App\Models\Client\Client;
use App\Models\Service\Service;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $guarded = [];
    public function service(){
        return $this->belongsTo(Service::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
        public function email()
    {
        return $this->belongsTo(Email::class);
    }
        public function phone()
    {
        return $this->belongsTo(Phone::class);
    }
}
