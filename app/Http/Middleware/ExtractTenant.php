<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Stancl\Tenancy\Tenancy;

class ExtractTenant
{
    public function handle($request, Closure $next)
    {
        // Access the current tenant
        $tenant = tenancy()->tenant;

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        // Set the tenant ID in the config
        Config::set('tenant_id', $tenant->id);

        return $next($request);
    }
}
