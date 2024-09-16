<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\CustomerGroup;
use Illuminate\Http\Request;
use App\Traits\JwtHelper;
use App\Models\Customer;
use App\Models\User;

class MenuAuthController extends Controller {
    // initialized JwtHelper to boot static
    public function __construct()
    {
        JwtHelper::init();
    }

    // User registration

    public function register( Request $request  ) {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'domain' => 'required|string',
                'name' => 'required|string',
                'email' => 'required|string|email',
                'password' => 'required|string',
                'phone' => 'required|string',
            ]);

        } catch (ValidationException $e) {
            // Catch and return validation error response
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $e->errors()
            ], 422);
        }

        $existingUser = User::on('tenant')->where([['name', $validatedData['name']], ['email', $validatedData['email']]])->first();

        if($existingUser){
            return response()->json(["message"=>"user already exists"], 400);
        }

        // Create new customer uesr
        $user = User::on('tenant')->create( [
            'name' => $validatedData[ 'name' ],
            'email' => $validatedData[ 'email' ],
            'password' => Hash::make( $validatedData[ 'password' ] ),
            'phone' => $validatedData[ 'phone' ],
            'role_id' => 5,
            'is_active' => false,
            'is_deleted'=>false
        ] );

        $customer_group = CustomerGroup::on('tenant')->where('name', 'general')->select('id')->first();

        $data['name'] = $validatedData[ 'name' ];
        $data['email'] = $validatedData[ 'email' ];
        $data['phone'] = $validatedData[ 'phone' ];
        $data['user_id'] = $user->id;
        $data['customer_group_id'] = $customer_group->id;
        $data['is_active'] = true;
        Customer::on('tenant')->create($data);

        // Get tenant_id from the session or middleware
        $tenantId = session('tenant_id');

        if (!$tenantId) {
            return response()->json(['error' => 'Tenant not found in session'], 404);
        }

        // Generate token for authenticated user
        $token = JwtHelper::encode(['user' => ['id' => $user->id]]);

        return response()->json( [
            'tenantId' => $tenantId,
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 60 * 60 * 12,
            'userId' => $user->id
        ], 202 );
    }

    public function login( Request $request ) {
        // Validate input
        try {
            $request->validate( [
                'identifier' => 'required|string',
                'password' => 'required|string',
            ] );

            $identifier = $request->input( 'identifier' );
            $password = $request->input( 'password' );
        }catch (ValidationException $e) {
            // Catch and return validation error response
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $e->errors()
            ], 422);
        }

        // Fetch user from the tenant's database
        $user = User::on('tenant')->where('email', $identifier)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Verify the password
        if (!Hash::check($password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 400);
        }

        // Get tenant_id from the session or middleware
        $tenantId = session('tenant_id');

        if (!$tenantId) {
            return response()->json(['error' => 'Tenant not found in session'], 404);
        }

        try {
            // Generate the JWT token
            $token = JwtHelper::encode(['user' => ['id' => $user->id]]);

            return response()->json([
                'tenantId' => $tenantId,
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 60 * 60 * 12,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not create token', 'message' => $e->getMessage()], 500);
        }

   }
}