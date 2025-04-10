<?php

namespace App\Auth\EHealth\Services;

use App\Models\User;
use App\Models\LegalEntity;
use App\Auth\EHealth\Services\TokenStorage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use App\Repositories\EmployeeRepository;
use App\Classes\eHealth\Api\EmployeeApi;
use App\Models\Employee\EmployeeRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Classes\eHealth\Request as eHealthRequest;
use Illuminate\Contracts\Validation\Validator as ResponseValidator;

class EHealthLoginUserHandler
{
    protected EmployeeRepository $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Check if this first user's login. If so then all user's data (as employee) has to be updated
     *
     * @param \App\Models\LegalEntity $legalEntity
     * @param string $authUserUUID
     *
     * @return User|null
     */
    public function checkLoginedUser(LegalEntity $legalEntity, string $authUserUUID): ?User
    {
        // Get user trying to login
        $alreadyAuthorizedUser = User::where('uuid', $authUserUUID)->first();
        $authLegalEntityUUID = $legalEntity->uuid;

        if ($alreadyAuthorizedUser) {
            // Check if user has more than one Employee Role that hasn't been authorized
            if (!$this->employeeRepository->authenticateNewEmployees($authLegalEntityUUID, $alreadyAuthorizedUser, $authUserUUID)) {
                Log::error(__('auth.login.error.user_authentication', [], 'en'));

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

        $user = User::byEmailAndLegalEntity($userEmail, $legalEntity->id)->first();

        // Get Employee or EmployeeRequest instance for specified user and it's Legal Entity ID
        $employeeRequest = EmployeeRequest::employeeInstance($user->id, $legalEntity->uuid, ['OWNER'], true)->first();

        $isAuntenticated = $employeeRequest
            ? $this->employeeRepository->authenticateNewOwner($employeeRequest, $user, $authUserUUID)
            : $this->employeeRepository->authenticateNewEmployees($legalEntity->uuid, $user, $authUserUUID);

        // Logout if user is not authenticated properly
        if (!$isAuntenticated) {
            return null;
        }

        return $user;
    }

    /**
     * If any error occurs...
     *
     * @param string $err Text error message via translation
     *
     * @return RedirectResponse
     */
    public function breakAuth(string $err = ''): RedirectResponse
    {
        $authEhealth = config('ehealth.api.auth_ehealth');

        // Logout user from the system
        if (session()->has($authEhealth) || session()->has(config('ehealth.api.oauth.bearer_token'))) {
            new eHealthRequest('POST', config('ehealth.api.oauth.logout'), [])->sendRequest();

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
            'user_id' => 'required|string',
            'value' => 'required|string'
        ]);
    }

    /**
     * Check authentication $response schema for errors
     *
     *  @return array Returned only specified fields
     */
    public function validateUserDetailsResponse(array $data): ResponseValidator
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
