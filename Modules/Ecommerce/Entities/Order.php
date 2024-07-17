<?php

namespace Modules\Ecommerce\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "name",
        "enable_order",
        "enable_payments",
        "payment_required",
    ];

    protected static function newFactory()
    {
        return \Modules\Ecommerce\Database\factories\OrderFactory::new();
    }
}