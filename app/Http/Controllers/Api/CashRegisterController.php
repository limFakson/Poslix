<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\landlord\Tenant;
use App\Http\Requests\CashRegisterRequest;
use App\Http\Resources\Api\CashRegisterResource;
use App\Http\Resources\Api\CashRegisterCollection;

class CashRegisterController extends Controller
{
    //
    public function index(Request $request)
    {
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 400);
        }
        // Connected to the tenant database
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

        $cashRegister = DB::connection('tenant')->table('cash_registers')
        ->leftjoin('users', 'cash_registers.user_id', '=', 'users.id')
        ->leftjoin('warehouses', 'cash_registers.warehouse_id', '=', 'warehouses.id')
        ->select(
            'cash_registers.*',
            'users.name as user_name',
            'warehouses.name as warehouse_name'
        )
        ->orderBy('cash_registers.updated_at', 'desc')
        ->get();


        return new CashRegisterCollection($cashRegister);
    }

    public function store(CashRegisterRequest $request)
    {
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 400);
        }
        // Connected to the tenant database
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

        $cashData = $request;
        $now = \Carbon\Carbon::now()->toDateTimeString();
        $highestId = DB::connection('tenant')->table('cash_registers')->max('id');
        $newId = $highestId + 1;

        $createData = [
            "id"=>$newId,
            'cash_in_hand' => $cashData['cashInHand']?? null,
            'user_id' => $cashData['userId']?? null,
            'warehouse_id' => $cashData['warehouseId']?? null,
            'status' => $cashData['status']?? null,
            'created_at' => $now,
            'updated_at' => $now
        ];

        // Filter out null values
        $createData = array_filter($createData, function($value) {
            return!is_null($value);
        });
        // Update the customer details directly in the database
        DB::connection('tenant')->table('cash_registers')
            ->insert($createData);

        return response()->json($createData, 200);
    }

    public function show(Request $request, $id)
    {
        $tenantId = $request->input('tenant_id');
        // $userId = $request->input('user_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 400);
        }
        // Connected to the tenant database
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

        $cash = DB::connection('tenant')->table('cash_registers')
        ->leftjoin('users', 'cash_registers.user_id', '=', 'users.id')
        ->leftjoin('warehouses', 'cash_registers.warehouse_id', '=', 'warehouses.id')
        ->select(
            'cash_registers.*',
            'users.name as user_name',
            'warehouses.name as warehouse_name'
        )
        ->where('cash_registers.id', $id)
        ->first();

        if (!$cash) {
            return response()->json(['error' => 'Cash Registeres not found'], 404);
        }

        return new CashRegisterResource($cash);
    }

    public function update(Request $request, $cashId)
    {
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 400);
        }
        // Connected to the tenant database
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

        $cashData = $request;
        $now = \Carbon\Carbon::now()->toDateTimeString();

        $updateData = [
            'cash_in_hand' => $cashData['cashInHand']?? null,
            'user_id' => $cashData['userId']?? null,
            'warehouse_id' => $cashData['warehouseId']?? null,
            'status' => $cashData['status']?? null,
            'updated_at' => $now
        ];

        $updateData = array_filter($updateData, function($value) {
            return!is_null($value);
        });
        // Update the customer details directly in the database
        DB::connection('tenant')->table('cash_registers')
            ->where('id', $cashId)
            ->update($updateData);

        return response()->json($updateData, 200);
    }
}
