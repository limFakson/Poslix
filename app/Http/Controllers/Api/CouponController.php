<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\landlord\Tenant;
use App\Http\Resources\Api\CouponResource;
use App\Http\Resources\Api\CouponCollection;

class CouponController extends Controller
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

        $coupon = DB::connection('tenant')->table('coupons')
        ->leftjoin('users', 'coupons.user_id', '=', 'users.id')
        ->select(
            'coupons.*',
            'users.name as user_name'
        )
        ->get();

        return new CouponCollection($coupon);
    }

    public function show(Request $request, $id)
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

        $coupon = DB::connection('tenant')->table('coupons')
        ->find($id);

        if (!$coupon) {
            return response()->json(['error' => 'Coupon not found'], 404);
        }

        return new CouponResource($coupon);
    }
}
