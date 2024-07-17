<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class RefreshSession
{
    public function handle($request, Closure $next)
    {
        if ($request->ajax()) {
            // Regenerate the session ID to keep the session active
            Session::regenerate();
        }

        return $next($request);
    }
}
