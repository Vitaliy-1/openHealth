<?php

namespace App\Classes\eHealth\Middleware;

use Closure;
use Carbon\Carbon;
use App\Auth\EHealth\Services\TokenStorage;
use Illuminate\Support\Facades\Auth;

class CheckSessionToken
{
    protected  TokenStorage $tokenStorage;

    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
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
        $tokenExpiresAt = $this->tokenStorage->getExpiresAt();

        // Check if the auth token and its expiration time exist
        if (Auth::check() && $this->tokenStorage->hasBearerToken() && $tokenExpiresAt) {
            $expiresTime = Carbon::parse($tokenExpiresAt);

            // If the token has expired, try to refresh it using the refresh token
            if (Carbon::now()->greaterThanOrEqualTo($expiresTime)) {
                if ($this->tokenStorage->getRefreshToken()) {
                    $newTokenData = $this->tokenStorage->refreshBearerToken();

                    if (!$newTokenData) {
                        Auth::logout();

                        return redirect()->route('login')->withErrors('Session expired, please log in again.');
                    }
                } else {
                    Auth::logout();

                    return redirect()->route('login')->withErrors('Session expired, please log in again.');
                }
            }
        }

        return $next($request);
    }
}
