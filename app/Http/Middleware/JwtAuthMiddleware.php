<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Traits\JwtHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class JwtAuthMiddleware {

    public function handle( $request, Closure $next ) {
        JwtHelper::init();

        // Get token from the Authorization header
        $token = $request->bearerToken();

        if ( !$token ) {
            return response()->json( [ 'error' => 'Token not provided' ], 401 );
        }

        // Decode the token
        $decoded = JwtHelper::decode( $token );

        if ( !$decoded ) {
            return response()->json( [ 'error' => 'Invalid or expired token' ], 401 );
        }

        // Set the user data from the token
        $request->user = $decoded->user;

        // verify user gotten from token
        if ( !$this->verifyUser( $request->user ) ) {
            return response()->json( [ 'message' => 'Invalid token' ], 403 );
        }

        // Allow the request to continue
        return $next( $request );
    }

    protected function verifyUser( $user ) {
        $user = User::where( 'name', $user->id )->first();

        if ( !$user ) {
            Log::error( 'Invalid token: User not found' );
            return false;
        }

        return true;
    }
}