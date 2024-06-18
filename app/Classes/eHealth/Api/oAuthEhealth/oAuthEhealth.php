<?php

namespace App\Classes\eHealth\Api\oAuthEhealth;

use App\Classes\eHealth\Api\PersonApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Classes\eHealth\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class oAuthEhealth implements oAuthEhealthInterface
{
    const OAUTH_TOKENS = '/oauth/tokens';
    const OAUTH_USER = '/oauth/user';



    public function callback(): \Illuminate\Http\RedirectResponse
    {
        if ( env('EHEALTH_CALBACK_PROD') === false) {
            dd(request()->all());
        }

        if (!request()->has('code')) {
            return redirect()->route('login');
        }

        $code = request()->input('code');
        $this->authenticate($code);

        return redirect()->route('dashboard'); // Add this line
    }

    public function authenticate($code)
    {
        $data = [
            'token' => [
                'client_id' => env('EHEALTH_CLIENT_ID'),
                'client_secret' => env('EHEALTH_CLIENT_SECRET'),
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => env('EHEALTH_REDIRECT_URI')
            ]
        ];

        $request = (new Request('POST', self::OAUTH_TOKENS, $data, false))->sendRequest();

        self::setToken($request);
        $user = self::getUser();


        $this->login($user);
    }

    public function login(array $email = []): void
    {
        $user = $this->findOrCreateUser($email);

        $this->loginUser($user);
    }

    private function findOrCreateUser(array $data): User
    {
        $existingUser = User::where('email', $data['email'])->first();

        if ($existingUser) {
            return $existingUser;
        }

        $data['password'] = Hash::make(Str::random(16));

        return User::create($data);
    }

    private function loginUser(User $user): void
    {
        Auth::login($user);
        redirect()->route('dashboard');
    }


    public static function loginUrl($email = '')
    {
        $url = env('EHEALTH_AUTH_HOST') . '/sign-in?client_id=' . env('EHEALTH_CLIENT_ID') . '&redirect_uri=' . env('EHEALTH_REDIRECT_URI');

        if (!empty($email)) {
            $url .= '&email=' . $email;
        }

        return $url;
    }

    public static function setToken($data)
    {
        Session::put('auth_token', $data['value']);
        Session::put('auth_token_expires_at', now()->addHours(1));

        Session::put('refresh_token', $data['details']['refresh_token']);
        Session::put('refresh_token_expires_at', now()->addHours(1));

        Session::save();
    }

    public function getToken(): string
    {
        return Session::get('auth_token');
    }

    /**
     * @throws ApiException
     */
    public function getUser(): array
    {
        return (new Request('GET', self::OAUTH_USER, [], true))->sendRequest();
    }

    public static function forgetToken()
    {
        Session::forget('auth_token');
        Session::forget('auth_token_expires_at');
        Session::forget('refresh_token');
        Session::forget('refresh_token_expires_at');
        return redirect()->route('login');

    }



    public static function person(){
        return PersonApi::_getAuthMethod(['id'=>'f13ab4b7-1167-4215-9fb3-2116b775ddb1']);
    }
}


