<?php

namespace App\Classes\eHealth\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckSessionToken
{

    public function handle($request, Closure $next)
    {
//        if ( !$request->session()->has('auth_token')) {
//            Auth::guard('web')->logout();
//        }

        return $next($request);
    }
}
