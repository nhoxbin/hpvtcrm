<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, HasPermissions;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'created_by_user_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    protected $appends = [
        'is_admin', 'all_permissions'
    ];

    protected function getDefaultGuardName(): string { return 'web'; }

    protected function isAdmin() : Attribute {
        return Attribute::get(fn () => $this->hasAnyRole(['Super Admin', 'Admin']));
    }

    protected function isSuperAdmin() : Attribute {
        return Attribute::get(fn () => $this->hasRole(['Super Admin']));
    }

    protected function allPermissions() : Attribute {
        return Attribute::get(fn () => $this->getAllPermissions());
    }

    public function created_by_user() {
        return $this->belongsTo(self::class, 'created_by_user_id');
    }

    public function created_users() {
        return $this->hasMany(self::class, 'created_by_user_id');
    }

    public function customers() {
        return $this->hasMany(Customer::class);
    }

    public function onebss_customers() {
        return $this->hasMany(OneBssCustomer::class);
    }

    public function onebss_account() {
        return $this->hasMany(OneBssAccount::class)->latest();
    }

    public function digishop_customers() {
        return $this->hasMany(DigiShopCustomer::class);
    }
}
