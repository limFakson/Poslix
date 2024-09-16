<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class DomainExpansion extends Controller {
    //

    public function getTenantId( Request $request ) {
        $tenantId = session( 'tenant_id' );

        if ( !$tenantId ) {
            return response()->json( [ 'error' => 'Tenant not found in session' ], 404 );
        }

        return response()->json( [ 'tenantId'=>$tenantId ] );
    }
}
