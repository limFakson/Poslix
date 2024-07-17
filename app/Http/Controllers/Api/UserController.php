<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\Api\UserCollection;
use Illuminate\Support\Facades\DB;
use App\Models\landlord\Tenant;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function index()
    {
        return User::all();
    }

    public function tenant(Request $request)
    {
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 400);
        }

        if(!$tenant) {
            return response()->json(['message'=>'Tenant not found']);
        }

        return response()->json($tenant);
    }

    public function tenantuser(Request $request, $id)
    {
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 400);
        }

        $tenancyDb = $tenant->tenancy_db_name;

        config(['database.connections.tenant' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => $tenancyDb,
            'username' => config('app.db_username'),
            'password' => config('app.db_password'),
        ]]);
        DB::purge('tenant');
        DB::reconnect('tenant');

        $users = DB::connection('tenant')
        ->table('users')
        ->leftjoin('warehouses', 'users.warehouse_id', '=', 'warehouses.id')
        ->leftjoin('billers', 'users.biller_id', '=', 'billers.id')
        ->leftjoin('roles', 'users.role_id', '=', 'roles.id')
        ->select(
                'users.*',
                'roles.name as role_name',
                'warehouses.name as warehouse_name',
                'warehouses.phone as warehouse_phone',
                'warehouses.address as warehouse_address',
                'warehouses.is_active as warehouse_is_active',
                'billers.name as biller_name'
            )
        ->where('users.id', $id)
        ->get();

        return new UserCollection($users);
    }

    public function store(Request $request)
    {

    }

    public function show(User $user)
    {
        // return new UserResource($user);
    }

    public function update(Request $request, $user)
    {

    }

    public function destroy($user)
    {

    }
}
