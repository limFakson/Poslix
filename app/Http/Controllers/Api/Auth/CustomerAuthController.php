<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerAuthController extends Controller {
    //
    // User registration

    public function register( Request $request ) {
        // validate data sent through the request
        $validatedData = $request->validate( [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string|max:20',
        ] );

        // Create new customer uesr
        $user = User::create( [
            'name' => $validatedData[ 'name' ],
            'email' => $validatedData[ 'email' ],
            'password' => Hash::make( $validatedData[ 'password' ] ),
            'phone' => $validatedData[ 'phone' ],
            'role_id' => 5,
            'is_active' => 0
        ] );

        // Generate token for authenticated user
        $token = JWTAuth::fromUser( $user );

        return response()->json( [
            'token' => $token,
            'userId' => $user->id
        ], 202 );
    }
}