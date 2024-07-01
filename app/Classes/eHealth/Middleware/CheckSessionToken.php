<?php

namespace App\Classes\eHealth\Middleware;

use App\Classes\eHealth\Api\oAuthEhealth\oAuthEhealthInterface;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckSessionToken
{


    protected $oauthEhealth;

    public function __construct(oAuthEhealthInterface $oauthEhealth)
    {
        $this->oauthEhealth = $oauthEhealth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check if the auth token and its expiration time exist
        if (Session::has('auth_token') && Session::has('auth_token_expires_at')) {
            $expiresAt = Carbon::parse(Session::get('auth_token_expires_at'));
            // If the token has expired, try to refresh it using the refresh token
            if (Carbon::now()->greaterThanOrEqualTo($expiresAt)) {
                if (Session::has('refresh_token')) {
                    $refreshToken = Session::get('refresh_token');
                    $newTokenData = $this->oauthEhealth->refreshAuthToken($refreshToken);
                    if ($newTokenData) {
                        $this->oauthEhealth->setToken($newTokenData);
                    } else {
                        Auth::guard('web')->logout();
                        return redirect()->route('login')->withErrors('Session expired, please log in again.');
                    }
                } else {
                    Auth::guard('web')->logout();
                    return redirect()->route('login')->withErrors('Session expired, please log in again.');
                }
            }
        }

        return $next($request);
    }

}
