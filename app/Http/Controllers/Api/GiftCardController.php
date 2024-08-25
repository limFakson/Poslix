<?php

namespace App\Http\Controllers\Api;

use App\Models\GiftCard;
use Illuminate\Http\Request;
use App\Models\landlord\Tenant;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\GiftCardResource;
use App\Http\Resources\Api\GiftCardCollection;

class GiftCardController extends Controller {
    //

    public function index( Request $request ) {
    }

    public function show( Request $request, $id ) {
    }

    public function store( Request $request ) {
        $data = $request->all();

        if ( !isset( $data[ 'cardNo' ] ) ) {
            return response()->json( [
                'error' => [
                    'cardNo' => [ 'Card Number field is required' ]
                ]
            ], 400 );
        }
        $giftcard_num = $data[ 'cardNo' ];

        $giftcard_details = GiftCard::where( [ [ 'card_no', $giftcard_num ], [ 'is_active', true ] ] )->first();

        if ( !$giftcard_details ) {
            return response()->json( [ 'error'=>'Gift Card not found' ], 404 );
        }

        return response()->json( new GiftCardResource( $giftcard_details ), 200 );
    }
}