<?php

namespace App\Models;

use App\Enums\OneBssSalesStateEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OneBssCustomer extends Model
{
    use HasFactory;

    protected $fillable = ['phone', 'core_balance', 'goi_data', 'user_id', 'sales_state', 'sales_note', 'admin_note'];

    protected $casts = [
        'goi_data' => 'json',
        'sales_state' => OneBssSalesStateEnum::class,
    ];
}
