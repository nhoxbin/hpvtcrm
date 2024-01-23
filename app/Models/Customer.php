<?php

namespace App\Models;

use App\Enums\SalesStateEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'phone', 'sales_state', 'sales_note'];

    protected $casts = [
        'sales_state' => SalesStateEnum::class,
        'available_data' => 'json',
    ];

    protected $appends = [
        'state'
    ];

    protected function state() : Attribute {
        return Attribute::get(fn () => !empty($this->attributes['sales_state']) && SalesStateEnum::tryFrom($this->attributes['sales_state']) ? __('sales_state.' . $this->attributes['sales_state']) : null);
    }

    public function user() {
    	return $this->belongsTo(User::class);
    }

    public function transactions() {
    	return $this->hasMany(Transaction::class);
    }
}
