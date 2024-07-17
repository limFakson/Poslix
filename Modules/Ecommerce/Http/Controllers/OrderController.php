<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\landlord\Tenant;
use Modules\Ecommerce\Entities\Order;
use Auth;

class OrderController extends Controller {
    /**
    * Display a listing of the resource.
    * @return Renderable
    */

    public function index() {
        return view( 'ecommerce::index' );
    }

    public function orderSetting() {
        $dine = Order::where( 'user_id', Auth::user()->id )->where( 'name', 'dine' )->first();
        $pickup = Order::where( 'user_id', Auth::user()->id )->where( 'name', 'pickup' )->first();
        $delivery = Order::where( 'user_id', Auth::user()->id )->where( 'name', 'delivery' )->first();
        return view( 'backend.setting.order_setting', compact( 'dine', 'pickup', 'delivery' ) );
    }

    public function orderapi(Request $request) {
        $userId = $request->input('user_id');
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 404);
        } else if(!$userId){
            return response()->json(["message"=> "User id is needed to be passed as (user_id)"], 404);
        };

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
        $userid = DB::connection('tenant')->table('users')->where('id', $userId)->first();
        if (!$userid){
            return response()->json(["message"=> "User not found"], 404);
        }

        $dine = DB::connection('tenant')->table('orders')->where( 'user_id', $userId )->where( 'name', 'dine' )->first();
        $pickup = DB::connection('tenant')->table('orders')->where( 'user_id', $userId )->where( 'name', 'pickup' )->first();
        $delivery = DB::connection('tenant')->table('orders')->where( 'user_id', $userId )->where( 'name', 'delivery' )->first();
        $data = ["dine"=>$dine, "pickup"=>$pickup, "delivery"=>$delivery];
        return response($data);
    }

    public function orderSettingStore( Request $request ) {
        $datas = [
            [ 'user_id' => Auth::user()->id, 'name' => 'dine', 'enable_order'=> $request->enable_dine_in_order == true ? 1: 0, 'enable_payments'=> $request->enable_dine_online_payments_check == true ? 1: 0, 'payment_required'=> $request->enable_dine_payment_required == true ? 1: 0 ],
            [ 'user_id' => Auth::user()->id, 'name' => 'pickup', 'enable_order'=> $request->enable_pickup_order == true ? 1: 0, 'enable_payments'=> $request->enable_pickup_online_payments_check == true ? 1: 0, 'payment_required'=> $request->enable_pickup_payment_required  == true ? 1: 0 ],
            [ 'user_id' => Auth::user()->id, 'name' => 'delivery', 'enable_order'=> $request->enable_delivery_order == true ? 1: 0, 'enable_payments'=> $request->enable_delivery_online_payments_check == true ? 1: 0, 'payment_required'=> $request->enable_delivery_payment_required == true ? 1: 0 ],
        ];
        foreach ( $datas as $data ) {
            Order::updateOrCreate( [ 'user_id' => Auth::user()->id, 'name' => $data[ 'name' ] ], [ 'enable_order' => $data[ 'enable_order' ], 'enable_payments' => $data[ 'enable_payments' ], 'payment_required' => $data[ 'payment_required' ] ] );
        }
        return back()->with( 'message', 'Saved successfully' );
    }
    /**
    * Show the form for creating a new resource.
    * @return Renderable
    */

    public function create() {
        return view( 'ecommerce::create' );
    }

    /**
    * Store a newly created resource in storage.
    * @param Request $request
    * @return Renderable
    */

    public function store( Request $request ) {
        //
    }

    /**
    * Show the specified resource.
    * @param int $id
    * @return Renderable
    */

    public function show( $id ) {
        return view( 'ecommerce::show' );
    }

    /**
    * Show the form for editing the specified resource.
    * @param int $id
    * @return Renderable
    */

    public function edit( $id ) {
        return view( 'ecommerce::edit' );
    }

    /**
    * Update the specified resource in storage.
    * @param Request $request
    * @param int $id
    * @return Renderable
    */

    public function update( Request $request, $id ) {
        //
    }

    /**
    * Remove the specified resource from storage.
    * @param int $id
    * @return Renderable
    */

    public function destroy( $id ) {
        //
    }
}
