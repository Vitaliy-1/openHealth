<?php

namespace App\Classes\eHealth\Api\oAuthEhealth;

use App\Classes\eHealth\Exceptions\ApiException;
use App\Classes\eHealth\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class oAuthEhealth implements oAuthEhealthInterface
{
    const OAUTH_TOKENS = '/oauth/tokens';
    const OAUTH_USER = '/oauth/user';
    const OAUTH_APPROVAL = '/oauth/apps/authorize';
    const OAUTH_NONCE = '/oauth/nonce';

    public function callback(): \Illuminate\Http\RedirectResponse
    {
        if (!request()->has('code')) {
            return redirect()->route('login');
        }

        $code = request()->input('code');

        $this->authenticate($code);

        return redirect()->route('dashboard'); // Add this line
    }

    public function authenticate($code)
    {

        $user = User::find(\session()->get('user_id_auth_ehealth'));
        if (!$user) {
            return redirect('http://localhost/ehealth/oauth?code=' . $code);
        }

        $data = [
            'token' => [
                'client_id'     => $user->legalEntity->client_id ?? '',
                'client_secret' => $user->legalEntity->client_secret ?? '',
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => env('EHEALTH_REDIRECT_URI'),
                'scope'         => $user->getAllPermissions()->unique()->pluck('name')->join(' ')
            ]
        ];

        $request = (new Request('POST', self::OAUTH_TOKENS, $data, false))->sendRequest();
        if (!$request) {
            return redirect()->route('login'); // Add this line
        }
        self::setToken($request);

        $this->login($user);
    }

    public function approve(){
        $user = User::find(\session()->get('user_id_auth_ehealth'));

        $redirectUri = env('EHEALTH_REDIRECT_URI');

        $queryParams = [
            'app'=> [
                'client_id'     => $user->legalEntity->client_id ?? '',
                'redirect_uri'  => $redirectUri,
                'scope'         => $user->getAllPermissions()->unique()->pluck('name')->join(' ')
            ]
        ];


        $request = (new Request('POST', self::OAUTH_APPROVAL, $queryParams))->sendRequest();



    }

    public function nonce(){
        $queryParams = [
            'client_id'     => $user->legalEntity->client_id ?? '',
            'client_secret' => $user->legalEntity->client_secret ?? '',
        ];
        $request = (new Request('POST', self::OAUTH_NONCE, $queryParams))->sendRequest();

    }

    public function login($user): void
    {
        Auth::login($user);
//        redirect()->route('dashboard');
    }


    public static function loginUrl($user)
    {
        // Base URL and client ID
        $baseUrl = env('EHEALTH_AUTH_HOST') . '/sign-in';
        $redirectUri = env('EHEALTH_REDIRECT_URI');
        // Base query parameters
        $queryParams = [
            'client_id'     => $user->legalEntity->client_id ?? '',
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code'
        ];
        // Additional query parameters if email is provided

        if (!empty($user->email)) {
            $scope = $user->getAllPermissions()->unique()->pluck('name')->join(' ');
            $queryParams['email'] = $user->email;
            $queryParams['scope'] = $scope;
        }

        \session()->put('user_id_auth_ehealth', $user->id);
        // Build the full URL with query parameters
        return $baseUrl . '?' . http_build_query($queryParams);
    }

    public static function setToken($data)
    {
        Session::put('auth_token', $data['value']);
        Session::put('auth_token_expires_at', Carbon::createFromTimestamp($data['expires_at']));
        Session::put('refresh_token', $data['details']['refresh_token']);
        Session::save();
    }

    public function getToken(): string
    {
        return Session::get('auth_token') ?? '';
    }

    /**
     * @throws ApiException
     */
    public static function getUser(): array
    {
        return (new Request('GET', self::OAUTH_USER, []))->sendRequest();
    }

    public static function forgetToken()
    {
        Session::forget('auth_token');
        Session::forget('auth_token_expires_at');
        Session::forget('refresh_token');
        Session::forget('refresh_token_expires_at');
        return redirect()->route('login');

    }

    public function getApikey(): string
    {
        return  env('EHEALTH_API_KEY');
    }

    public function refreshAuthToken(): array
    {
        $user = Auth::user();
        $data = [
            'token' => [
                'client_id'     => $user->legalEntity->client_id ?? '',
                'client_secret' => $user->legalEntity->client_secret ?? '',
                'grant_type'    => 'refresh_token',
                'refresh_token' => Session::get('refresh_token'),
            ]
        ];
        $request = (new Request('POST', self::OAUTH_TOKENS, $data, false))->sendRequest();
        self::setToken($request);
        return $request;
    }


    public function isLoggedIn(): bool
    {
        return Session::has('auth_token') && Session::has('auth_token_expires_at');
    }

}


