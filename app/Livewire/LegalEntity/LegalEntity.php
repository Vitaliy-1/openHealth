<?php

declare(strict_types=1);

namespace App\Livewire\LegalEntity;

use App\Enums\User\Role;
use Log;
use Arr;
use Exception;
use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\License;
use Livewire\Component;
use App\Enums\JobStatus;
use App\Traits\FormTrait;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\LegalEntityType;
use App\Repositories\Repository;
use App\Models\Employee\Employee;
use App\Events\LegalEntityCreate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Classes\Cipher\Traits\Cipher;
use Illuminate\Support\Facades\Cache;
use App\Repositories\PhoneRepository;
use App\Enums\Employee\RequestStatus;
use App\Traits\Addresses\AddressSearch;
use App\Repositories\AddressRepository;
use App\Models\Employee\EmployeeRequest;
use App\Repositories\EmployeeRepository;
use App\Enums\License\Type as LicenseType;
use Illuminate\Validation\ValidationException;
use App\Models\LegalEntity as LegalEntityModel;
use App\Livewire\LegalEntity\Forms\LegalEntitiesForms;
use App\Livewire\Employee\AbstractEmployeeFormManager;
use App\Livewire\LegalEntity\Forms\LegalEntitiesRequestApi;

abstract class LegalEntity extends Component
{
    use FormTrait;
    use Cipher;
    use WithFileUploads;
    use AddressSearch;

    protected const string STEP_PATH = 'views/livewire/legal-entity/step';

    protected const array LICENSE_TYPE_MAP = [
        LegalEntityModel::TYPE_PRIMARY_CARE => LicenseType::MSP->value,
        LegalEntityModel::TYPE_OUTPATIENT => LicenseType::MSP->value,
        LegalEntityModel::TYPE_EMERGENCY => LicenseType::MSP->value,
        LegalEntityModel::TYPE_PHARMACY => LicenseType::PHARMACY->value,
    ];

    /**
     * @var string
     */
    protected const CACHE_PREFIX = 'register_legal_entity_form';

    /**
     * @var string The Cache ID to store Legal Entity being filled by the current user
     */
    protected string $entityCacheKey;

    /**
     * @var string The Cache ID to store Owner being filled by the current user
     */
    protected string $ownerCacheKey;

    /**
     * @var string The Cache ID to store Owner being filled by the current user
     */
    protected string $stepCacheKey;

    /**
     * @var LegalEntitiesForms The Form
     */
    public LegalEntitiesForms $legalEntityForm;

    /**
     * @var LegalEntityModel|null The Legal Entity being filled
     */
    protected ?LegalEntityModel $legalEntity;

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

    public array $legalEntityTypes = [];

    protected array $allowedLegalEntityTypes = [
        LegalEntityModel::TYPE_PRIMARY_CARE,
        LegalEntityModel::TYPE_OUTPATIENT,
        LegalEntityModel::TYPE_EMERGENCY,
        LegalEntityModel::TYPE_PHARMACY,
        LegalEntityModel::TYPE_MSP_LIMITED
    ];

    /**
     * @return void set cache keys
     */
    public function boot(
        AddressRepository $addressRepository,
        PhoneRepository $phoneRepository
    ): void {
        $this->addressRepository = $addressRepository;
        $this->phoneRepository = $phoneRepository;

        $this->entityCacheKey = self::CACHE_PREFIX . '-' . Auth::id() . '-' . LegalEntityModel::class;
        $this->ownerCacheKey = self::CACHE_PREFIX . '-' . Auth::id() . '-' . Employee::class;
        $this->stepCacheKey = self::CACHE_PREFIX . '-' . Auth::id() . '-' . 'steps';
    }

    protected function mount(): void
    {
        $this->mergeAddress($this->convertArrayKeysToCamelCase($this->legalEntity->toArray())['address'] ?? []);

        $this->getDictionary();

        $this->setLegalEntityTypes();

        $this->setCertificateAuthority();

        $this->getOwnerFields();
    }

