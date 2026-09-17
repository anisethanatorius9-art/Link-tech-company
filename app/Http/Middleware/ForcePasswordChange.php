<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->must_change_password
            && ! $request->routeIs('security.edit', 'logout')) {
            return redirect()->route('security.edit')->with('status', 'Please set a new password before continuing.');
        }

        return $next($request);
    }
}
