<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuPayment extends Model
{
    protected $table = 'menu_payment';

    protected $fillable = [
        "name",
        "payment_options"
    ];

}