    /**
     * @return void
     */
    protected function getOwnerFields(): void
    {
        // Get owner dictionary fields
        $fields = [
            'POSITION' => config('ehealth.employee_type.OWNER.position'),
            'DOCUMENT_TYPE' => ['PASSPORT', 'NATIONAL_ID']
        ];

        // Get dictionaries
        foreach ($fields as $type => $keys) {
            $this->dictionaries[$type] = $this->getDictionariesFields($keys, $type);
        }
    }

    protected function setLegalEntityTypes(): void
    {
        $this->legalEntityTypes = LegalEntityType::whereIn('name', $this->allowedLegalEntityTypes)
            ->pluck('localized_name', 'name')
            ->map(fn ($label) => __($label))
            ->reverse()
            ->toArray();
    }

    abstract protected function getLegalEntity(): ?LegalEntityModel;

    protected function setLegalEntity(): bool
    {
        // Set $this->legalEntity property
        $this->legalEntity = $this->getLegalEntity();

        // If a LegalEntity is found, fill the form with its data
        if ($this->legalEntity) {
            $modelData = $this->convertArrayKeysToCamelCase($this->legalEntity->toArray());
            $modelData['license'] = [];

            $modelData['type'] = $this->legalEntity->type?->name ?: '';

            if (!empty($modelData['licenses'])) {
                $modelData['license'] = $modelData['licenses'] ?? [];
                unset($modelData['licenses']);
            }

            $modelData['website'] ??= '';
            $modelData['accreditation'] = ($modelData['accreditation'] ?? []) + $this->legalEntityForm->accreditation;

            $this->legalEntityForm->fill($modelData);

            return true;
        }
        $this->legalEntity = new LegalEntityModel();

        return false;

    }

    protected function mergeAddress(array $address): void
    {
        if (empty($address)) {
            return;
        }

        foreach ($address as $key => $value) {
            $this->address[$key] = $value;
        }

        if (isset($this->address['area'])) {
            $this->address['area'] = mb_strtoupper($this->address['area']);
        }
    }

    protected function getLegalEntityFromCache(): ?LegalEntityModel
    {
        return Cache::get($this->entityCacheKey) ?? null;
    }

    /**
     * Get list of the Authority Centers of the Key's Certification
     *
     * @return array|null
     */
    private function setCertificateAuthority(): array|null
    {
        return $this->getCertificateAuthority = $this->getCertificateAuthority();
    }

    /**
     * Livewire lifecycle hook triggered when the beneficiary field is updated.
     *
     * @param  mixed  $value  The new value of the beneficiary field
     *
     * @return void
     */
    public function updatedLegalEntityFormBeneficiary($value)
    {
        $this->legalEntityForm->onBeneficiaryUpdated($value);
    }

    /**
     * Livewire lifecycle hook triggered when the receiverFundsCode field is updated.
     *
     * @param  mixed  $value  The new value of the receiverFundsCode field
     *
     * @return void
     */
    public function updatedLegalEntityFormReceiverFundsCode($value)
    {
        $this->legalEntityForm->onReceiverFundsCodeUpdated($value);
    }

