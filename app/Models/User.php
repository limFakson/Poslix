<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject {
    use Notifiable;
    use HasRoles;
    use HasApiTokens;

    // Implement the methods required by the JWTSubject interface

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'company_name', 'role_id', 'biller_id', 'warehouse_id', 'is_active', 'is_deleted'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function isActive() {
        return $this->is_active;
    }

    public function holiday() {
        return $this->hasMany( 'App\Models\Holiday' );
    }
}