<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class ResetAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->flush();
        }
        return $next($request);
    }
}
