<?php

namespace App\Models\Email;

use App\Enums\IsMainEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
class Email extends Model
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
