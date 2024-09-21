<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use App\Http\Resources\Api\OrderResource;
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

    public function orderapi( Request $request ) {
        $dine = Order::where( 'name', 'dine' )->first();
        $pickup = Order::where( 'name', 'pickup' )->first();
        $delivery = Order::where( 'name', 'delivery' )->first();
        $data = [ 'dine'=>new OrderResource( $dine ), 'pickup'=>new OrderResource( $pickup ), 'delivery'=>new OrderResource( $delivery ) ];
        return response( $data );
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
