<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigiShopCustomer extends Model
{
    use HasFactory;

    protected $fillable = ['tkc', 'first_product_name', 'packages'];
}
