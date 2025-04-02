<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class LogoutController extends Controller
{
    public function logout(Request $request, bool $redirect = true)
    {
        $user = Auth::user();

        if ($user) {
            $user->tokens()->delete();
        }

        $sessionId = $request->session()->getId();

        if (config('session.driver') === 'database') {
            Session::getHandler()->destroy($sessionId);
        }

        Auth::guard('web')->logout();

        Session::flush();

        session()->invalidate();
        session()->regenerateToken();

        return $redirect ? redirect('/login') : true;
    }
}
