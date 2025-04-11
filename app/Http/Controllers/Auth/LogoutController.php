<?php

namespace App\Http\Controllers\Auth;

use App\Classes\eHealth\Request as eHealthRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class LogoutController extends Controller
{
    const OAUTH_LOGOUT = '/auth/logout';

    public function logout(Request $request, bool $redirect = true)
    {
        if (session()->has('user_id_auth_ehealth') || session()->has('auth_token')) {
            new eHealthRequest('POST', self::OAUTH_LOGOUT, [])->sendRequest();
        }

        $user = Auth::user();

        if ($user) {
            $user->tokens()->delete();
        }

        $sessionId = $request->session()->getId();

        if (config('session.driver') === 'database') {
            Session::getHandler()->destroy($sessionId);
        }

        Auth::guard('web')->logout();

        session()->invalidate();
        session()->regenerateToken();

        return $redirect ? redirect('/login') : true;
    }
}
