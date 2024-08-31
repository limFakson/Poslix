<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductExtraCategory extends Model {
    use HasFactory;

    protected $fillable = [
        'id',
        'product_id',
        'extra_category_id'
    ];

    public function product() {
        return $this->belongsTo( Product::class );
    }

    public function extraCategory() {
        return $this->belongsTo( ExtraCategory::class );
    }
}