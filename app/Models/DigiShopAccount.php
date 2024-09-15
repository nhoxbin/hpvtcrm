<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class DigiShopAccount extends Model
{
    use HasFactory;

    protected $fillable = ['username', 'password', 'access_token', 'status', 'user_id'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customers(): HasManyThrough
    {
        return $this->hasManyThrough(DigiShopCustomer::class, User::class, 'id', 'user_id', 'user_id', 'id');
    }
}
