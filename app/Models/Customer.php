<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $casts = [
        'available_data' => 'json',
    ];

    public function user() {
    	return $this->belongsTo(User::class);
    }

    public function transaction() {
    	return $this->belongsTo(Transaction::class);
    }

    public function sales_state() {
    	return $this->belongsTo(SalesState::class);
    }
}
