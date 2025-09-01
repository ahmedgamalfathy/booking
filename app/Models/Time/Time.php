<?php

namespace App\Models\Time;

use App\Enums\IsAailableEnum;
use App\Models\Service\Service;
use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    protected $guarded = [];

    protected $casts =[
        'is_available' => IsAailableEnum::class,
    ];
}
