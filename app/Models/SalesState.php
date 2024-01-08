<?php

namespace App\Models;

use App\Enums\SalesStateEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesState extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'code' => SalesStateEnum::class,
    ];
}
