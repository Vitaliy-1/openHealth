<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $email = config('app.email');
        $phone = config('app.phone');

        return view('home', compact('email', 'phone'));
    }
}
