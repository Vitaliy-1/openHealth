<?php

namespace App\Livewire\LegalEntity;

use Log;
use Exception;
use Validator;
use Carbon\Carbon;
use Livewire\Component;
use App\Traits\FormTrait;
use Livewire\WithFileUploads;
use App\Traits\AddressSearch;
use Illuminate\Support\Facades\DB;
use App\Classes\Cipher\Traits\Cipher;
use App\Classes\Cipher\Api\CipherApi;
use Illuminate\Support\Facades\Cache;
use App\Repositories\PhoneRepository;
use App\Repositories\AddressRepository;
use App\Classes\eHealth\Api\EmployeeApi;
use App\Models\Employee\EmployeeRequest;
use App\Repositories\EmployeeRepository;
use Illuminate\Validation\ValidationException;
use App\Models\LegalEntity as LegalEntityModel;
use App\Livewire\LegalEntity\Forms\LegalEntitiesForms;
use App\Livewire\LegalEntity\Forms\LegalEntitiesRequestApi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LegalEntity extends Component
{
    use FormTrait,
        Cipher,
        WithFileUploads,
        AddressSearch;

    /**
     * @var LegalEntitiesForms The Form
     */
    public LegalEntitiesForms $legalEntityForm;

    /**
     * @var LegalEntityModel|null The Legal Entity being filled
     */
    public ?LegalEntityModel $legalEntity;

    /**
     * @var AddressRepository|null Save the address data in separate table
     */
    protected ?AddressRepository $addressRepository;

    protected EmployeeRepository $employeeRepository;

    protected PhoneRepository $phoneRepository;

    /**
     * @var object|null
     */
    public ?object $file = null;

    /**
     * @var array|string[] Get dictionaries keys
     */
    public array $dictionaryNames = [
        'PHONE_TYPE',
        'LICENSE_TYPE',
        'SETTLEMENT_TYPE',
        'GENDER',
        'SPECIALITY_LEVEL',
        'ACCREDITATION_CATEGORY',
        'POSITION',
        'DOCUMENT_TYPE'
    ];

    /**
     * @return void set cache keys
     */
    public function boot(
        AddressRepository $addressRepository,
        PhoneRepository $phoneRepository
    ): void{
        $this->addressRepository = $addressRepository;
        $this->phoneRepository = $phoneRepository;
    }

    public function mount(): void
    {
        $this->getLegalEntity();

        $this->mergeAddress($this->convertArrayKeysToCamelCase($this->legalEntity->toArray())['address'] ?? []);

        $this->getDictionary();

        $this->setCertificateAuthority();

        $this->getOwnerFields();
    }

    /**
     * @return void
     */
    public function getOwnerFields(): void
    {
        // Get owner dictionary fields
        $fields = [
            'POSITION'      => ['P1', 'P2', 'P3', 'P32', 'P4', 'P6', 'P5'],
            'DOCUMENT_TYPE' => ['PASSPORT', 'NATIONAL_ID']
        ];

        // Get dictionaries
        foreach ($fields as $type => $keys) {
            $this->dictionaries[$type] = $this->getDictionariesFields($keys, $type);
        }
    }

    public function getLegalEntity(): void
    {
        // Try to get the LegalEntity assigned for the user or from the cache
        $this->legalEntity = $this->getLegalEntityFromAuth() ?? $this->getLegalEntityFromCache();

        // If a LegalEntity is found, fill the form with its data
        if ($this->legalEntity) {
            $modelData = $this->convertArrayKeysToCamelCase($this->legalEntity->toArray());
            $modelData['license'] = [];

            if (!empty($modelData['licenses'])) {
                $modelData['license'] = $modelData['licenses'] ?? [];
                unset($modelData['licenses']);
            }

            $this->legalEntityForm->fill($modelData);
        } else {
            $this->legalEntity = new LegalEntityModel();
        }
    }

    protected function mergeAddress(array $address): void
    {
        if (empty($address)) {
            return;
        }

        foreach($address as $key => $value) {
            $this->address[$key] = $value;
        }

        if (isset($this->address['area'])) {
            $this->address['area'] = mb_strtoupper($this->address['area']);
        }
    }

    private function getLegalEntityFromCache(): ?LegalEntityModel
    {
        return Cache::get($this->entityCacheKey) ?? null;
    }

    /**
     * Get the legal entity associated with the currently authenticated user.
     *
     * @return LegalEntityModel|null
     */
    private function getLegalEntityFromAuth(): ?LegalEntityModel
    {
        return auth()->user()->legalEntity ?? null;
    }

    /**
     * Get list of the Authority Centers of the Key's Certification
     *
     * @return array|null
     */
    public function setCertificateAuthority(): array|null
    {
        return $this->getCertificateAuthority = $this->getCertificateAuthority();
    }

    // TODO: implement in the future release when EDRPOU will validate from outside also
    public function saveLegalEntityFromExistingData($data): void
    {
        $normalizedData = [];

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                switch ($key) {
                    case 'id':
                        $normalizedData['uuid'] = $value;
                        break;
                    case 'residence_address':
                        $normalizedData['residence_address'] = $value;
                        break;
                    case 'edr':
                        foreach ($data['edr'] as $edrKey => $edrValue) {
                            $normalizedData[$edrKey] = $edrValue;
                        }
                        break;
                    default:
                        $normalizedData[$key] = $value;
                        break;
                }
            }

            $this->legalEntity->fill($normalizedData);

            $this->legalEntityForm->fill($normalizedData);

            if (!Cache::has($this->entityCacheKey) || $this->checkChanges()) {
                Cache::put($this->entityCacheKey, $this->legalEntity, now()->days(90));
            }
        }
    }

    public function saveEmployeeResponse($response, $legalEntity, int|null $userId = null): void
    {
        $employeeResponse = schemaService()->setDataSchema($response, app(EmployeeApi::class))
            ->responseSchemaNormalize()
            ->replaceIdsKeysToUuid(['id', 'legalEntityId', 'divisionId', 'partyId'])
            ->snakeCaseKeys(true)
            ->getNormalizedData();

        $employeeResponse['user_id'] = $userId;

        app(EmployeeRepository::class)->saveEmployeeData($employeeResponse, $legalEntity,new EmployeeRequest());
    }

    /**
     * Step 8 for handling sign legal entity  submission.
     *
     * @throws ValidationException
     */
    public function signLegalEntity(bool $isEdit = false): void
    {
        if ($isEdit) {
                $this->legalEntityForm->onEditValidate();

            if ($this->getErrorBag()->isNotEmpty()) {
                $this->dispatchBrowserEvent('scroll-to-error');
            }
        }

        // $this->legalEntityForm->customRulesValidation();  // TODO: Uncomment this after adding custom rules

        // Prepare data for public offer
        $this->legalEntityForm->publicOffer = $this->preparePublicOffer();

        // Prepare security data
        $this->legalEntityForm->security = $this->prepareSecurityData();

        // Convert form data to an array
        $data = $this->prepareDataForRequest($this->legalEntityForm->toArray());

        $taxId = $this->legalEntityForm->owner['taxId'];

        // Sending encrypted data
        $base64Data = $this->sendEncryptedData($data, $taxId, CipherApi::SIGNATORY_INITIATOR_BUSINESS);

        // Handle errors from encrypted data
        if (isset($base64Data['errors'])) {
            $this->dispatchErrorMessage($base64Data['errors']);
            return;
        }

        // Prepare data for API request
        $response = LegalEntitiesRequestApi::_createOrUpdate([
            'signed_legal_entity_request' => $base64Data,
            'signed_content_encoding'     => 'base64',
        ]);

        // Handle errors from API request
        if (isset($request['errors']) && is_array($response['errors'])) {
            $this->dispatchErrorMessage(__('Запис не було збережено'), $response['errors']);

            return;
        }

        Log::info('Legal Entity Success RESPONSE', $response); // TODO: Important! Delete after testing!!!

        try {
            $response = $this->validateResponseSchema($response);
        } catch (Exception $err) {
            $this->dispatchErrorMessage($err->getMessage());

            return;
        }

        // Handle successful API request
        try {
            $this->handleSuccessResponse($response, $data);
        } catch(Exception $err) {
            // Dispatch error message for possible errors
            $this->dispatchErrorMessage($err->getMessage());
        }
    }

    /**
     * Check $response schema for errors
     */
    protected function validateResponseSchema(mixed $data): array
    {
        $validator = Validator::make($data, [
            'data' => 'required|array',
            'data.edr' => 'required|array',
            "data.edr.edrpou" => "required|string",
            "data.edr.id" => "required|string",
            "data.edr.name" => "required|string",
            'data.edr.short_name' => 'nullable|string',
            'data.edr.public_name' => 'nullable|string',
            'data.edr.legal_form' => 'nullable|string',
            "data.edr.kveds" => 'required|array',
            "data.edr.kveds.*.name" => 'required|string',
            "data.edr.kveds.*.code" => 'required|string',
            "data.edr.kveds.*.is_primary" => 'required|boolean',
            "data.edr.registration_address" => 'required|array',
            "data.edr.registration_address.zip" => 'nullable|string',
            "data.edr.registration_address.country" => 'nullable|string',
            "data.edr.registration_address.address" => 'nullable|string',
            "data.edr.registration_address.parts" => 'nullable|array',
            "data.edr.registration_address.parts.atu" => 'nullable|string',
            "data.edr.registration_address.parts.atu_code" => 'nullable|string',
            "data.edr.registration_address.parts.building" => 'nullable|string',
            "data.edr.registration_address.parts.building_type" => 'nullable|string',
            "data.edr.registration_address.parts.house" => 'nullable|string',
            "data.edr.registration_address.parts.house_type" => 'nullable|string',
            "data.edr.registration_address.parts.num" => 'nullable|string',
            "data.edr.registration_address.parts.num_type" => 'nullable|string',
            "data.edr.state" => 'required|int',
            "data.edr_verified" => 'nullable|boolean',
            'data.id' => 'required|string',
            'data.type' => 'required|string',
            'data.edrpou' => 'required|string',
            'data.status' => 'required|string',
            'data.phones' => 'required|array',
            'data.phones.*.type' => 'required|string',
            'data.phones.*.number' => 'required|string|size:13',
            'data.receiver_funds_code' => 'sometimes|string',
            'data.beneficiary' => 'sometimes|string',
            'data.website' => 'sometimes|string',
            'data.email' => 'required|string',
            'data.nhs_verified' => 'required|boolean',
            'data.nhs_reviewed' => 'required|boolean',
            'data.nhs_comment' => 'nullable|boolean',
            'data.residence_address' => 'required|array',
            'data.residence_address.type' => 'required|string',
            'data.residence_address.country' => 'required|string',
            'data.residence_address.area' => 'required|string',
            'data.residence_address.settlement' => 'required|string',
            'data.residence_address.settlement_type' => 'required|string',
            'data.residence_address.settlement_id' => 'required|string',
            'data.residence_address.region' => 'sometimes|string',
            'data.residence_address.street_type' => 'sometimes|string',
            'data.residence_address.street' => 'sometimes|string',
            'data.residence_address.building' => 'sometimes|string',
            'data.residence_address.apartment' => 'sometimes|string',
            'data.residence_address.zip' => 'sometimes|string',
            'data.accreditation' => 'sometimes|array',
            'data.accreditation.category' => 'required|string',
            'data.accreditation.issued_date' => 'sometimes|string',
            'data.accreditation.expiry_date' => 'sometimes|string',
            'data.accreditation.order_no' => 'required|string',
            'data.accreditation.order_date' => 'required_unless:data.accreditation.category,NO_ACCREDITATION|string',
            'data.license' => 'sometimes|array',
            'data.license.id' => 'sometimes|string',
            'data.license.type' => 'required|string',
            'data.license.license_number' => 'sometimes|string',
            'data.license.issued_by' => 'required|string',
            'data.license.issued_date' => 'required|string',
            'data.license.expiry_date' => 'sometimes|string',
            'data.license.active_from_date' => 'required|string',
            'data.license.what_licensed' => 'required|string',
            'data.license.order_no' => 'required|string',
            'data.archive' => 'sometimes|array',
            'data.archive.*.date' => 'required_with:data.archive|string',
            'data.archive.*.place' => 'required_with:data.archive|string',
            'data.inserted_by' => 'nullable|string',
            'data.inserted_at' => 'nullable|string',
            'data.updated_by' => 'nullable|string',
            'data.updated_at' => 'nullable|string',
            'data.is_active' => 'nullable|boolean',
            'urgent' => 'required|array',
            'urgent.employee_request_id' => 'required|string',
            'urgent.security.client_secret' => 'required|string',
            'urgent.security.client_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::error('Legal Entity Response Schema:', ['errors' => $validator->errors()]);

            throw new Exception(__('Помилка при обробці відповіді від сервера'));
        }

        return $validator->validated();
    }

    /**
     * Prepares a public offer array with consent text and consent status.
     *
     * @return array
     */
    private function preparePublicOffer(): array
    {
        // Define an array with consent text and consent status
        return [
            'consent_text' => 'Sample consent_text',
            'consent'      => true
        ];
    }

    /**
     * Prepares security data for authentication.
     *
     * @return array
     */
    private function prepareSecurityData(): array
    {
        return [
            'redirect_uri' => 'https://openhealths.com',
        ];
    }

    /**
     * Prepares the data for the request by converting documents to an array, tax_id to no_tax_id, and archive to an array.
     *
     * @param array $data The data to be prepared for the request
     * @return array The prepared data for the request
     */
    private function prepareDataForRequest(array $data): array
    {
        // TODO: check if need leave empty archive and accreditation if not checked when sending request to the ESOZ

        $data = $this->convertArrayKeysToSnakeCase($data);

        // Converting documents to array
        if (isset($data['owner']['documents'])) {
            $data['owner']['documents'] = [$data['owner']['documents']];
        }

        $data['residence_address'] = $this->convertArrayKeysToSnakeCase($this->address);

        // Converting accreditation to array
        $data['accreditation'] = $data['accreditation_show'] ? $data['accreditation'] : [];

        // Check if 'category' === 'NO_ACCREDITATION' and only required fields are filled, also update following fields: 'issued_date', 'expiry_date', 'order_date'
         if(isset($data['accreditation']['category']) && $data['accreditation']['category'] === 'NO_ACCREDITATION') {
            if (!isset($data['accreditation']['issued_date']) && !isset($data['accreditation']['expiry_date'])) {
                $data['accreditation']['issued_date'] = null;
                $data['accreditation']['expiry_date'] = null;
                $data['accreditation']['order_date'] = null;
            }
        }

        // Converting archive to array
        $data['archive'] = $data['archivation_show'] ? [$data['archive']] : [];

        unset($data['archivation_show']);
        unset($data['accreditation_show']);

        return removeEmptyKeys($data);
    }

    protected function prepareEmployeeData(string $legalEntityId, array $requestData): array
    {
        $arr = [
            'employee_request' => [
                'legal_entity_id' => $legalEntityId,
                'position' => $requestData['owner']['position'],
                'start_date' => Carbon::now()->format('Y-m-d'),
                'status' => 'NEW',
                'employee_type' => "OWNER",
                'party' => [
                    'first_name' => $requestData['owner']['first_name'],
                    'last_name' => $requestData['owner']['last_name'],
                    'second_name' => $requestData['owner']['second_name'] ?? '',
                    'birth_date' => $requestData['owner']['birth_date'],
                    'gender' => $requestData['owner']['gender'],
                    'tax_id' => $requestData['owner']['tax_id'],
                    'no_tax_id' => $requestData['owner']['no_tax_id'],
                    'email' => $requestData['owner']['email'],
                    'documents' => $requestData['owner']['documents'],
                    'phones' => $requestData['owner']['phones']
                ]
            ]
        ];

        return $arr;
    }

    /**
     * Dispatches an error message with optional errors array.
     *
     * @param string $message The error message to dispatch
     * @param array $errors Additional errors to include
     * @return void
     */
    private function dispatchErrorMessage(string $message, array $errors = []): void
    {
        Log::error($message, $errors);

        $this->dispatch('flashMessage', [
            'message' => $message,
            'type'    => 'error',
            'errors'  => $errors
        ]);
    }

    /**
     * Handle success response from API request.
     *
     * @param array $response The response from the API request
     * @return void
     */
    private function handleSuccessResponse(array $response, array $requestData = [])
    {
        if(!isset($requestData['accreditation'])) {
            unset($response['data']['accreditation']);
        }

        if(!isset($requestData['archive'])) {
            unset($response['data']['archive']);
        }

        try {
            DB::transaction(function() use($response, $requestData) {
                $this->createOrUpdateLegalEntity($response);

                if (empty($this->legalEntity->uuid)) {
                    throw new Exception(__('LegalEntity Create: LegalEntity не має UUID'), 1);
                }

                if (!\auth()->user()?->legalEntity?->getOwner()?->exists()) {
                    try {
                        $user = $this->createUser();
                    } catch (Exception $err) {
                        throw new Exception('Error: create User: ' . $err->getMessage(), 2);
                    }
                } else {
                    $user = auth()->user;
                }

                try {
                    $employeeData = $this->prepareEmployeeData($this->legalEntity->uuid, $requestData);
                } catch (Exception $err) {
                    throw new Exception('Error: prepareEmployeeData: ' . $err->getMessage(), 3);
                }

                try {
                    $employeeResponse = $this->getEmployeeResponse($employeeData['employee_request'], $this->legalEntity->uuid, $response['urgent']['employee_request_id']);
                } catch (Exception $err) {
                    throw new Exception('Error: getEmployeeResponse:  ' . $err->getMessage(), 4);
                }

                try {
                    $this->saveEmployeeResponse($employeeResponse, $this->legalEntity, $user?->id ?? null);
                } catch (Exception $err) {
                    throw new Exception('Error: saveEmployeeResponse: ' . $err->getMessage(), 5);
                }

                if (isset($response['data']['license'])) {
                    $this->createLicense($response['data']['license']);
                } else {
                    throw new Exception(__('Ліцензійні дані відсутні'), 6);
                }

                if (Cache::has($this->entityCacheKey)) {
                    Cache::forget($this->entityCacheKey);
                }

                if (Cache::has($this->ownerCacheKey)) {
                    Cache::forget($this->ownerCacheKey);
                }

                if (Cache::has($this->stepCacheKey)) {
                    Cache::forget($this->stepCacheKey);
                }

                $this->logout();
            });
        } catch (Exception $err) {
            Log::error(__('Сталася помилка під час обробки запиту'), ['error' => $err->getMessage()]);

            throw new Exception(__('Сталася помилка під час обробки запиту.' . ' Код помилки: ' . $err->getCode()));
        }
    }


    protected function logout()
    {
        $user = Auth::user();

        if ($user) {
            $user->tokens()->delete();
        }

        Auth::guard('web')->logout();
        Session::flush();

        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function getEmployeeResponse(array $employeeData, string $legalEntityUUID, string $employeeRequestId): array
    {
        $party = $employeeData['party'];

        return [
              "legal_entity_id" => $legalEntityUUID,
              "position" => $employeeData['position'],
              "start_date" => $employeeData['start_date'],
              "status" => $employeeData['status'],
              "employee_type" => $employeeData['employee_type'],
              "party" => [
                "first_name" => $party['first_name'],
                "last_name" => $party['last_name'],
                "second_name" => $party['second_name'],
                "birth_date" => $party['birth_date'],
                "gender" => $party['gender'],
                "no_tax_id" => $party['no_tax_id'],
                "tax_id" => $party['tax_id'] ?? null,
                "email" => $party['email'],
                "documents" => $party['documents'],
                "phones" => $party['phones']
              ],
              "id" => $employeeRequestId,
              "inserted_at" => Carbon::now()->format('Y-m-d'),
              "updated_at" => Carbon::now()->format('Y-m-d')
        ];
    }

    /**
     * Create a new legal entity based on the provided data
     *
     * @param array $data  data needed to create the legal entity
     *
     * @return void
     */
    public function createOrUpdateLegalEntity(array $data): LegalEntityModel|null
    {
        // Get the UUID from the data, if it exists
        $uuid = $data['data']['id'] ?? '';

        $addressData = [$data['data']['residence_address']];
        unset($data['data']['residence_address']);

        try {
            // Find or create a new LegalEntity object by UUID
            $this->legalEntity = LegalEntityModel::firstOrNew(['uuid' => $uuid]);

            // Fill the object with data
            if (isset($data['data']) && is_array($data['data'])) {
                $this->legalEntity->fill($data['data']);
            }

            // Set UUID from data or default to empty string
            $this->legalEntity->uuid = $data['data']['id'] ?? '';

            // Set client secret from data or default to empty string
            $this->legalEntity->client_secret = $data['urgent']['security']['client_secret'] ?? $data['urgent']['security']['secret_key'] ?? null;

            // Set client id from data or default to null
            $this->legalEntity->client_id = $data['urgent']['security']['client_id'] ?? null;

            // Save or update the object in the database
            $this->legalEntity->save();

            $this->addressRepository->addAddresses($this->legalEntity, $addressData);

            $this->phoneRepository->addPhones($this->legalEntity, $data['data']['phones']);
        } catch (Exception $err) {
            throw new Exception('LegalEntity Create Error: ' . $err->getMessage());
        }

        return $this->legalEntity;
    }
}
