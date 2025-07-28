<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Auth\EHealth\Services\TokenStorage;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LegalEntity;
use App\Repositories\Repository;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use App\Classes\eHealth\Api\EmployeeApi;
use App\Models\Employee\EmployeeRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Classes\eHealth\Request as EHealthRequest;
use Illuminate\Contracts\Validation\Validator as ResponseValidator;
use Illuminate\Validation\Rule;

class EHealthLoginController extends Controller
{
    /**
     * This method is called when the user is redirected back from eHealth after it's successful authentication
     *
     * @return null|RedirectResponse
     */
    public function __invoke(Request $request): ?RedirectResponse
    {
        // get the email entered by the user in the login form
        $sessionEmail = session()->pull('selected_email');
        $testUser = $sessionEmail && in_array($sessionEmail, config('ehealth.test.emails'));

        /* exchange code to token */
        if (
            (config('ehealth.api.callback_prod') === false) &&
            // Pass certain emails anyway for testing purposes
            !$testUser
        ) {
            $code = $request->input('code');
            $url = 'http://localhost/ehealth/oauth?code=' . $code;

            return redirect($url);
        }

        if (!$request->has('code')) {
            return Redirect::route('login');
        }

        $selectedLegalEntityUuidFromSession = session()->pull('selected_legal_entity_uuid_for_ehealth');

        if (!$selectedLegalEntityUuidFromSession) {
            Log::warning('Legal Entity is not selected');

            return $this->breakAuth('auth.login.error.legal_entity_identity');
        }

        $eHealthTokenResponseData = $this->sendEHealthTokenRequest($request, $selectedLegalEntityUuidFromSession);

        if (empty($eHealthTokenResponseData)) {
            return Redirect::route('login')->with('error', __('auth.login.error.user_identity'));
        }

        $validator = $this->validateEHealthTokenResponse($eHealthTokenResponseData);

        if ($validator->fails()) {
            Log::error(__('auth.login.error.validation.auth', [], 'en'), ['errors' => $validator->errors()]);
            return Redirect::route('login')->with('error', __('auth.login.error.validation.auth'));
        }

        $validatedEHealthTokenData = $validator->validated();

        app(TokenStorage::class)->store($validatedEHealthTokenData);

        $authUserUUID = $validatedEHealthTokenData['user_id'];
        $authLegalEntityUUID = $validatedEHealthTokenData['details']['client_id'];

        /* This checks if the user chose one LE, but eHealth returned another */
        if ($selectedLegalEntityUuidFromSession !== $authLegalEntityUUID) {
            Log::warning('User selected a different Legal Entity in form than eHealth returned.', [
                'Selected in form' => $selectedLegalEntityUuidFromSession,
                'Returned by eHealth' => $authLegalEntityUUID,
                'User UUID' => $authUserUUID,
            ]);

            return $this->breakAuth('auth.login.error.legal_entity_identity');
        }

        $legalEntity = LegalEntity::byUuid($authLegalEntityUUID)->firstOrFail();

        $isFirstLogin = !User::where('uuid', $authUserUUID)->first()?->uuid;

        auth()->shouldUse('ehealth');

        $user = $this->checkLoginedUser($legalEntity, $authUserUUID);

        if (!$user) {
            Log::error(__('auth.login.error.user_authentication', [], 'en'));
            return $this->breakAuth('auth.login.error.user_authentication');
        }

        // We must ensure that the user entered test email in the login form corresponds to the user's eHealth email
        if ($testUser && ($sessionEmail !== $user->email)) {
            Log::error(__('auth.login.error.test_user_email', [], 'en'));
            return $this->breakAuth('auth.login.error.test_user_email');
        }

        auth('ehealth')->login($user);

        /* Check if the user has assigned LegalEntity */
        if ($legalEntity) {
            Log::info(__('auth.login.success.user_auth', [], 'en'), ['User ID' => $user->id]);

            return Redirect::route('dashboard', [$legalEntity])->with('success', $isFirstLogin ? __('auth.login.success.new_user_auth') : null);
        } else {
            Auth::guard('ehealth')->logout();

            return Redirect::route('login')->with('error', __('auth.login.error.legal_entity.wrong_request'));
        }
    }

