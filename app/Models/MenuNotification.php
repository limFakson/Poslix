<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuNotification extends Model {

    protected $fillable = [
        'message', 'icon', 'table', 'is_viewed', 'viewed_by',
    ];
}
