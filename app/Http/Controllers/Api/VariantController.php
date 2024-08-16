<?php

namespace App\Http\Controllers\api;

use App\Http\Resources\Api\VariantCollection;
use App\Http\Resources\Api\VariantResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Variant;

class VariantController extends Controller {
    public function index() {
        $variant = Variant::get();
        return response( [ 'variant'=>new VariantCollection( $variant ) ] );
    }
}