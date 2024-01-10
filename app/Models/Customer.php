<?php

namespace App\Models;

use App\Enums\SalesStateEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $casts = [
        'sales_state' => SalesStateEnum::class,
        'available_data' => 'json',
    ];

    public function user() {
    	return $this->belongsTo(User::class);
    }

    public function transactions() {
    	return $this->hasMany(Transaction::class);
    }
}