    /**
     * Step 8 for handling sign legal entity  submission.
     *
     * @throws ValidationException
     */
    protected function signLegalEntity(): array|null
    {
        // TODO: remove this after MVP (if not needed)
        if (!$this->legalEntityForm->customRulesValidation()) {
            return null;
        }

        // Prepare data for public offer
        $this->legalEntityForm->publicOffer = $this->preparePublicOffer();

        // Prepare security data
        $this->legalEntityForm->security = $this->prepareSecurityData();

        // Convert form data to an array
        $data = $this->prepareDataForRequest($this->legalEntityForm->toArray());

        $taxId = $this->legalEntityForm->owner['taxId'];

        Log::info('Legal Entity Success SOURCE DATA', $data);

        // Sending encrypted data
        $base64Data = $this->sendEncryptedData($data, $taxId, $data['edrpou']);

        // Handle errors from encrypted data
        if (isset($base64Data['errors'])) {
            $this->dispatchErrorMessage($base64Data['errors']);

            return null;
        }

        // Prepare data for API request
        $response = LegalEntitiesRequestApi::_createOrUpdate([
            'signed_legal_entity_request' => $base64Data,
            'signed_content_encoding' => 'base64',
        ]);

        // Handle errors from API request
        if (isset($response['errors']) && is_array($response['errors'])) {
            $this->dispatchErrorMessage(__('Запис не було збережено'), $response['errors']);

            return null;
        }

        if ($this->legalEntityForm->owner['employee_id'] ?? null) {
            $data['employee_id'] = $this->legalEntityForm->owner['employee_id'];
        }

        Log::info('Legal Entity Success RESPONSE', $response); // TODO: Important! Delete after testing!!!

        try {
            $response = $this->validateResponse($response);
        } catch (Exception $err) {
            $this->dispatchErrorMessage($err->getMessage());

            return null;
        }

        if (empty($response) || !is_array($response)) {
            $this->dispatchErrorMessage(__('auth.login.error.server.response'));

            return null;
        }

        return ['response' => $response, 'request' => $data];
    }

