<?php

namespace Modules\Ecommerce\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appearance extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "logo",
        "color",
        "menu_option",
    ];

    protected static function newFactory()
    {
        return \Modules\Ecommerce\Database\factories\AppearanceFactory::new();
    }
}