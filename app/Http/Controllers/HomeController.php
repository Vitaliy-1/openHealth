<?php

namespace App\Http\Controllers;

use App\Classes\eHealth\Api\LegalEntitiesApi;
use App\Classes\eHealth\Api\LicenseApi;
use App\Classes\eHealth\Api\oAuthEhealth\oAuthEhealth;
use GuzzleHttp\Client;
use Illuminate\Http\Client\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Psr7\Request;
use Psr\Log\LoggerInterface;
class HomeController extends Controller
{
    public function index() {
        $email = config('app.email');
        $phone = config('app.phone');

        return view('home', compact('email', 'phone'));
    }

}

