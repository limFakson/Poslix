<?php

namespace App\Http\Controllers\Api\Auth;
// \Debugbar::disable();
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\DB;
use App\Models\landlord\Tenant;
use Illuminate\Support\Facades\Hash;
use App\Models\landlord\Domain;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $domain = $request->input('domain');
        $identifier = $request->input('identifier');
        $password = $request->input('password');

        $domainModel = Domain::where('domain', $domain)->first();

        if (!$domainModel) {
            return response()->json(['error' => 'Domain not found'], 401);
        }

        $tenantId = $domainModel->tenant_id;

        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 401);
        }

        $tenancyDb = $tenant->tenancy_db_name;

        // Switched to the tenant's database using 'tenancy_db_name'
        config(['database.connections.tenant' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => $tenancyDb,
            'username' => config('app.db_username'),
            'password' => config('app.db_password'),
            ]]);
        DB::purge('tenant');
        DB::reconnect('tenant');

        // Move the reconnect line here
        DB::reconnect('tenant');
        $expectedDbName = $tenant->tenancy_db_name;
        $currentDbName = DB::connection('tenant')->getDatabaseName();

        if ($expectedDbName !== $currentDbName) {
            Log::error('Failed to connect to tenant database');
        }
        $users = DB::connection('tenant')->table('users')->get();

        $user = $users->where('name', $identifier)->first();

        $username = $user->name;

        if (!$user) {
            return response()->json(['error' => 'User not found'], 401);
        }

        $timezone = config('app.timezone');
        if ($username === $identifier && Hash::check($password, $user->password)) {
            // if (Auth::attempt(['name' => $identifier, 'password' => $password])) {
            //     $user = Auth::user();
            //     $token = $user->createToken('API Token', ['*'])->plainTextToken;
            //     return response()->json(['token' => $token], 200);
            // }
            return response()->json(['tenantId' => $tenantId, 'userId' =>$user->id, 'timezone'=>$timezone], 200, ['Content-Type' => 'application/json']);
        } else {
            return response()->json(['error' => 'Username And Password Are Wrong.'], 401);
        }
    }

}