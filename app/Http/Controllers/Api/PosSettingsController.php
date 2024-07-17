<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\landlord\Tenant;
use App\Http\Requests\PosSettingRequest;
use App\Http\Resources\Api\MenuSettingResources;
use App\Http\Resources\Api\CustomMethodResource;
use App\Http\Resources\Api\PosSettingsResource;

class PosSettingsController extends Controller
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

        $setting = DB::connection('tenant')->table('pos_setting')
        ->latest()
        ->get();
        $custom = DB::connection('tenant')->table('custom_methods')
        ->where('active', true)
        ->get();

        return response()->json([
            "posSetting"=> PosSettingsResource::Collection($setting),
            "customs"=> CustomMethodResource::Collection($custom)
        ]);
    }

    public function update(PosSettingRequest $request, $posSettingsId)
    {
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 400);
        }
        // dd($request);
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

        $posSettigsData = $request;
        $now = \Carbon\Carbon::now()->toDateTimeString();

        $updateData = [
            'customer_id' => $posSettigsData['customerId']?? null,
            'warehouse_id' => $posSettigsData['warehouseId']?? null,
            'biller_id' => $posSettigsData['billerId']?? null,
            'product_number' => $posSettigsData['productNumber']?? null,
            'keybord_active' => $posSettigsData['KeyboardActive']?? null,
            'is_table' => $posSettigsData['isTable']?? null,
            'stripe_public_key' => $posSettigsData['stripePublicKey']?? null,
            'stripe_secret_key' => $posSettigsData['stripeSecretKey']?? null,
            'paypal_live_api_username' => $posSettigsData['paypalLiveApiUsername']?? null,
            'paypal_live_api_password' => $posSettigsData['paypalLiveApiPassword']?? null,
            'paypal_live_api_secret' => $posSettigsData['paypalLiveApiSecret']?? null,
            'payment_options' => $posSettigsData['paymentOptions']?? null,
            'invoice_option' => $posSettigsData['invoiceOption']?? null,
            'updated_at' => $now
        ];
        $updateData = array_filter($updateData, function($value) {
            return!is_null($value);
        });
        $posSettigs = DB::connection('tenant')->table('pos_setting')
        ->where('id',$posSettingsId)
        ->update($updateData);
        return response()->json($updateData, 201);
    }

    public function menu_settings(Request $request){
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 400);
        }
        // dd($request);
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

        $menu_setting = DB::connection('tenant')->table('menu_payment')
        ->latest()
        ->first();
        $custom_menu = DB::connection('tenant')->table('custom_methods')
        ->where('is_online', true)
        ->get();

        return response([
            "MenuSetting"=>new MenuSettingResources($menu_setting),
            "customs"=>CustomMethodResource::Collection($custom_menu)
        ]);
    }

}
