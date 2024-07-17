<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extras extends Model
{
    use HasFactory;

    public $fillable=[
        'id',
        'extra_category_id',
        'name',
        'price'
    ];

    public function extraCategory()
    {
        return $this->belongsTo(ExtraCategory::class);
    }
}
