<?php

namespace App\Http\Controllers\Auth;

use App\Classes\eHealth\Api\oAuthEhealth\oAuthEhealth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

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
