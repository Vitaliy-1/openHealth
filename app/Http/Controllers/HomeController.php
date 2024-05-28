<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index() {
        $email = config('app.email');
        $phone = config('app.phone');

        return view('home', compact('email', 'phone'));
    }
}