    /**
     * Check $response schema for errors
     */
    protected function validateResponse(mixed $data): array
    {
        $replaced = $this->replaceEHealthPropNames($data);

        $validator = Validator::make($replaced, [
            'data' => 'required|array',
            'data.edr' => 'required|array',
            "data.edr.edrpou" => "required|string",
            "data.edr.uuid" => "required|string",
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
            'data.uuid' => 'required|string',
            'data.type' => 'required|string',
            'data.edrpou' => 'required|string',
            'data.status' => 'required|string',
            'data.phones' => 'required|array',
            'data.phones.*.type' => 'required|string',
            'data.phones.*.number' => 'required|string|size:13',
            'data.phones.*.note' => 'sometimes|string',
            'data.receiver_funds_code' => 'nullable|string',
            'data.beneficiary' => 'nullable|string',
            'data.website' => 'nullable|string',
            'data.email' => 'required|string',
            'data.nhs_verified' => 'required|boolean',
            'data.nhs_reviewed' => 'required|boolean',
            'data.nhs_comment' => 'nullable|string',
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
            'data.accreditation' => 'nullable|array',
            'data.accreditation.category' => 'required_if:data.accreditation,array|string',
            'data.accreditation.issued_date' => 'sometimes|string',
            'data.accreditation.expiry_date' => 'sometimes|string',
            'data.accreditation.order_no' => 'required_with:data.accreditation.category|string',
            'data.license' => 'nullable|array',
            'data.license.uuid' => 'required_if:data.license,array|string',
            'data.license.type' => 'required_if:data.license,array|string',
            'data.license.license_number' => 'sometimes|string',
            'data.license.issued_by' => 'sometimes|string',
            'data.license.issued_date' => 'sometimes|string',
            'data.license.expiry_date' => 'nullable|string',
            'data.license.is_active' => 'nullable|boolean',
            'data.license.ehealth_inserted_at' => 'required_if:data.license,array|string',
            'data.license.ehealth_inserted_by' => 'required_if:data.license,array|string',
            'data.license.active_from_date' => 'sometimes|string',
            'data.license.what_licensed' => 'sometimes|string',
            'data.license.order_no' => 'sometimes|string',
            'data.license.ehealth_updated_at' => 'required_if:data.license,array|string',
            'data.license.ehealth_updated_by' => 'required_if:data.license,array|string',
            'data.archive' => 'nullable|array',
            'data.archive.*.date' => 'required_if:data.archive,array|string',
            'data.archive.*.place' => 'required_if:data.archive,array|string',
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

        // If "category" has value "NO_ACCREDITATION" then data.accreditation.order_date should be null
        $validator->sometimes(
            'data.accreditation.order_date',
            'required_unless:data.accreditation.category,NO_ACCREDITATION|string',
            fn ($input) => isset(
                $input->data['accreditation']) &&
                    is_array($input->data['accreditation']) &&
                    array_key_exists(
                        'category',
                        $input->data['accreditation']
                    )
        );

        if ($validator->fails()) {
            Log::error('Legal Entity Response Schema:', ['errors' => $validator->errors()]);

            throw new Exception(__('Помилка при обробці відповіді від сервера'));
        }

        return $validator->validated();
    }

    /**
     * Replace eHealth property names with the ones used in the application.
     * E.g., id => uuid.
     */
    protected function replaceEHealthPropNames(array $properties): array
    {
        $replaced = [];

        foreach ($properties as $name => $value) {
            $newName = match ($name) {
                'id' => 'uuid',
                'inserted_at' => 'ehealth_inserted_at',
                'inserted_by' => 'ehealth_inserted_by',
                'updated_at' => 'ehealth_updated_at',
                'updated_by' => 'ehealth_updated_by',
                default => $name
            };

            $replaced[$newName] = $value;

            if (is_array($value)) {
                $replaced[$newName] = self::replaceEHealthPropNames($value);
            }
        }

        return $replaced;
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
            'consent_text' => __(dictionary()->getDictionary('LE_CONSENT_TEXT')['APPROVED']),
            'consent' => $this->legalEntityForm->publicOffer['consent'] ?? false,
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
     * Prepares available license types for use in a select input.
     *
     * Optionally filters or adjusts the prepared list based on the provided type,
     * and returns the result in string form suitable for UI rendering/binding.
     *
     * @param string $type License type discriminator used to tailor the select options.
     *
     * @return string Prepared license type options representation for the select field.
     */
    public function getLicenseTypesByLegalEntityType(?string $type = null): string
    {
        if (empty($type) || !\array_key_exists($type, self::LICENSE_TYPE_MAP)) {
            return '';
        }

        return self::LICENSE_TYPE_MAP[$type];
    }

    /**
     * Prepares the data for the request by converting documents to an array, tax_id to no_tax_id, and archive to an array.
     *
     * @param  array  $data  The data to be prepared for the request
     * @return array The prepared data for the request
     */
    private function prepareDataForRequest(array $data): array
    {
        $dateReformatArray = [
            'owner.birth_date',
            'owner.documents.issued_at',
            'accreditation.order_date',
            'accreditation.issued_date',
            'accreditation.expiry_date',
            'archive',
        ];

        if (Arr::has($data, 'license.uuid')) {
            $uuid = Arr::pull($data, 'license.uuid');

            if (!$this->isLicenseChanged($data['license'], $uuid)) {
                $data['license'] = ['id' => $uuid];
            }
        }

        // If license has been added/edited, reformat its dates
        if (empty($data['license']['id'])) {
            $dateReformatArray = array_merge($dateReformatArray, [
                'license.issued_date',
                'license.expiry_date',
                'license.active_from_date'
            ]);
        }

        $data = $this->convertArrayKeysToSnakeCase($data);

        $data = $this->dateReformat($data, $dateReformatArray);

        // Converting documents to array
        if (Arr::has($data, 'owner.employee_uuid')) {
            Arr::set($data, 'owner.employee_id', Arr::get($data, 'owner.employee_uuid'));
        }

        // If no_tax_id=true its means that taxID should store related document's number
        if (Arr::boolean($data, 'owner.no_tax_id')) {
            Arr::set($data, 'owner.tax_id', Arr::get($data, 'owner.documents.number'));
        }

        // Converting documents to array
        if (Arr::has($data, 'owner.documents')) {
            Arr::set($data, 'owner.documents', [Arr::get($data, 'owner.documents')]);
        }

        Arr::forget($data, [
            'owner.user_id',
            'owner.id',
            'owner.employee_uuid',
            'owner.uuid',
            'owner.about_myself',
            'owner.working_experience',
            'owner.verification_status'
        ]);

        $data['residence_address'] = $this->convertArrayKeysToSnakeCase($this->address);

        // Converting accreditation to array
        $data['accreditation'] = $data['accreditation_show'] ? $data['accreditation'] : [];

        // Check if 'category' === 'NO_ACCREDITATION' and only required fields are filled, also update following fields: 'issued_date', 'expiry_date', 'order_date'
        if (Arr::get($data, 'accreditation.category') === 'NO_ACCREDITATION') {
            Arr::set($data, 'accreditation.issued_date', null);
            Arr::set($data, 'accreditation.expiry_date', null);
            Arr::set($data, 'accreditation.order_date', null);
        }

        // Converting archive to array
        $data['archive'] = $data['archivation_show'] ? $data['archive'] : [];

        Arr::forget($data, [
            'archivation_show',
            'accreditation_show',
            'beneficiary_show',
            'receiver_funds_code_show'
            ]
        );

        $data = removeEmptyKeys($data);

        $data['website'] ??= "";

        return $data;
    }

    /**
     * Reformats date values in the provided data array based on the specified data items.
     *
     *
     * @param  array  $data  The input data array containing date values to be reformatted
     * @param  array  $dataItems  Configuration array specifying which items are dates and how to format them
     * @return array
     */
    protected function dateReformat(array $data, array $dataItems): array
    {
        foreach ($dataItems as $item) {
            $itemValue = Arr::get($data, $item);

            if (empty($itemValue)) {
                continue;
            }

            if (\is_array($itemValue)) {
                $reformatted = collect($itemValue)->map(function ($item) {
                    if (isset($item['date'])) {
                        $item['date'] = convertToYmd($item['date']);
                    }

                    return $item;
                })->toArray();

                Arr::set($data, $item, $reformatted);
            } else {
                Arr::set($data, $item, convertToYmd($itemValue));
            }
        }

        return $data;
    }

    /**
     * Check if the license data has been changed for a given legal entity.
     *
     * @param  array  $data  The new license data to compare
     * @param  string  $uuid  The unique identifier of the license model
     * @return bool Returns true if the license has been changed, false otherwise
     */
    protected function isLicenseChanged(array $data, string $uuid): bool
    {
        $license = License::where('uuid', $uuid)->first()->toArray();

        if (empty($license)) {
            return true;
        }

        foreach ($data as $key => $value) {
            if ($value !== $license[$key]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Prepare all data needs for creating EmployeeRequest throught LegalEntity creation
     *
     * @param  string  $legalEntityId
     * @param  array  $requestData
     * @return array
     */
    private function mapEmployeRequestData(array $requestData): array
    {
        // Check if the current user is an already logined and all changes is for edit legal entity, if so - set $isEdit to true, otherwise - it's create legal entity
        // $isEdit = Auth::getDefaultDriver() === 'ohealths';

        $employeeId = Arr::pull($requestData, 'owner.employee_id', null);

        $arr = [
            "position" => $requestData['owner']['position'],
            "employee_type" => "OWNER",
            "start_date" => Carbon::now()->format('Y-m-d'),
            "end_date" => null,
            "division_id" => null,
            "documents" => $requestData['owner']['documents'],
            "last_name" => $requestData['owner']['last_name'],
            "first_name" => $requestData['owner']['first_name'],
            "second_name" => $requestData['owner']['second_name'] ?? '',
            "gender" => $requestData['owner']['gender'],
            "birth_date" => $requestData['owner']['birth_date'],
            "phones" => $requestData['owner']['phones'],
            "tax_id" => $requestData['owner']['tax_id'],
            "no_tax_id" => $requestData['owner']['no_tax_id'],
            "email" => $requestData['owner']['email'],
            "working_experience" => Arr::pull($requestData['owner'], 'working_experience', null),
            "about_myself" => Arr::pull($requestData['owner'], 'about_myself', null),
            "party_id" => Arr::pull($requestData['owner'], 'party_id', null),
            "doctor" => [
                "specialities" => [],
                "science_degree" => [],
                "qualifications" => [],
                "educations" => []
            ]
        ];

        if ($employeeId) {
            unset($requestData['owner']['employee_id']);
            $arr['employee_id'] = $requestData['employee_id'];
        }

        return $arr;
    }

    /**
     * Dispatches an error message with optional errors array.
     *
     * @param  string  $message  The error message to dispatch
     * @param  array  $errors  Additional errors to include
     * @return void
     */
    protected function dispatchErrorMessage(string $message, array $errors = []): void
    {
        Log::error($message, $errors);

        $this->dispatch('flashMessage', [
            'message' => $message,
            'type' => 'error',
            'errors' => $errors
        ]);
    }

    protected function filterUnprovidedFields(array $response, array $requestData): array
    {
        /**
         * This need to check because it's not always present.
         * Only way to determine if it's present is to check if it's not empty.
         * This mainly concerns the editing of the legal entity.
         */
        if (!isset($requestData['accreditation']) || !$requestData['accreditation'] ['category']) {
            unset($response['data']['accreditation']);
        }

        /**
         * This need to check because it's not always present.
         * Only way to determine if it's present is to check if it's not empty.
         * This mainly concerns the editing of the legal entity.
         */
        if (!isset($requestData['archive']) || empty($requestData['archive'])) {
            unset($response['data']['archive']);
        }

        return $response;
    }

    /**
     * Prepare all data need for create EmployeeRequest (for case of creating Legal Entity only!)
     * And store the EmployeeRequest & Revision record
     *
     * @param  LegalEntityModel  $legalEntity
     * @param  array  $requestData
     * @param  string  $employeeRequestId
     * @throws Exception
     * @return void
     */
    protected function createEmployeeRequest(LegalEntityModel $legalEntity, array $requestData, string $employeeRequestId): void
    {
        // Check if the current user is an already logined and all changes is for edit legal entity, if so - set $isEdit to true, otherwise - it's create legal entity
        $isEdit = Auth::getDefaultDriver() === 'ehealth';

        $preparedData = $this->mapEmployeRequestData($requestData);

        // Here get the anonymous instanse of the AbstractEmployeeFormManager class
        $employeeRequestHelper = $this->getAbstractEmployeeFormManagerHelper($preparedData);

        // Get the draft EmployeeRequest record (create a new one)
        $employeeRequest = $employeeRequestHelper->handleDraftPersistence();

        // COMPLETED status set only when legalentity is created, for edit legal entity - status will be always PENDING, because we need to wait for approvement of the changes
        $employeeRequest->syncStatus = $isEdit ? JobStatus::PARTIAL->value : JobStatus::COMPLETED->value;

        // This method just create a draft record in the local DB and set the $this->employeeRequestId property)
        $employeeRequestEmulatedData = $this->mapEmployeeRequestResponse($preparedData, $legalEntity->uuid, $employeeRequestId);

        // Save the EmployeeRequest emulated response data to the local DB (create the revision record at the same time)
        $employeeRequestHelper->applyUpdateLocalRecords($employeeRequest, $employeeRequestEmulatedData, $legalEntity);
    }

    /**
     * Emulate the EmployeeRequest response from the server (as if the really one will received)
     *
     * @param  array  $employeeData
     * @param  string  $legalEntityUUID
     * @param  string  $employeeRequestId
     * @return array
     */
    protected function mapEmployeeRequestResponse(array $employeeData, string $legalEntityUUID, string $employeeRequestId): array
    {
        return [
           "id" => $employeeRequestId,
            "ehealth_response" => [
                "data" => [
                    "employee_type" => $employeeData['employee_type'],
                    "id" => $employeeRequestId,
                    "inserted_at" => Carbon::now()->format('Y-m-d'),
                    "legal_entity_id" => $legalEntityUUID,
                    "party" => [
                        "birth_date" => $employeeData['birth_date'],
                        "documents" => $employeeData['documents'],
                        "email" => $employeeData['email'],
                        "first_name" => $employeeData['first_name'],
                        "gender" => $employeeData['gender'],
                        "last_name" => $employeeData['last_name'],
                        "no_tax_id" => $employeeData['no_tax_id'],
                        "phones" => $employeeData['phones'],
                        "second_name" => $employeeData['second_name'],
                        "tax_id" => $employeeData['tax_id'],
                        "working_experience" => $employeeData['working_experience'],
                        "about_myself" => $employeeData['about_myself']
                    ],
                    "position" => $employeeData['position'],
                    "start_date" => $employeeData['start_date'],
                    "status" => RequestStatus::NEW->value,
                    "updated_at" => Carbon::now()->format('Y-m-d')
                ],
                "meta" => [
                    "code" => 200,
                    "type" => "object",
                    "url" => "http://api-svc.il/api/v2/employee_requests",
                    "request_id" => "2260fac7-249f-4b16-85b3-69f685f812d9#530957"
                ]
            ]
        ];
    }

    /**
     * Create a new legal entity based on the provided data
     *
     * @param  array  $data  data needed to create the legal entity
     * @return void
     */
    protected function persistLegalEntity(array $data): array
    {
        // Get the legalEntity's type from the data
        $type = Arr::pull($data['data'], 'type', '');

        // Get the UUID from the data, if it exists
        $uuid = Arr::pull($data['data'], 'uuid', '');

        // This need because the LegalEntity has a separate table for the address
        $addressData = [Arr::pull($data['data'], 'residence_address', [])];

        // This need because the LegalEntity has a separate table for the phones
        $phones = Arr::pull($data['data'], 'phones', []);

        // Do unset this because it already set if create or present and deny to modify if edit
        unset($data['data']['license']);

        // Normalize date fields (need for MySQL date format)
        if (isset($data['data']['inserted_at'])) {
            $data['data']['inserted_at'] = convertToYmd($data['data']['inserted_at']);
        }

        if (isset($data['data']['updated_at'])) {
            $data['data']['updated_at'] = convertToYmd($data['data']['updated_at']);
        }

        try {
            // Find or create a new LegalEntity object by UUID
            $this->legalEntity = LegalEntityModel::firstOrNew(['uuid' => $uuid]);

            if (empty($data['data']['accreditation'])) {
                $this->legalEntity->accreditation = [];
            }

            if (empty($data['data']['archive'])) {
                $this->legalEntity->archive = null;
            }

            if (empty($data['data']['beneficiary']) || !$this->legalEntityForm->beneficiaryShow) {
                $this->legalEntity->beneficiary = null;
                unset($data['data']['beneficiary']);
            }

            if (empty($data['data']['receiver_funds_code']) || !$this->legalEntityForm->receiverFundsCodeShow) {
                $this->legalEntity->receiverFundsCode = null;
                unset($data['data']['receiver_funds_code']);
            }

            // Fill the object with data
            $this->legalEntity->fill($data['data']);

            $type = LegalEntityType::firstWhere('name', $type);
            $this->legalEntity->type()->associate($type);

            $clientSecret = $data['urgent']['security']['client_secret'] ?? $data['urgent']['security']['secret_key'] ?? null;
            $clientId = $data['urgent']['security']['client_id'] ?? null;

            // Set client secret from data or default to empty string
            if ($clientSecret) {
                $this->legalEntity->client_secret = $clientSecret;
            }

            // Set client id from data or default to null
            if ($clientId) {
                $this->legalEntity->client_id = $clientId;
            }

            // Save or update the object in the database
            $this->legalEntity->save();

        } catch (Exception $err) {
            throw new Exception('LegalEntity Store Error: ' . $err->getMessage());
        }

        return ['addressData' => $addressData, 'phones' => $phones];
    }

    protected function createNewLegalEntity(array $data): LegalEntityModel|null
    {
        $legalEntityData = $this->persistLegalEntity($data);

        try {
            $this->addressRepository->addAddresses($this->legalEntity, $legalEntityData['addressData']);

            $this->phoneRepository->addPhones($this->legalEntity, $legalEntityData['phones']);

            $this->legalEntity->refresh();
        } catch (Exception $err) {
            throw new Exception('LegalEntity Create Error: ' . $err->getMessage());
        }

        return $this->legalEntity;
    }

    protected function modifyLegalEntity(array $data): LegalEntityModel|null
    {
        $legalEntityData = $this->persistLegalEntity($data);

        try {
            $this->addressRepository->syncAddresses($this->legalEntity, $legalEntityData['addressData']);

            $this->phoneRepository->syncPhones($this->legalEntity, $legalEntityData['phones']);

            $this->legalEntity->refresh();

            $this->saveLicense($data['data']['license']);
        } catch (Exception $err) {
            throw new Exception('LegalEntity Create Error: ' . $err->getMessage());
        }

        return $this->legalEntity;
    }

    /**
     * Create a new license with the provided data.
     *
     * @param  array  $data  The data to fill the license with.
     */
    protected function saveLicense(array $data): void
    {
        $data['ehealth_inserted_at'] = convertToYmd($data['ehealth_inserted_at']);
        $data['ehealth_updated_at'] = convertToYmd($data['ehealth_updated_at']);

        $license = License::firstOrNew(['uuid' => $data['uuid']]);
        $license->fill($data);
        $license->is_primary = $data['type'] === LicenseType::MSP->value || $data['type'] === LicenseType::PHARMACY->value;

        if (isset($this->legalEntity)) {
            $this->legalEntity->licenses()->save($license);
        }
    }

    /**
     * Create a new user for the legal entity.
     *
     * @return User|null
     */
    protected function createUser(): ?User
    {
        // Get the currently authenticated user
        $authenticatedUser = Auth::user();

        // Retrieve the email address of the legal entity owner from the form or set it to null
        $ownerEmail = $this->legalEntityForm->owner['email'] ?? null;

        // Generate a random password
        $password = Str::random(10);

        // Check if a user with the provided email already exists
        $owner = User::where('email', $ownerEmail)->first() ?? User::create([
                'email' => $ownerEmail,
                'password' => Hash::make($password),
            ]);

        try {
            $owner->save();

            $owner->refresh();
        } catch (Exception $e) {
            $this->dispatchErrorMessage(__('Сталася помилка під час обробки запиту'), ['error' => $e->getMessage()]);

            return null;
        }

        Auth::shouldUse('web');

        // Assign the 'OWNER' role to the user authenticated via web guard
        $owner->assignRole(Role::OWNER);

        Auth::shouldUse('ehealth');

        // Assign the 'OWNER' role to the user authenticated via ehealth guard
        $owner->assignRole(Role::OWNER);

        // Send credentials and email verification link
        event(new LegalEntityCreate($authenticatedUser, $owner, $password));

        return $owner;
    }

    /**
     * Returns an anonymous helper (extends AbstractEmployeeFormManager) that
     * creates an EmployeeRequest draft and its Revision for the current Legal Entity
     *
     * @param  array  $preparedData
     * @return AbstractEmployeeFormManager Anonymous helper instance.
     */
    protected function getAbstractEmployeeFormManagerHelper(array $preparedData): AbstractEmployeeFormManager
    {
        return new class ($preparedData, $this->legalEntity) extends AbstractEmployeeFormManager
        {
            public function __construct(private array $preparedData, private LegalEntityModel $legalEntity)
            {
            }

            public function handleDraftPersistence(): EmployeeRequest
            {
                $nestedDataForRevision = $this->mapRevisionData($this->preparedData);

                // Prepare the data for the request model itself
                $employeeRequestData = Arr::only($this->preparedData, [
                    'position', 'start_date', 'end_date', 'employee_type', 'division_id', 'email', 'party_id'
                ]);

                if (!empty($this->preparedData['employee_id'])) {
                    $employeeRequestData['employee_id'] = $this->preparedData['employee_id'];
                }

                // If no draft exists, create a new one.
                $newRequest = Repository::employee()->createEmployeeRequestDraft($employeeRequestData, $this->legalEntity);

                $this->saveRevisionForRequest($newRequest, $nestedDataForRevision);

                return $newRequest;
            }

            // Public proxy to call protected updateLocalRecords from outside the component context
            public function applyUpdateLocalRecords(EmployeeRequest $request, array $eHealthResponse, ?LegalEntityModel $legalEntity = null): void
            {
                $this->updateLocalRecords($request, $eHealthResponse, $legalEntity);
            }
        };
    }
}
