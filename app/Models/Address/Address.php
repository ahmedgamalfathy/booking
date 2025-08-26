<?php

namespace App\Models\Address;

use App\Enums\IsMainEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
      use HasFactory,SoftDeletes;
        protected $guarded = [];
       protected $casts = [
        'is_main' => IsMainEnum::class,
      ];
        public function model()
    {
        return $this->morphTo();
    }
}
