<?php

namespace App\Http\Controllers\Api;

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

        return response()->json( new CouponResource( $coupon_details ), 200 );
    }
}
