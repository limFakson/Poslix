<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionButton extends Model
{
    protected $fillable = ['title', 'icon', 'color', 'actionlink', 'status'];
}