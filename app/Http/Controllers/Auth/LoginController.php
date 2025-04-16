<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Classes\eHealth\Api\oAuthEhealth\oAuthEhealth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user->emailVerifiedAt) {
            return back()->with('error', __('auth.login.error.email_verification'));
        }

        if ($user && $request->missing('is_local_auth') && $user->isClientId() ) {
            $url = oAuthEhealth::loginUrl($user);

            return redirect()->to($url);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Невірний логін або пароль.',
        ]);
    }
}
