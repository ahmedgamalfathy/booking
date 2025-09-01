<?php

namespace App\Models\Exception;

use App\Enums\IsAailableEnum;
use Illuminate\Database\Eloquent\Model;

class Exception extends Model
{
    protected $guarded = [];
    protected $casts = [
        'is_available' => IsAailableEnum::class,
    ];
}
