<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BillerCollection;
use App\Http\Resources\Api\BillerResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\landlord\Tenant;

class BillerController extends Controller
{
    //
    public function index(Request $request)
    {
        $tenantId = $request->input('tenant_id');
        $userId = $request->input('user_id');
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

        $biller = DB::connection('tenant')->table('billers')
                ->where('is_active', 1)
                ->first();

        // dd($biller);
        return new BillerResource($biller);
    }
}