<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\landlord\Tenant;
use Illuminate\Support\Facades\Hash;
use App\Models\landlord\Domain;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller {
    // User registration
    // public function register( Request $request )
    // {
    //     $validatedData = $request->validate( [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:6|confirmed',
    // ] );

    //     $user = User::create( [
    //         'name' => $validatedData[ 'name' ],
    //         'email' => $validatedData[ 'email' ],
    //         'password' => Hash::make( $validatedData[ 'password' ] ),
    // ] );

    //     $token = JWTAuth::fromUser( $user );

    //     return response()->json( [ 'token' => $token ] );
    // }

    // User login

    // Get authenticated user

    public function me() {
        return response()->json( Auth::user() );
    }

    // Refresh JWT token

    public function refresh() {
        return response()->json( [ 'token' => Auth::refresh() ] );
    }

    // User logout

    public function logout() {
        Auth::logout();
        return response()->json( [ 'message' => 'Successfully logged out' ] );
    }

    public function login( Request $request ) {
        // Validate input
        $request->validate( [
            'domain' => 'required|string',
            'identifier' => 'required|string',
            'password' => 'required|string',
        ] );

        $identifier = $request->input( 'identifier' );
        $password = $request->input( 'password' );
        $domain = $request->input( 'domain' );

        // Domain is already used by middleware to connect to the right tenant DB

        // Fetch user from the tenant's database
        $user = User::on('tenant')->where('name', $identifier)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 401);
        }

        // Verify the password
        if (!Hash::check($password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Generate JWT Token for the user
        if (!$token = Auth::guard('api')->login($user)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        dd($user);

        return response()->json([
            'tenantId' => $user->tenant_id,
            'userId' => $user->id,
            'token' => $token,
            'timezone' => config('app.timezone'),
        ], 200, ['Content-Type' => 'application/json' ] );
    }

}
