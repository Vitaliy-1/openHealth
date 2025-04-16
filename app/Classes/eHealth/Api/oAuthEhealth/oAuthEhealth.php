<?php

namespace App\Classes\eHealth\Api\oAuthEhealth;


use Exception;
use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\LegalEntity;
use App\Models\Relations\Party;
use App\Classes\eHealth\Request;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use App\Repositories\EmployeeRepository;
use App\Classes\eHealth\Api\EmployeeApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Http\Controllers\Auth\LogoutController;
use App\Models\Employee\EmployeeRequest;

class oAuthEhealth implements oAuthEhealthInterface
{
    const OAUTH_TOKENS = '/oauth/tokens';
    const OAUTH_USER = '/oauth/user';
    const OAUTH_APPROVAL = '/oauth/apps/authorize';
    const OAUTH_NONCE = '/oauth/nonce';

    /**
     * This method is called when the user is redirected back from eHealth after it's authentication
     *
     * @return mixed|RedirectResponse
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
            return redirect()->route('login');
        }

        $code = request()->input('code');

        $authResponse = $this->authenticate($code);

        $authResponseData = $this->validateAuthResponse($authResponse);

        if (empty($authResponseData)) {
            return $this->logoutUser('auth.login.error.server.response');
        }

        $authUserUUID = $authResponseData['user_id'];
        $authLegalEntityUUID = $authResponseData['details']['client_id'];

        // Get user trying to login
        $user = User::where('uuid', $authUserUUID)->first();

        if ($user) {
            Log::info(__('auth.login.success.user_auth', [], 'en'), ['User ID' => $user->id]);

            return redirect()->route('dashboard');
        }

        // If user not found, try to get user from eHealth response by auth user data request
        $authorizedUserData = $this->validateUserDetailsResponse($this->getUser());

        if (empty($authorizedUserData)) {
            return $this->logoutUser('auth.login.error.server.response');
        }

        $userUUID = $authorizedUserData['id'];
        $userEmail = $authorizedUserData['email'];

        // TODO pass this through validate
        if ($userUUID !== $authUserUUID) {
            $this->logoutUser('auth.login.error.user_identity');
        }

        $legalEntity = LegalEntity::where('uuid', $authLegalEntityUUID)->first();

        // Error if user doesn't have legal entity
        if (!$legalEntity) {
            return $this->logoutUser('auth.login.error.legal_entity_identity');
        }

        $user = User::where('email', $userEmail)->where('legal_entity_id', $legalEntity->id)->first();

        $employee = $this->checkUserAsEmployee($user);

        $employeeInstance = null;

        if (!$employee) {
            $employeeInstance = $user->employeeRequests()->where('user_id', $user->id)->first();

            // If user hasn't an employeeRequest this means something really going wrong
            if (!$employeeInstance) {
                return $this->logoutUser('auth.login.error.server.user_credentials');
            }
        } else {
            $employeeInstance = $employee;
        }

        $employeeType = $employeeInstance->employeeType;

        $isAuntenticated = $employeeType === 'OWNER'
            ? $this->authenticateNewOwner($employeeInstance, $user, $authUserUUID)
            : $this->authenticateNewEmployee($employeeInstance, $user, $authUserUUID);

        // Logout if user is not authenticated properly
        if (!$isAuntenticated) {
            return $this->logoutUser('auth.login.error.user_authentication');
        }

        Log::info(__('auth.login.success.new_user_auth', [], 'en'), ['User ID' => $user->id]);

        return redirect()->route('dashboard')->with('success', __('auth.login.success.new_user_auth')); // Add this line
    }

    /**
     * Authenticate new OWNER and save data to the database
     * Also check if the other employees is already exists in the system and save its data too
     *
     * @param EmployeeRequest $employeeRequest Only EmployeeRequest type because up to now we should have only the EmployeeRequest for the OWNER
     * @param User $user
     * @param string $authUserUUID
     *
     * @return bool
     */
    public function authenticateNewOwner(EmployeeRequest $employeeRequest, User $user, string $authUserUUID): bool|RedirectResponse
    {
        $employeePosition = $employeeRequest->position;

        $employeeListURL = $this->employeesListUrl($user->legalEntity->uuid);

        // List of the users (employees) belongs to the same legal entity
        $employeeList = $this->getEmployeesList($employeeListURL);

        $employeeData = [];

        foreach ($employeeList as $employee) {
            $employeeData = $employee;

            if ($employee['position'] === $employeePosition  && $employee['employee_type'] === 'OWNER') {

                $employeeResponse = $this->getEmployeeData($employee['id']);
                $employeeData = $this->validateEmployeeData($employeeResponse);

                if (empty($employeeData)) {
                    return false;
                }

                $employeeData['party']['email'] = $user->email;
            }

            $employeeData['legal_entity_id'] = $employeeData['legal_entity']['id'];
            $employeeData['inserted_at'] = Carbon::now()->format('Y-m-d');
            $employeeData['updated_at'] = Carbon::now()->format('Y-m-d');

            if (!$this->saveEmployeeData($user,  $employeeRequest->party, $employeeData, $authUserUUID)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Authenticate new employee and save data to the database
     *
     * @param Employee $employee Only Employee type because up to now we should have all the data for employees
     * @param User $user
     * @param string $authUserUUID
     *
     * @return bool
     */
    public function authenticateNewEmployee(Employee $employee, User $user, string $authUserUUID): bool|RedirectResponse
    {
        $employeePosition = $employee->position;
        $employeeType = $employee->employeeType;
        $employeeListURL = $this->employeesListUrl($user->legalEntity->uuid);

        // List of the users (employees) belongs to the same legal entity
        $employeeList = $this->getEmployeesList($employeeListURL);

        $employeeData = [];

        foreach ($employeeList as $employee) {
            $employeeData = $employee;

            if ($employee['position'] === $employeePosition  && $employee['employee_type'] === $employeeType) {

                $employeeResponse = $this->getEmployeeData($employee['id']);
                $employeeData = $this->validateEmployeeData($employeeResponse);

                if (empty($employeeData)) {
                    return false;
                }

                $party  = $employeeData['party'];

                if ($party['first_name'] !== $employee->party->firstName ||
                    $party['last_name'] !== $employee->party->lastName ||
                    $party['second_name'] !== $employee->party->secondName
                ) {
                    continue;
                }

                $employeeData['party']['email'] = $user->email;

                break;
            }
        }

        $employeeData['legal_entity_id'] = $employeeData['legal_entity']['id'];
        $employeeData['inserted_at'] = Carbon::now()->format('Y-m-d');
        $employeeData['updated_at'] = Carbon::now()->format('Y-m-d');

        if (!$this->saveEmployeeData($user,  $employee->party, $employeeData, $authUserUUID)) {
            return false;
        }

        return true;
    }

    /**
     * Save employee data to the database
     *
     * @param User $user
     * @param Party|null $party
     * @param array $employeeData Data received from request to eHealth (GetEmployeesList|GetEmployeeDetails)
     * @param string $authUserUUID
     *
     * @return bool
     */
    protected function saveEmployeeData(User $user, Party|null $party, array $employeeData, string $authUserUUID): bool
    {
        $employeeResponse = schemaService()->setDataSchema($employeeData, app(EmployeeApi::class))
            ->responseSchemaNormalize()
            ->replaceIdsKeysToUuid(['id', 'legalEntityId', 'divisionId', 'partyId'])
            ->snakeCaseKeys(true)
            ->getNormalizedData();

        $employeeResponse['user_id'] = $user->id;

        $user->uuid = $authUserUUID;

        try {
            DB::transaction(function() use($employeeResponse, $user, $party) {
                // Update Party uuid because it is hasn't actual value in the employeeRequest
                if (!empty($party) && $party->uuid !== $employeeResponse['party']['uuid']) {
                    $party->uuid = $employeeResponse['party']['uuid'];

                    $party->save();
                }

                $user->save();

                app(EmployeeRepository::class)->saveEmployeeData($employeeResponse, $user->legalEntity, new Employee());
            });
        } catch (Exception $err) {
            Log::error(__('auth.login.error.data_saving'), ['error' => $err->getMessage()]);

            return false;
        }

        return true;
    }

    /**
     * Authenticate user with eHealth
     *
     * @param string $code
     *
     * @return mixed
     */
    public function authenticate(string $code): mixed
    {
        $user = User::find(\session()->get('user_id_auth_ehealth'));

        if (!$user) {
            return redirect()->route('login')->with('error', __('auth.login.error.user_identity'));
        }

        $data = [
            'token' => [
                'client_id'     => $user->legalEntity->client_id ?? '',
                'client_secret' => $user->legalEntity->client_secret ?? '',
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => config('ehealth.api.redirect_uri'),
                'scope'         => $user->getScopes()
            ]
        ];

        $response = new Request('POST', self::OAUTH_TOKENS, $data, false)->sendRequest();

        self::setToken($response);

        // $this->login($user);
        Auth::login($user);

        return $response;
    }

    //TODO: Check if it works
    public function approve(): void
    {
        $user = User::find(\session()->get('user_id_auth_ehealth'));

        $queryParams = [
            'app'=> [
                'client_id'     => $user->legalEntity->client_id ?? '',
                'redirect_uri'  => config('ehealth.api.redirect_uri'),
                'scope'         => $user->getScopes()
            ]
        ];

        new Request('POST', self::OAUTH_APPROVAL, $queryParams)->sendRequest();
    }

    //TODO: Check if it works
    public function nonce():void
    {
        $queryParams = [
            'client_id'     => $user->legalEntity->client_id ?? '',
            'client_secret' => $user->legalEntity->client_secret ?? '',
        ];

        new Request('POST', self::OAUTH_NONCE, $queryParams)->sendRequest();
    }

    /**
     * Check if user is employee (is not null)
     *
     * @param User $user
     *
     * @return Employee|null
     */
    protected function checkUserAsEmployee(User $user): ?Employee
    {
        return $user->employees()->where('user_id', $user->id)->first();
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

        session()->put('user_id_auth_ehealth', $user->id);

        // Build the full URL with query parameters
        return $baseUrl . '?' . http_build_query($queryParams);
    }

    public static function employeesListUrl(string $legalEntityUUID): string
    {
        // Base URL (API endpoint)
        $baseUrl = config('ehealth.api.domain');

        // Base query parameters
        $queryParams = [
            'legal_entity_id' => $legalEntityUUID,
            'page'          => 1,
            'per_page'      => 100
        ];

        // Build the full URL with query parameters
        return $baseUrl . '/api/employees?' . http_build_query($queryParams);
    }

    public static function setToken($data):void
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
        return new Request('GET', self::OAUTH_USER, [])->sendRequest();
    }

    /**
     * LogoutUser if any error occurs
     *
     * @param string $err Text error message via translation
     *
     * @return RedirectResponse
     */
    public static function logoutUser(string $err = ''): RedirectResponse
    {
        // Logout user from the system
        app(LogoutController::class)->logout(request(), false);

        // Forget token
        self::forgetToken(false);

        // Forget session data
        session()->forget('user_id_auth_ehealth');

        // Redirect to login page with error message
        $err = $err ? $err : 'auth.login.error.common';

        $logMessage = __($err, [], 'en');

        Log::error($logMessage);

        $errorMessage = __($err);

        return redirect('/login')->with('error', $errorMessage);
    }

    public static function getEmployeesList(string $url):array
    {
        return new Request('GET', $url, [])->sendRequest();
    }

    public static function getEmployeeData(string $employeeId):array
    {
        $url = config('ehealth.api.domain') . "/api/employees/$employeeId";

        return new Request('GET', $url, [])->sendRequest();
    }

    public static function getEmployeeRequeestData(string $requestId):array
    {
        $url = config('ehealth.api.domain') . "/api/employee_requests/$requestId";

        return new Request('GET', $url, [])->sendRequest();
    }

    public static function forgetToken(bool $doRedirect = true)
    {
        if (Session::has('auth_token')){
            Session::forget('auth_token');
            Session::forget('auth_token_expires_at');
            Session::forget('refresh_token');
            Session::forget('refresh_token_expires_at');
        }

        if ($doRedirect) {
            return redirect()->route('login');
        }
    }

    public function getApikey(): string
    {
        return config('ehealth.api.api_key');
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

        $request = new Request('POST', self::OAUTH_TOKENS, $data, false)->sendRequest();

        self::setToken($request);

        return $request;
    }

    public function isLoggedIn(): bool
    {
        return Session::has('auth_token') && Session::has('auth_token_expires_at');
    }

    /**
     * Check authentication $response schema for errors
     *
     * @return array Returned only specified fields
     */
    protected function validateAuthResponse(mixed $data): array
    {
        $validator = Validator::make($data, [
            'details' => 'required|array',
            'details.client_id' => 'required|string',
            'details.scope' => 'required|string',
            'user_id' => 'required|string',
            'value' => 'required|string'
        ]);

        if ($validator->fails()) {
            Log::error('Legal Entity Response Schema:', ['errors' => $validator->errors()]);

            return [];
        }

        return $validator->validated();
    }

    /**
     * Check authentication $response schema for errors
     *
     *  @return array Returned only specified fields
     */
    protected function validateUserDetailsResponse(mixed $data): array
    {
        $validator = Validator::make($data, [
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

        if ($validator->fails()) {
            Log::error('Legal Entity Response Schema:', ['errors' => $validator->errors()]);

            return [];
        }

        return $validator->validated();
    }

    /**
     * Check employee details $response schema for errors.
     *
     * @return array Returned only specified fields
     */
    protected function validateEmployeeData(mixed $data): array
    {
        $validator = Validator::make($data, [
            'division' => 'nullable|array',
            'division.id' => 'required_with:division|string',
            'division.name' => 'required_with:division|string',
            'division.legal_entity_id' => 'nullable|string',
            'employee_type' => 'required|string',
            'end_date' => 'nullable|string',
            'id' => 'required|string',
            'is_active' => 'required|bool',
            'legal_entity' => 'required|array',
            'legal_entity.id' => 'required|string',
            'party' => 'required|array',
            'party.id' => 'required|string',
            'party.first_name' => 'required|string',
            'party.last_name' => 'required|string',
            'party.second_name' => 'nullable|string',
            'party.no_tax_id' => 'nullable|bool',
            'party.gender' => 'nullable|string',
            'party.verification_status' => 'required|string',
            'party.tax_id' => 'nullable|string',
            'party.birth_date' => 'nullable|string',
            'party.phones' => 'nullable|array',
            'party.phones.*.type' => 'required_with:party.phones|string',
            'party.phones.*.number' => 'required_with:party.phones|string',
            'party.working_experience' => 'nullable',
            'party.about_myself' => 'nullable',
            'start_date' => 'required|string',
            'status' => 'required|string',
            'position' => 'required|string',
            'doctor' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            Log::error('Legal Entity Response Schema:', ['errors' => $validator->errors()]);

            return [];
        }

        return $validator->validated();
    }
}
