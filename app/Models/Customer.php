<?php

namespace App\Models;

use App\Enums\SalesStateEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $casts = [
        'sales_state' => SalesStateEnum::class,
        'available_data' => 'json',
    ];

    protected $appends = [
        'state'
    ];

    protected function state() : Attribute {
        return Attribute::get(fn (?string $value) => !empty($value) ? __('sales_state.' . SalesStateEnum::tryFrom($value)->value) : null);
    }

    public function user() {
    	return $this->belongsTo(User::class);
    }

    public function transactions() {
    	return $this->hasMany(Transaction::class);
    }
}