    /**
     * Check if this first user's login. If so then all user's data (as employee) has to be updated
     *
     * @param \App\Models\LegalEntity $legalEntity
     * @param string $authUserUUID
     *
     * @return User|null
     */
    protected function checkLoginedUser(LegalEntity $legalEntity, string $authUserUUID): ?User
    {
        // Get user trying to login
        $alreadyAuthorizedUser = User::where('uuid', $authUserUUID)->first();
        $authLegalEntityUUID = $legalEntity->uuid;

        if ($alreadyAuthorizedUser) {
            /**
             * must set actual permissions for the particular legal entity, see:
             * https://spatie.be/docs/laravel-permission/v6/basic-usage/teams-permissions#content-working-with-teams-permissions
             */
            setPermissionsTeamId($legalEntity->id);
            $alreadyAuthorizedUser->unsetRelation('roles')->unsetRelation('permissions');

            // Check if user has connection to selected Legal Entity
            if (!$alreadyAuthorizedUser->hasAccessToLegalEntityByUuid($authLegalEntityUUID)) {
                Log::error(__('auth.login.error.user_authentication', [], 'en') . __(" User {$alreadyAuthorizedUser->uuid} does not have required access to LegalEntity {$authLegalEntityUUID} after sync."));

                return null;
            }

            // Check if user has more than one Employee Role that hasn't been authorized
            if (!Repository::employee()->authenticateNewEmployees($authLegalEntityUUID, $alreadyAuthorizedUser, $authUserUUID)) {
                Log::error(__('auth.login.error.user_authentication', [], 'en'));

                return null;
            }

            // Check employee for updates
            if (!Repository::employee()->checkForEmployeeUpdate($legalEntity, $alreadyAuthorizedUser, $authUserUUID)) {
                Log::error(__('auth.login.error.user_employee_update', [], 'en'));

                return null;
            }

            return $alreadyAuthorizedUser;
        }

        // If user not found, try to get user from eHealth response by Get User Details request
        $authorizedUserValidator = $this->validateUserDetailsResponse(EmployeeApi::getUserDetails());

        /** @var \Illuminate\Contracts\Validation\Validator $authorizedUserValidator */
        if ($authorizedUserValidator->fails()) {
            Log::error(__('auth.login.error.vlidation.user_details', [], 'en'), ['errors' => $authorizedUserValidator->errors()]);

            return null;
        }

        $authorizedUserData = $authorizedUserValidator->validated();

        $userUUID = $authorizedUserData['id'];
        $userEmail = $authorizedUserData['email'];

        // Check if user doesn't change email through ESOZ login
        if ($userUUID !== $authUserUUID) {
            Log::error(__('auth.login.error.user_identity', [], 'en'));

            return null;
        }

        $user = User::where('email', $userEmail)->first();

        if (!$user) {
            Log::error(__('auth.login.error.user_not_found_by_email', [], 'en') . ": {$userEmail}");

            return null;
        }

        /**
         * must set actual permissions for the particular legal entity, see:
         * https://spatie.be/docs/laravel-permission/v6/basic-usage/teams-permissions#content-working-with-teams-permissions
         */
        setPermissionsTeamId($legalEntity->id);
        $user->unsetRelation('roles')->unsetRelation('permissions');

        // Get Employee or EmployeeRequest instance for specified user and it's Legal Entity ID
        $employeeRequest = EmployeeRequest::employeeInstance($user->id, $legalEntity->uuid, ['OWNER'], true)->first();

        $isAuntenticated = $employeeRequest
            ? Repository::employee()->authenticateNewOwner($employeeRequest, $user, $authUserUUID)
            : Repository::employee()->authenticateNewEmployees($legalEntity->uuid, $user, $authUserUUID);

        // Logout if user is not authenticated properly
        if (!$isAuntenticated) {
            Log::error(__('auth.login.error.user_authentication', [], 'en'), ['error' => 'Wrong authenticateNewOwner or authenticateNewEmployees workflow'] );

            return null;
        }

        return $user;
    }

    /**
     * Send request to EHealth to get the token for an auth code,
     * see: https://uaehealthapi.docs.apiary.io/#reference/public.-medical-service-provider-integration-layer/oauth/exchange-oauth-code-grant-to-access-token
     * @return array
     */
    protected function sendEHealthTokenRequest(Request $request, string $selectedLegalEntityUuidFromSession): array
    {
        return EmployeeApi::authenticate(
            $request->input('code'),
            $selectedLegalEntityUuidFromSession,
        );
    }

    /**
     * If any error occurs...
     *
     * @param string $err Text error message via translation
     *
     * @return RedirectResponse
     */
    protected function breakAuth(string $err = ''): RedirectResponse
    {
        $authEhealth = config('ehealth.api.auth_ehealth');

        // Logout user from the system
        if (session()->has($authEhealth) || session()->has(config('ehealth.api.oauth.bearer_token'))) {
            new EHealthRequest('POST', config('ehealth.api.oauth.logout'), [])->sendRequest();

            // Forget bearer token and other token's data
            app(TokenStorage::class)->clear();
        }

        // Forget session data
        session()->forget($authEhealth);

        // Redirect to login page with error message
        $err = $err ? $err : 'auth.login.error.common';

        $logMessage = __($err, [], 'en');

        Log::error($logMessage);

        $errorMessage = __($err);

        return Redirect::to('/login')->with('error', $errorMessage);
    }

    /**
     * Validate EHealth token exchange response
     * see response example: https://uaehealthapi.docs.apiary.io/#reference/public.-medical-service-provider-integration-layer/oauth/exchange-oauth-code-grant-to-access-token?console=1
     * @return ResponseValidator Returned only specified fields
     */
    protected function validateEHealthTokenResponse(array $data): ResponseValidator
    {
        return Validator::make($data, [
            'details' => ['required', 'array'],
            'details.client_id' => ['required','uuid', Rule::exists('legal_entities', 'uuid')],
            'details.scope' => [
                'required',
                function (string $attribute, string $value, Closure $fail) {
                    if ($attribute != 'details.scope') {
                        return;
                    }

                    $scopesReceived = explode(' ', $value);
                    $scopesAvailable = collect(config('ehealth.roles'))
                        ->flatten()
                        ->unique()
                        ->toArray();
                    $diff = array_diff($scopesReceived, $scopesAvailable);

                    if (empty($diff)) {
                        return;
                    }

                    $fail("The following scopes are unsupported: " . implode(', ', $diff) );
                }
            ],
            'details.refresh_token' => ['required','string'],
            'user_id' => ['required', 'uuid'],
            'value' => ['required', 'string'],
            'expires_at' => ['required', 'numeric'],
        ]);
    }

    /**
     * Check authentication $response schema for errors
     *
     *  @return ResponseValidator Returned only specified fields
     */
    protected function validateUserDetailsResponse(array $data): ResponseValidator
    {
        return Validator::make($data, [
            'id' => 'required|string',
            'email' => 'required|string',
            'is_blocked' => 'required|bool',
            'block_reason' => 'nullable|string',
            'person_id' => 'nullable|string',
            'tax_id' => 'nullable|string',
            'settings' => 'nullable|array',
            'inserted_at' => 'required|string',
            'updated_at' => 'required|string',
        ]);
    }
}
