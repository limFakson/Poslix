<?php

namespace App\Models\landlord;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    // protected $table = 'domains';

    public function tenants()
    {
        return $this->hasMany(Tenant::class); // Example relationship
    }
}