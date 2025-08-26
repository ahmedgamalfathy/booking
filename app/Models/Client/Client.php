<?php

namespace App\Models\Client;

use App\Models\Email\Email;
use App\Models\Phone\Phone;
use App\Models\Address\Address;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
     protected $guarded = [];
      use HasFactory,SoftDeletes;
    public function emails()
    {
        return $this->morphMany(Email::class, 'model');
    }

    public function phones()
    {
        return $this->morphMany(Phone::class, 'model');
    }

    public function addresses()
    {
        return $this->morphMany(Address::class, 'model');
    }
}
