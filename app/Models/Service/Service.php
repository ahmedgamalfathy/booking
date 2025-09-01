<?php

namespace App\Models\Service;

use App\Enums\TypeEnum;
use App\Enums\StatusEnum;
use App\Models\Time\Time;
use App\Enums\DayOfWeekEnum;
use App\Models\Exception\Exception;
use App\Models\Appointment\Appointment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Service extends Model
{
    protected $guarded =[];
    protected $casts = [
        'status' => StatusEnum::class,
        'day_of_week'=> DayOfWeekEnum::class,
        'type' => TypeEnum::class,
    ];
    public function times()
    {
        return $this->hasMany(Time::class);
    }
    public function exceptions()
    {
        return $this->hasMany(Exception::class);
    }
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
        protected function path(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Storage::disk('public')->url($value) : "",
        );
    }
}
