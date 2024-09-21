<?php

namespace App\Http\Controllers\Api\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Biller;
use App\Models\Customer;
use App\Traits\JwtHelper;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\landlord\Domain;
use App\Models\landlord\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Api\UserCollection;
use App\Http\Resources\Api\CustomerResource;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller {
    // initialized JwtHelper to boot static
    public function __construct()
    {
        JwtHelper::init();
    }

    // Get authenticated user

    public function me(Request $request) {
        $name = $request->user;
        $user = User::where('id', $name->id)->first();
        $data = [];
        if ($user->role_id < 5){
            $warehouse = Warehouse::where('id', $user->warehouse_id)->first();
            $biller = Biller::where('id', $user->biller_id)->first();
            if($warehouse){
                $data["warehouseId"] = $warehouse->id;
                $data["warehouseName"] = $warehouse->name;
                $data["warehouseAddress"] = $warehouse->address;
            }
            if($biller){
                $data["billerId"] = $biller->id;
                $data["billerName"] = $biller->name;
            }
        }else{
            $customer_data = Customer::where('user_id', $user->id)->first();
            $data = new CustomerResource($customer_data);
        }

        return response()->json(['user'=>['userId'=>$user->id, 'name'=>$user->name, 'phone'=>$user->phone], "details"=>$data]);
    }

    // Refresh JWT token

    // public function refresh() {
    //     return response()->json( [ 'token' => Auth::refresh() ] );
    // }

    // User logout

    public function logout() {
        Auth::logout();
        return response()->json( [ 'message' => 'Successfully logged out' ] );
    }

    // User login

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
            return response()->json(['error' => 'User not found'], 404);
        }

        // Verify the password
        if (!Hash::check($password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 400);
        }

        // check user permission
        if ($user->role_id >= 5){
            return response()->json(['error' => 'User does not have permission to access this application'], 401);
        }

        if($user->is_active == false){
            return response()->json(['message'=>'logged in but id is not activated'], 400);
        }

        try {
            // Generate the JWT token
            $token = JwtHelper::encode(['user' => ['id' => $user->id]]);


            // Get tenant_id from the session or middleware
            $tenantId = session('tenant_id');

            if (!$tenantId) {
                return response()->json(['error' => 'Tenant not found in session'], 404);
            }

            return response()->json([
                'tenantId' => $tenantId,
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 60 * 60 * 12,
                'userId'=>$user->id,
                'timezone' => config('app.timezone'),
            ], 200, ['Content-Type' => 'application/json']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not create token', 'message' => $e->getMessage()], 500);
        }
    }

}
