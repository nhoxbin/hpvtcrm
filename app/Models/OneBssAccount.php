<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OneBssAccount extends Model
{
    use HasFactory;

    protected $fillable = ['username', 'expires_in', 'access_token', 'user_id'];

    public function scopeGetToken($query)
    {
        return $query->whereNotNull('access_token')->latest();
    }
}
