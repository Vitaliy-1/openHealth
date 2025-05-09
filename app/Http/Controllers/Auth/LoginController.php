<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\User;
use App\Models\LegalEntity;
use Illuminate\Http\Request;
use App\Auth\EHealth\Services\TokenStorage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Classes\eHealth\Api\EmployeeApi;
use App\Auth\EHealth\Services\EHealthLoginUserHandler;
use Illuminate\Support\Facades\Validator;
use App\Classes\eHealth\Request as eHealthRequest;
use Illuminate\Contracts\Validation\Validator as ResponseValidator;

class LoginController extends Controller
{
    protected EHealthLoginUserHandler $handleLoginUser;

    public function __construct(EHealthLoginUserHandler $handleLoginUser)
    {
        $this->handleLoginUser = $handleLoginUser;
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Помилка автентифікації',
            ]);
        }

        if (!$user->emailVerifiedAt) {
            return back()->with('error', __('auth.login.error.email_verification'));
        }

        if ($user && $request->missing('is_local_auth') && $user->isClientId() ) {
            $url = $this->loginUrl($user);

            return Redirect::to($url);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return Redirect::intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Невірний логін або пароль.',
        ]);
    }

    /**
     * This method is called when the user is redirected back from eHealth after it's successfull authentication
     *
     * @return null|RedirectResponse
     */
    public function callback(): ?RedirectResponse
    {
        // exchange code to token
        if (config('ehealth.api.callback_prod') === false) {
            $code = request()->input('code');
            $url =  'http://localhost/ehealth/oauth?code=' . $code;

            return redirect($url);
        }

        if (!request()->has('code')) {
            return Redirect::route('login');
        }

        try {
            $code = request()->input('code');

            $authResponse = EmployeeApi::authenticate($code);

            $authResponseValidator = $this->validateAuthResponse($authResponse);

            /** @var \Illuminate\Contracts\Validation\Validator $authResponseValidator */
            if ($authResponseValidator->fails()) {
                Log::error(__('auth.login.error.vlidation.auth', [], 'en'), ['errors' => $authResponseValidator->errors()]);

                return $this->handleLoginUser->breakAuth('auth.login.error.server.response');
            }

            $authResponseData = $authResponseValidator->validated();

            app(TokenStorage::class)->store($authResponseData);

            $authUserUUID = $authResponseData['user_id'];
            $authLegalEntityUUID = $authResponseData['details']['client_id'];

            try {
                $legalEntity = LegalEntity::byUuid($authLegalEntityUUID)->firstOrFail();
            } catch (Exception $err) {
                // Error if legal entity cannot be found
                Log::error(__('auth.login.error.unexistent_legal_entity', [], 'en'), ['Error' => $err->getMessage()]);

                return $this->handleLoginUser->breakAuth('auth.login.error.legal_entity_identity');
            }

            $isFirstLogin = (bool) ! User::where('uuid',$authUserUUID)->first()?->uuid;

            $user = $this->handleLoginUser->checkLoginedUser($legalEntity, $authUserUUID);

            if (!$user) {
                Log::error(__('auth.login.error.user_authentication', [], 'en'));

                return $this->handleLoginUser->breakAuth('auth.login.error.user_authentication');
            }
        } catch (Exception $err) {
            Log::error(__('auth.login.error.unexpected', [], 'en'), ['Error' => $err->getMessage()]);

            return $this->handleLoginUser->breakAuth();
        }

        auth('ehealth')->login($user);

        Log::info(__('auth.login.success.user_auth', [], 'en'), ['User ID' => $user->id]);

        return Redirect::route('dashboard')->with('success', $isFirstLogin ? __('auth.login.success.new_user_auth') : null);
    }

    public function logout(Request $request, bool $redirect = true)
    {
        if (auth('ehealth')->check()
            && (session()->has(config('ehealth.api.auth_ehealth'))
            || session()->has(config('ehealth.api.oauth.bearer_token')))
        ) {
            new eHealthRequest('POST', config('ehealth.api.oauth.logout'), [])->sendRequest();
        }

        $sessionId = $request->session()->getId();

        if (config('session.driver') === 'database') {
            Session::getHandler()->destroy($sessionId);
        }

        Auth::logout();

        session()->invalidate();
        session()->regenerateToken();

        return $redirect ? redirect('/login') : true;
    }

    /**
     * Prepare login URL for eHealth depending on the user credentials and redirect URI
     *
     * @param $user
     *
     * @return string
     */
    public static function loginUrl($user): string
    {
        // Base URL and client ID
        $baseUrl = config('ehealth.api.auth_host');
        $redirectUri = config('ehealth.api.redirect_uri');

        // Base query parameters
        $queryParams = [
            'client_id'     => $user->legalEntity->client_id ?? '',
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code'
        ];

        // Additional query parameters if email is provided
        if (!empty($user->email)) {
            $queryParams['email'] = $user->email;
            $queryParams['scope'] = $user->getScopes();
        }

        session()->put(config('ehealth.api.auth_ehealth'), $user->id);

        // Build the full URL with query parameters
        return $baseUrl . '?' . http_build_query($queryParams);
    }

    /**
     * Check authentication $response schema for errors
     *
     * @return array Returned only specified fields
     */
    public function validateAuthResponse(array $data): ResponseValidator
    {
        return Validator::make($data, [
            'details' => 'required|array',
            'details.client_id' => 'required|string',
            'details.scope' => 'required|string',
            'details.refresh_token' => 'required|string',
            'user_id' => 'required|string',
            'value' => 'required|string',
            'expires_at' => 'required|numeric'
        ]);
    }
}
