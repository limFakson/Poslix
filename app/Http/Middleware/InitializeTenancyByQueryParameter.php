<?php

namespace App\Http\Middleware;

use Closure;
use Stancl\Tenancy\Tenancy;

class InitializeTenancyByQueryParameter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected $tenancy;

    public function __construct(Tenancy $tenancy)
    {
        $this->tenancy = $tenancy;
    }

    public function handle($request, Closure $next)
    {
        $tenantId = $request->query('tenant_id');

        if ($tenantId) {
            // Assuming you have a method to find tenant by ID
            $tenant = $this->tenancy->find($tenantId);

            if ($tenant) {
                // Initialize the tenant context
                $this->tenancy->initialize($tenant);
            } else {
                return response()->json(['error' => 'Invalid tenant_id'], 400);
            }
        }

        return $next($request);
    }
}
