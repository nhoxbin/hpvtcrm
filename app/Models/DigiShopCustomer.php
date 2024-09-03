<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigiShopCustomer extends Model
{
    use HasFactory;

    protected $fillable = ['phone_number', 'user_id', 'tkc', 'first_product_name', 'packages', 'integration', 'long_period', 'is_request'];

    protected $casts = [
        'packages' => 'json',
        'integration' => 'json',
        'long_period' => 'json',
        'is_request' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
