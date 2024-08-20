<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ActionButton;
use Illuminate\Http\Request;

class ButtonController extends Controller {
    //

    public function index( Request $request ) {
        $action = ActionButton::where( 'status', true )->get();
        return response()->json( $action );
    }
}