<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Models\landlord\Tenant;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CouponResource;
use App\Http\Resources\Api\CouponCollection;

class CouponController extends Controller {
    //

    public function index( Request $request ) {
    }

    public function show( Request $request, $id ) {

    }

    public function store( Request $request ) {
        $data = $request->all();

        if ( !isset( $data[ 'code' ] ) ) {
            return response()->json( [
                'error' => [
                    'code' => [ 'Code field is required' ]
                ]
            ], 400 );
        }

        $coupon_code = $data[ 'code' ];

        $coupon_details = Coupon::where( [ [ 'code', $coupon_code ], [ 'is_active', true ] ] )->first();

        if ( !$coupon_details ) {
            return response()->json( [ 'error'=>'Coupon not found' ], 404 );
        }

        $expire_date = $coupon_details->expired_date;

        if ( Carbon::parse( $expire_date )->isPast() ) {
            return response()->json( [ 'error' => 'Coupon has expired' ], 400 );
        }

        $qty_used = $coupon_details->used;
        $available_qty = $coupon_details->quantity;

        if ( $qty_used >= $available_qty ) {
            return response()->json( [ 'error'=>'Coupon has been expended' ], 400 );
        }

        return response()->json( [ 'coupon'=>new CouponResource( $coupon_details ) ], 200 )->header( 'Content-Type', 'application/json' );
    }

    public function update( Request $request, $id ) {
        $coupon = Coupon::where( [ [ 'id', $id ], [ 'is_active', true ] ] )->first();

        if ( !$coupon ) {
            return response()->json( [ 'error'=>'Coupon not found' ], 404 );
        }

        $qty_used = $coupon->used;
        $available_qty = $coupon->quantity;

        if ( $qty_used >= $available_qty ) {
            return response()->json( [ 'error'=>'Coupon has been expended' ], 400 );
        }

        $data = $request->all();
        $data[ 'used' ] = $coupon->used + 1;
        $coupon->update( $data );

        return response()->json( new CouponResource( $coupon ), 200 );
    }
}