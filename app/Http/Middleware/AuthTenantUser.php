<?php

namespace App\Http\Middleware;

use Log;
use Closure;
use Illuminate\Http\Request;
use App\Models\landlord\Domain;
use App\Models\landlord\Tenant;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AuthTenantUser {
    /**
    * Handle an incoming request.
    *
    * @param  \Closure( \Illuminate\Http\Request ): ( \Symfony\Component\HttpFoundation\Response )  $next
    */

    public function handle( $request, Closure $next ) {
        $domain = $request->input( 'domain' );

        $domainModel = Domain::where( 'domain', $domain )->first();

        if ( !$domainModel ) {
            return response()->json( [ 'error' => 'Domain not found' ], 401 );
        }

        $tenantId = $domainModel->tenant_id;
        $tenant = Tenant::find( $tenantId );

        if ( !$tenant ) {
            return response()->json( [ 'error' => 'Tenant not found' ], 401 );
        }

        // Switch to tenant database
        $tenancyDb = $tenant->tenancy_db_name;

        config( [ 'database.connections.tenant' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => $tenancyDb,
            'username' => config( 'app.db_username' ),
            'password' => config( 'app.db_password' ),
        ] ] );

        DB::purge( 'tenant' );
        DB::reconnect( 'tenant' );

        // Check if successfully connected to tenant database
        $expectedDbName = $tenant->tenancy_db_name;
        $currentDbName = DB::connection( 'tenant' )->getDatabaseName();

        if ( $expectedDbName !== $currentDbName ) {
            Log::error( 'Failed to connect to tenant database' );
            return response()->json( [ 'error' => 'Failed to connect to tenant database' ], 500 );
        }

        // Save tenant_id in session for future requests
        session( [ 'tenant_id' => $tenantId ] );

        // Proceed with the request
        return $next( $request );
    }
}