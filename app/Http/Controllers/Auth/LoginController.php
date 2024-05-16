<?php

namespace App\Http\Controllers\Auth;

use App\Classes\eHealth\Api\oAuthEhealth\oAuthEhealth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{

    public function index(){
        return view('auth.login');
    }

    public function login(Request $request){
//        Validator::make($request->all(), [
//            'email' => ['required', 'string', 'email', 'max:255'],
//        ]);

        $oAuth = oAuthEhealth::loginUrl($request->email);
        return redirect()->to($oAuth);
    }

}
