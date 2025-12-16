<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\RedirectIfAuthenticated as Middleware;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated extends Middleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}
