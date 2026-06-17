<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('admin_unlocked') !== true) {
            return redirect()->route('access.show');
        }

        return $next($request);
    }
}
