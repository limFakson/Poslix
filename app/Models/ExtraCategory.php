<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraCategory extends Model
{
    use HasFactory;

    public $fillable = [
        'id',
        'category_name',
        'is_multi',        
    ];

    public function extras()
    {
        return $this->hasMany(Extras::class);
    }
}
