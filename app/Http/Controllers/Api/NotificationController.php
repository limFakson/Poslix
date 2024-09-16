<?php

namespace App\Http\Controllers\api;

use App\Http\Resources\Api\NotificationCollection;
use App\Http\Resources\Api\NotificationResource;
use App\Http\Requests\StoreNotificationRequest;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\Controller;
use App\Models\MenuNotification;
use Illuminate\Http\Request;
use App\Models\ActionButton;

class NotificationController extends Controller {
    //

    public function index( Request $request ) {
        $notification = MenuNotification::where( 'created_at', '>=', now()->subDay() )
        ->orderBy( 'created_at', 'desc' )
        ->get();
        return response()->json( new NotificationCollection( $notification ) );
    }

    public function store( StoreNotificationRequest $request ) {
        $tenantId = $request->input( 'tenant_id' );
        Config::set( 'tenant_id', $tenantId );

        $data = $request->all();

        if ( !isset( $data[ 'link' ] ) ) {
            return response()->json( [
                'error' => [
                    'link' => [ 'Link field is required' ]
                ]
            ], 400 );
        }
        $action_link = $data[ 'link' ];

        $action_details = ActionButton::where( 'actionlink', $action_link )->first();

        if ( !$action_details ) {
            return response()->json( [ 'error'=>'Invalid action link' ], 404 );
        }

        $notify_data[ 'message' ] = $action_details->name;
        $notify_data[ 'icon' ] = $action_details->icon;
        $notify_data[ 'table' ] = $data[ 'table' ] ?? null;

        $responce = MenuNotification::create( $notify_data );

        return response()->json( new NotificationResource( $responce ), 202 );
    }

    public function update( Request $request, $notiId ) {
        $tenantId = $request->input( 'tenant_id' );
        Config::set( 'tenant_id', $tenantId );
        $data = $request->all();

        if ( !isset( $data[ 'isViewed' ] ) || !isset( $data[ 'viewedBy' ] ) ) {
            return response()->json( [
                'error' => [
                    'isViewed' => isset( $data[ 'isViewed' ] ) ? [] : [ 'is viewed field is required' ],
                    'viewedBy' => isset( $data[ 'viewedBy' ] ) ? [] : [ 'viewed by field is required' ],
                ]
            ], 400 );
        }

        $notify_data = MenuNotification::find( $notiId );
        if ( !$notify_data ) {
            return response()->json( [ 'message' => 'Notification does not exist' ], 404 );
        }
        if ( !$notify_data->isViewed ) {
            return response()->json( [ 'message' => 'Notification has been marked already' ], 400 );
        }

        if ( isset( $data[ 'table' ] ) ) {
            $notify_data->table = $data[ 'table' ];
        }

        $notify_data->is_viewed = $data[ 'isViewed' ];
        $notify_data->viewed_by = $data[ 'viewedBy' ];
        $notify_data->save();

        return response()->json( new NotificationResource( $notify_data ), 200 );
    }
}