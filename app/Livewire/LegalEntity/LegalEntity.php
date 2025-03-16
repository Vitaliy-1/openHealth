<?php

namespace App\Livewire\LegalEntity;

use App\Classes\Cipher\Api\CipherApi;
use App\Livewire\LegalEntity\Forms\LegalEntitiesForms;
use App\Livewire\LegalEntity\Forms\LegalEntitiesRequestApi;
use App\Mail\OwnerCredentialsMail;

use App\Models\LegalEntity as LegalEntityModel;
use App\Models\License;
use App\Models\User;
use App\Classes\Cipher\Traits\Cipher;
use App\Models\Employee\Employee;
use App\Models\Relations\Address;
use App\Traits\AddressSearch;
use App\Traits\FormTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Livewire\WithFileUploads;
use App\Repositories\AddressRepository;
use App\Repositories\EmployeeRepository;
use Livewire\Attributes\Computed;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 *
 */
class LegalEntity extends Component
{
    use FormTrait,
        Cipher,
        WithFileUploads,
        AddressSearch;

    protected EmployeeRepository $employeeRepository;

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

    /**
     * @var Employee
     */
    public Employee $employee; // TODO: try to find out where this is used

    /**
     * @var object|null
     */
    public ?object $file = null;

    /**
     * @var array|string[] Get dictionaries keys
     */
    public array $dictionaries_field = [
        'PHONE_TYPE',
        'LICENSE_TYPE',
        'SETTLEMENT_TYPE',
        'GENDER',
        'SPECIALITY_LEVEL',
        'ACCREDITATION_CATEGORY'
    ];

    /**
     * @return void set cache keys
     */
    public function boot(AddressRepository $addressRepository): void
    {
        $this->addressRepository = $addressRepository;
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
            $this->dictionaries = array_merge($this->dictionaries, dictionary()->getDictionariesFields($keys, $type));
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
        } else {
            $this->legalEntityForm->customRulesValidation();
        }

        // Prepare data for public offer
        $this->legalEntityForm->publicOffer = $this->preparePublicOffer();

        // Prepare security data
        $this->legalEntityForm->security = $this->prepareSecurityData();

        // Convert form data to an array
        $data = $this->prepareDataForRequest($this->legalEntityForm->toArray());

        $taxId = $this->legalEntityForm->owner['taxId'];
        dd($data);
        // Sending encrypted data
        $base64Data = $this->sendEncryptedData($data, $taxId, CipherApi::SIGNATORY_INITIATOR_BUSINESS);

        // Handle errors from encrypted data
        if (isset($base64Data['errors'])) {
            $this->dispatchErrorMessage($base64Data['errors']);
            return;
        }

        // Prepare data for API request
        $request = LegalEntitiesRequestApi::_createOrUpdate([
            'signed_legal_entity_request' => $base64Data,
            'signed_content_encoding'     => 'base64',
        ]);

        // Handle errors from API request
        if (isset($request['errors']) && is_array($request['errors'])) {
            $this->dispatchErrorMessage(__('Запис не було збережено'), $request['errors']);
            return;
        }

        // Handle successful API request
        if (!empty($request['data'])) {
            $this->handleSuccessResponse($request);
        }

        // Dispatch error message for unknown errors
        $this->dispatchErrorMessage(__('Не вдалося отримати відповідь'));
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

        // Converting tax_id to no_tax_id
        $data['owner']['no_tax_id'] = empty($data['owner']['tax_id']);

        // Converting accreditation to array
        $data['accreditation'] = !empty($data['accreditation_show'])
            ? [$data['accreditation'] ?? []]
            : [];

        // Converting archive to array
        $data['archive'] = !empty($data['archivation_show'])
            ? [$data['archive'] ?? []]
            : [];

        unset($data['archivation_show']);
        unset($data['accreditation_show']);

        return removeEmptyKeys($data);
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
        $this->dispatch('flashMessage', [
            'message' => $message,
            'type'    => 'error',
            'errors'  => $errors
        ]);
    }

    /**
     * Handle success response from API request.
     *
     * @param array $request The response from the API request
     * @return void
     */
    private function handleSuccessResponse(array $request): void
    {
        try {
            $this->createOrUpdateLegalEntity($request);

            if (!\auth()->user()?->legalEntity?->getOwner()?->exists()) {
                $this->createUser();
            }

            if (isset($request['data']['license'])) {
                $this->createLicense($request['data']['license']);
            } else {
                $this->dispatchErrorMessage(__('Ліцензійні дані відсутні'));
                return;
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

            if (session()->has('savedAddress')) {
                session()->forget('savedAddress');
            }

            if (session()->has('savedLegalEntityForm')) {
                session()->forget('savedLegalEntityForm');
            }

            $this->redirect('/legal-entities/edit');
        } catch (Exception $e) {
            $this->dispatchErrorMessage(__('Сталася помилка під час обробки запиту'), ['error' => $e->getMessage()]);
            return;
        }
    }

    /**
     * Create a new legal entity based on the provided data
     *
     * @param array $data  data needed to create the legal entity
     *
     * @return void
     */
    public function createOrUpdateLegalEntity(array $data): void
    {
        // Get the UUID from the data, if it exists
        $uuid = $data['data']['id'] ?? '';

        $addressData = [$data['data']['residence_address']];
        unset($data['data']['residence_address']);

        if (empty($uuid)) {
            $this->dispatchErrorMessage(__('Не вдалось створити Юридичну особу'), ['errors' => 'No UUID found in data']);
            return;
        }

        // Find or create a new LegalEntity object by UUID
        $this->legalEntity = LegalEntityModel::firstOrNew(['uuid' => $uuid]);

        // Fill the object with data
        if (isset($data['data']) && is_array($data['data'])) {
            $this->legalEntity->fill($data['data']);
        }

        // Set UUID from data or default to empty string
        $this->legalEntity->uuid = $data['data']['id'] ?? '';

        // Set client secret from data or default to empty string
        $this->legalEntity->client_secret = $data['urgent']['security']['client_secret'] ?? '';

        // Set client id from data or default to null
        $this->legalEntity->client_id = $data['urgent']['security']['client_id'] ?? null;

        // Save or update the object in the database
        $this->legalEntity->save();

        $this->addressRepository->addAddresses($this->legalEntity, $addressData);
    }
}
