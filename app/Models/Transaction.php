<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['message', 'orderId', 'result', 'product'];

    protected $casts = [
        'product' => 'json',
    ];

    public static function booted()
    {
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function customer() {
    	return $this->belongsTo(Customer::class)->latest();
    }

    public function created_by() {
    	return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
