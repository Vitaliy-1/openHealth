<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use App\Models\LegalEntity;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rule;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Features\SupportRedirects\Redirector;

#[Layout('layouts.guest')]
class Login extends Component
{
    public string $legalEntityUUID = '';

    protected ?LegalEntity $legalEntity;

    public array $legalEntitesList = [];

    public string $email = '';

    public string $password = '';

    public bool $isLocalAuth = false;

    public function mount()
    {
        // List of ALL founded Legal Entites
        $this->legalEntitesList = $this->getLegalEntitesList();
    }

    /**
     * Get all legal entities founded in the system.
     * Reformat it data to the array looks like:
     * [
     *  ['<uuid-1>', 'Legal Entity 1 Name']
     *  ['<uuid-2>', 'Legal Entity 2 Name']
     * ]
     *
     * @return array
     */
    protected function getLegalEntitesList(): array
    {
        $edrList = LegalEntity::select(['id', 'uuid', 'edr'])->get()->toArray();

        return array_map(function($data) {
            $edr = $data['edr'];
            $arr['uuid'] = $data['uuid'];

            if (!empty($edr['name'])) {
                $arr['name'] = $edr['name'];
            } else if(!empty($arr['public_name'])) {
                $arr['name'] = $edr['public_name'];
            }

            return $arr;
        }, $edrList);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(): RedirectResponse|Redirector
    {
        $key = $this->throttleKey();

        $credentials = $this->validate();

        // This need to avoid further user authentication for local auth
        if (!empty($this->legalEntityUUID)) {
            unset($credentials['legalEntityUUID']);
        }

        // Check if user doesn't block by attempts exceeding
        if (! $this->ensureIsNotRateLimited($credentials)) {
            // Number of seconds before login retry
            $seconds = RateLimiter::availableIn($key);

            return Redirect::route('login')->with('error', __('auth.throttle', [
                'minutes' => ceil($seconds / 60),
                'seconds' => $seconds
            ]));
        }

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            $this->addError('email', __('auth.login.error.validation.auths'));
            return back();
        }

        // Save user's email into the session, required to check whether we can allow access on the test server
        if (App::isLocal()) {
            session()->put('selected_email', $this->email);
        }

        if (!$user->hasVerifiedEmail()) {
            // Save user's id to send a verification link again (if needed)
            session()->put('unverified_user_id', $user->id);
            $this->redirect(route('verification.notice'), navigate: true);
        }

        if (!$this->isLocalAuth) {
            if (!empty($this->legalEntityUUID)) {
                // Temporary save the UUID of the selected Legal Entity
                session()->put('selected_legal_entity_uuid_for_ehealth', $this->legalEntityUUID);
            } else {
                Log::error("Legal entity hasn't been choose for email {$user->email}");
                return back();
            }

            return Redirect::to(
                $this->buildEHealthLoginUrl($user)
            );
        }

        if (!Auth::attempt($credentials)) {
            RateLimiter::hit($key, config('ehealth.auth.decay_seconds'));
            $this->addError('email', __('auth.login.error.validation.credentials'));
            return back();
        }

        $this->clearLoginAttempts();
        Session::regenerate();

        // Get an array of the LegalEntity id's connected to this $user
        $accessibleLegalEntities = $user->accessibleLegalEntities()->toArray();

        if (!empty($accessibleLegalEntities)) {
            session()->flash('user_accessible_legal_entities', $accessibleLegalEntities);

            return redirect( route('legalEntity.select'));
        } else {
            return redirect( route('legal-entity.new.create'));
        }
    }

    protected function rules(): array
    {
        $uuids = array_map(fn($arr) => $arr['uuid'], $this->legalEntitesList);

        return array_filter([
            'email' => 'required|email',
            'password' => $this->isLocalAuth ? 'required|string' : 'nullable',
            'legalEntityUUID' => !$this->isLocalAuth
                ? ['required', Rule::in($uuids)]
                : null,
        ]);
    }

    public function messages()
    {
        return [
            'legalEntityUUID.required' => __('forms.choose_legal_entity'),
            'legalEntityUUID.in' => __('forms.del_and_choose_value'),
        ];
    }

    /**
     * Ensure the authentication request is not rate limited
     *
     * @return bool
     */
    protected function ensureIsNotRateLimited(array $credentials): bool
    {
        $key = $this->throttleKey();

        // Check if already has blocking
        if (cache()->has("login_lockout:{$key}")) {
            Log::warning(__('auth.login.error.lockout', [], 'en'), [
                'ip' => request()->ip(),
                'email' => $credentials['email']
                ]);

            return false;
        }

        if (! RateLimiter::tooManyAttempts($key, config('ehealth.auth.max_login_attempts'))) {
            return true;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($key);

        cache()->put("login_lockout:{$key}", true, now()->addSeconds($seconds));

        $this->addError('email', __('auth.login.error.exceed_login_attempts'));

        return false;
    }

    /**
     * Clear unsuccessfull login attempt data after success login
     *
     * @return void
     */
    protected function clearLoginAttempts(): void
    {
        $key = $this->throttleKey();

        RateLimiter::clear($this->throttleKey());

        cache()->forget("login_lockout:{$key}");
    }

    /**
    * Prepare login URL for eHealth depending on the user credentials and redirect URI
    *
    * @param $user
    *
    * @return string
    */
    protected function buildEHealthLoginUrl(User $user): string
    {
        // Base URL and client ID
        $baseUrl = config('ehealth.api.auth_host');
        $redirectUri = config('ehealth.api.redirect_uri');

        $selectedLegalEntityClientId = $this->getLegalEntityClientIdFromUuid($this->legalEntityUUID);

        $legalEntityId = LegalEntity::byUuid($this->legalEntityUUID)->first()->id;

        // Base query parameters
        $queryParams = [
            'client_id' => $selectedLegalEntityClientId ?? '',
            'redirect_uri' => $redirectUri,
            'response_type' => 'code'
        ];

        // Set a temporary team/legalEntity ID, this should be overridden once a user actually logs in.
        // Spatie Permissions sets permissions globally, they can't be loaded by querying relations tables
        setPermissionsTeamId($legalEntityId);
        $user->unsetRelation('roles')->unsetRelation('permissions');

        // Additional query parameters if email is provided
        if (!empty($user->email)) {
            $queryParams['email'] = $user->email;
            $queryParams['scope'] = $user->getScopes($selectedLegalEntityClientId);
        }

        session()->put(config('ehealth.api.auth_ehealth'), $user->id);

        // Build the full URL with query parameters
        return $baseUrl . '?' . http_build_query($queryParams);
    }

    /**
     * Helper to get client_id from selected record by legalEntityUUID.
     * This is crucial if the user desn't have a default LegalEntity assigned yet.
     *
     * @param string $uuid
     *
     * @return string|null
     */
    protected function getLegalEntityClientIdFromUuid(string $uuid): ?string
    {
        return LegalEntity::byUuid($uuid)->first()?->clientId;
    }

    /**
     * Get the authentication rate limiting throttle key.
     *
     * @return string
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}
