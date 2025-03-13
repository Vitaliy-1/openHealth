<?php

namespace App\Livewire\LegalEntity;

use App\Classes\Cipher\Api\CipherApi;
use App\Livewire\LegalEntity\Forms\LegalEntitiesForms;
use App\Livewire\LegalEntity\Forms\LegalEntitiesRequestApi;
use App\Mail\OwnerCredentialsMail;
use App\Models\Employee;
use App\Models\LegalEntity;
use App\Models\License;
use App\Models\User;
use App\Classes\Cipher\Traits\Cipher;
use App\Traits\FormTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Livewire\WithFileUploads;

/**
 *
 */
class LegalEntities extends Component
{

    use FormTrait, Cipher, WithFileUploads;

    /**
     * @var string
     */
    const CACHE_PREFIX = 'register_legal_entity_form';

    /**
     * @var LegalEntitiesForms The Form
     */
    public LegalEntitiesForms $legal_entity_form;

    /**
     * @var LegalEntity|null The Legal Entity being filled
     */
    public ?LegalEntity $legalEntity;

    /**
     * @var Employee
     */
    public Employee $employee;

    /**
     * @var int The total number of steps
     */
    public int $totalSteps = 8;

    /**
     * @var int The current step
     */
    public int $currentStep = 1;

    /**
     * @var string The Cache ID to store Legal Entity being filled by the current user
     */
    protected string $entityCacheKey;

    /**
     * @var string The Cache ID to store Owner being filled by the current user
     */
    protected string $ownerCacheKey;

    /**
     * @var string[]
     */
    protected $listeners = ['addressDataFetched'];

    /**
     * @var array|array[]|null
     */
    public ?array $steps = [
        'edrpou'        => [
            'title'    => 'ЄДРПОУ',
            'step'     => 1,
            'property' => 'edrpou',
            'view'     => '_step_edrpou',
        ],
        'owner'         => [
            'title'    => 'Власник',
            'step'     => 2,
            'property' => 'owner',
            'view'     => '_step_owner',
        ],
        'phones'        => [
            'title'    => 'Контакти',
            'step'     => 3,
            'property' => 'phones',
            'view'     => '_step_contact',
        ],
        'addresses'     => [
            'title'    => 'Адреси',
            'step'     => 4,
            'property' => 'residence_address',
            'view'     => '_step_residence_address',
        ],
        'accreditation' => [
            'title'    => 'Акредитація',
            'step'     => 5,
            'property' => 'residence_address',
            'view'     => '_step_accreditation',
        ],
        'license'       => [
            'title'    => 'Ліцензії',
            'step'     => 6,
            'property' => 'license',
            'view'     => '_step_license',

        ],
        'beneficiary'   => [
            'title'    => 'Інформація',
            'step'     => 7,
            'property' => 'license',
            'view'     => '_step_additional_information',
        ],
        'public_offer'  => [
            'title'    => 'Завершити',
            'step'     => 8,
            'property' => 'public_offer',
            'view'     => '_step_public_offer',
        ],
    ];

    /**
     * @var array|null
     */
    public ?array $addresses;

    /**
     * @var object|null
     */
    public ?object $file = null;


    /**
     * @var array|string[] Get dictionaries keys
     */
    public array $dictionaryNames = [
        'PHONE_TYPE',
        'POSITION',
        'LICENSE_TYPE',
        'SETTLEMENT_TYPE',
        'GENDER',
        'SPECIALITY_LEVEL',
        'ACCREDITATION_CATEGORY',
        'DOCUMENT_TYPE'
    ];

    /**
     * @return void set cache keys
     */

    public function boot(): void
    {
        $this->entityCacheKey = self::CACHE_PREFIX . '-' . Auth::id() . '-' . LegalEntity::class;
        $this->ownerCacheKey = self::CACHE_PREFIX . '-' . Auth::id() . '-' . Employee::class;
    }


    public function mount(): void
    {
        $this->getLegalEntity();
        $this->getDictionary();
        $this->stepFields();
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
            $this->getDictionariesFields($keys, $type);
        }
    }


    public function getLegalEntity(): void
    {

        // Try to get the LegalEntity from the cache
        $this->legalEntity = $this->getLegalEntityFromAuth() ?? $this->getLegalEntityFromCache();

        // If a LegalEntity is found, fill the form with its data
        if ($this->legalEntity) {
            $this->legal_entity_form->fill($this->legalEntity->toArray());
        }

        // Set the owner information from the cache if available
        $this->setOwnerFromCache();
    }

    private function getLegalEntityFromCache(): ?LegalEntity
    {
        // Check if the LegalEntity data exists in the cache
        if (Cache::has($this->entityCacheKey)) {
            $legalEntityData = Cache::get($this->entityCacheKey);
            $legalEntity = new LegalEntity();
            $legalEntity->fill($legalEntityData->toArray());

            return $legalEntity; // Return the filled LegalEntity
        }
        return null; // Return null if not found
    }

    /**
     * Get the legal entity associated with the currently authenticated user.
     *
     * @return LegalEntity|null
     */
    private function getLegalEntityFromAuth(): ?LegalEntity
    {
        return auth()->user()->legalEntity ?? null;
    }

    /**
     * Set the owner information from the cache if available.
     */
    private function setOwnerFromCache(): void
    {
        // Check if the owner information is available in the cache and the user is not a legal entity
        if (Cache::has($this->ownerCacheKey) && !Auth::user()->legalEntity) {
            $this->legal_entity_form->owner = Cache::get($this->ownerCacheKey); // Set the owner information from cache
        }
    }

    public function addRowPhone($property): array
    {
        if ($property == 'phones') {
            return $this->legal_entity_form->{$property}[] = ['type' => '', 'number' => ''];
        }

        return $this->legal_entity_form->{$property}['phones'][] = ['type' => '', 'number' => ''];
    }


    public function removePhone(int $key, string $property): void
    {
        if ($property == 'phones') {
            unset($this->legal_entity_form->{$property}[$key]);
        } else {
            unset($this->legal_entity_form->{$property}['phones'][$key]);

        }
    }



    /**
     * Increases the current step of the process.
     * Resets the error bag, validates the data, increments the current step, puts the legal entity in cache,
     * and ensures the current step does not exceed the total steps.
     *
     * @throws ValidationException
     */
    public function increaseStep(): void
    {
        $this->resetErrorBag();
        $this->validateData();
        $this->currentStep++;
        $this->putLegalEntityInCache();
        if ($this->currentStep > $this->totalSteps) {
            $this->currentStep = $this->totalSteps;
        }
    }

    /**
     * Get the title based on the current step number.
     *
     * @param int $currentStep The current step number
     * @return string The title of the step
     */
    public function getTitleByStep(int $currentStep): string
    {
        // Iterate through each step
        foreach ($this->steps as $step) {
            // Check if the step number matches the current step
            if ($step['step'] === $currentStep) {
                // Return the title of the step
                return $step['title'];
            }
        }
        // Return an empty string if no matching step is found
        return '';
    }


    public function setCertificateAuthority(): array|null
    {
        return $this->getCertificateAuthority = $this->getCertificateAuthority();
    }


    public function stepFields(): void
    {
        foreach ($this->steps as $step) {

            if (!empty($this->legal_entity_form->{$step['property']})) {
                continue;
            }

            $this->currentStep = $step['step'];
            break;

        }
    }

    /**
     * @param int $step
     * @return void
     */
    public function changeStep(int $step, $property): void
    {

        if (is_array($this->legal_entity_form->{$property}) && empty(removeEmptyKeys($this->legal_entity_form->{$property}))) {
            return;
        }
        if (!$this->arePreviousStepsFilled($step)) {
            return;
        }
        $this->currentStep = $step;
    }

    /**
     * @param int $step
     * @return bool
     */
    private function arePreviousStepsFilled(int $step): bool
    {
        foreach ($this->steps as $key => $stepData) {
            if ($stepData['step'] < $this->steps[$this->getStepKeyByStepNumber($step)]['step']) {
                $property = $stepData['property'];
                if (empty($this->legal_entity_form->{$property})) {
                    return false;
                }
            }
        }
        return true;
    }

    private function getStepKeyByStepNumber(int $step): ?string
    {
        foreach ($this->steps as $key => $stepData) {
            if ($stepData['step'] === $step) {
                return $key;
            }
        }
        return null;
    }

    /**
     * @return void
     */
    public function decreaseStep(): void
    {
        $this->resetErrorBag();
        $this->currentStep--;
        if ($this->currentStep < 1) {
            $this->currentStep = 1;
        }
    }

    /**
     * @throws ValidationException
     */
    public function validateData(): ?array
    {
        return match ($this->currentStep) {
            1 => $this->stepEdrpou(),
            2 => $this->stepOwner(),
            3 => $this->stepContact(),
            4 => $this->stepAddress(),
            5 => $this->stepAccreditation(),
            6 => $this->stepLicense(),
            7 => $this->stepAdditionalInformation(),
            default => [],
        };
    }


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
            $this->legal_entity_form->fill($normalizedData);
            if (!Cache::has($this->entityCacheKey) || $this->checkChanges()) {
                Cache::put($this->entityCacheKey, $this->legalEntity, now()->days(90));
            }
        }
    }

    /**
     * Update the legal entity in the cache if changes are detected or it doesn't exist already.
     */
    public function putLegalEntityInCache(): void
    {
        // Fill the legal entity model with data from the form
        $this->legalEntity->fill($this->legal_entity_form->toArray());

        // Check if the entity is not in the cache or if changes are detected
        if (!Cache::has($this->entityCacheKey) || $this->checkChanges()) {
            // Put the legal entity in the cache with a 90-day expiration
            Cache::put($this->entityCacheKey, $this->legalEntity, now()->days(90));
        }
    }

    /**
     * Check if there are changes in the Legal Entity attributes by comparing with cached data.
     *
     * @return bool Returns true if Legal Entity attributes have changed, false otherwise.
     */
    public function checkChanges(): bool
    {
        // Check if entity cache exists
        if (Cache::has($this->entityCacheKey)) {
            // If the Legal Entity has not changed, return false
            if (empty(array_diff_assoc($this->legalEntity->getAttributes(),
                Cache::get($this->entityCacheKey)->getAttributes()))) {
                return false; // Legal Entity has not changed
            }
        }

        return true; // Legal Entity has changed
    }

    /**
     * Check if the Legal Entity owner has changed.
     *
     * @return bool Returns true if the Legal Entity owner has changed, false otherwise.
     */
    public function checkOwnerChanges(): bool
    {
        // Check if the owner information is cached
        if (Cache::has($this->ownerCacheKey)) {
            $cachedOwner = Cache::get($this->ownerCacheKey);
            $legalEntityOwner = $this->legal_entity_form->owner;

            // Compare the cached owner with the current owner
            if (serialize($cachedOwner) === serialize($legalEntityOwner)) {
                return false; // No change in Legal Entity owner
            }
        }

        return true; // Return true if the Legal Entity owner has changed
    }

    // #Step  1 Create Legal Entity
    public function stepEdrpou(): void
    {
        $this->legal_entity_form->rulesForEdrpou();
        //TODO: Метод для перевірки ЕДРПОУ getLegalEntity
        $getLegalEntity = [];

        if (!empty($getLegalEntity)) {
            $this->saveLegalEntityFromExistingData($getLegalEntity);
        } else {
            $this->putLegalEntityInCache();
        }

    }

    // Step  2 Create Owner
    public function stepOwner(): void
    {

        $this->legal_entity_form->rulesForOwner();
        // Check if the owner information is available in the cache
        $personData = $this->legal_entity_form->owner;
        // Store the owner information in the cache
        if ($this->checkOwnerChanges()) {
            Cache::put($this->ownerCacheKey, $personData, now()->days(90));
        }
        if (isset($this->legalEntity->phones) && !empty($this->legalEntity->phones)) {
            $this->phones = $this->legalEntity->phones;
        }

    }

    // Step  3 Create/Update Contact[Phones, Email,beneficiary,receiver_funds_code]
    public function stepContact(): void
    {
        $this->legal_entity_form->rulesForContact();
    }

    // Step  4 Create/Update Address
    public function stepAddress(): void
    {
        $this->fetchDataFromAddressesComponent();
        $this->dispatch('address-data-fetched');

    }

    /**
     * Checks if the residence address in the legal entity form is an array and not empty.
     * If it is, increment the current step and put the legal entity in the cache.
     */
    public function checkAndProceedToNextStep(): void
    {
        if (is_array($this->legal_entity_form->residence_address) && !empty($this->legal_entity_form->residence_address)) {
            $this->currentStep++;
        }
        $this->putLegalEntityInCache();
    }

    // Step  5 Create/Update Accreditation
    public function stepAccreditation(): void
    {
        if (!empty(removeEmptyKeys($this->legal_entity_form->accreditation))) {
            $this->legal_entity_form->rulesForAccreditation();
        }
    }

    // Step  6 Create/Update License
    public function stepLicense(): void
    {
        $this->legal_entity_form->license['type'] = 'MSP';

        $this->legal_entity_form->rulesForLicense();
    }

    // Step  7 Create/Update Additional Information
    public function stepAdditionalInformation(): void
    {
        $this->legal_entity_form->rulesForAdditionalInformation();
    }

    /**
     * Updates the keyContainerUpload property with the value of the file property.
     */
    public function updatedFile(): void
    {
        if (!empty($this->file)) {
            $this->keyContainerUpload = $this->file;
        }
    }

    /**
     * Step for handling sing legal entity  submission.
     *
     * @throws ValidationException
     */
    public function signLegalEntity(): void
    {
        // Final Validate the legal entity form data
        $this->legal_entity_form->validate();

        if ($this->getErrorBag()->isNotEmpty()) {
            $this->dispatchBrowserEvent('scroll-to-error');
        }

        // Prepare data for public offer
        $this->legal_entity_form->public_offer = $this->preparePublicOffer();

        // Prepare security data
        $this->legal_entity_form->security = $this->prepareSecurityData();

        // Convert form data to an array
        $data = $this->prepareDataForRequest($this->legal_entity_form->toArray());

        $taxId = $this->legal_entity_form->owner['tax_id'];

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
        // Converting documents to array
        if (isset($data['owner']['documents'])) {
            $data['owner']['documents'] = [$data['owner']['documents']];
        }

        // Converting tax_id to no_tax_id
        $data['owner']['no_tax_id'] = empty($data['owner']['tax_id']);

        // Converting archive to array
        $data['archive'] = [$data['archive'] ?? []];

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
            if (!\auth()->user()->legalEntity->getOwner()->exists()) {
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
            $this->redirect('/legal-entities/edit');
        } catch (\Exception $e) {
            $this->dispatchErrorMessage(__('Сталася помилка під час обробки запиту'), ['error' => $e->getMessage()]);
            return;
        }
    }


    /**
     * Create a new legal entity based on the provided data.
     *
     * @param array $data  data needed to create the legal entity.
     * @return void
     */
    public function createOrUpdateLegalEntity(array $data): void
    {
        // Get the UUID from the data, if it exists
        $uuid = $data['data']['id'] ?? '';

        if (empty($uuid)) {
            $this->dispatchErrorMessage(__('Не вдалось створити Юридичну особу'), ['errors' => 'No UUID found in data']);
            return;
        }
        // Find or create a new LegalEntity object by UUID
        $this->legalEntity = LegalEntity::firstOrNew(['uuid' => $uuid]);

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
    }


    public function createUser(): User
    {
        // Get the currently authenticated user
        $authenticatedUser = Auth::user();

        // Retrieve the email address of the legal entity owner from the form or set it to null
        $email = $this->legal_entity_form->owner['email'] ?? null;

        // Generate a random password
        $password = Str::random(10);

        // Check if a user with the provided email already exists
        $user = User::where('email', $email)->first();

        // If the authenticated user is the owner, use them as the user
        if (isset($authenticatedUser->email) && strtolower($authenticatedUser->email) === $email) {
            // If the authenticated user is the owner, use them as the user
            $user = $authenticatedUser;
        } elseif (!$user) {
            // If no user exists with that email, create a new user
            $user = User::create([
                'email'    => $email,
                'password' => Hash::make($password),
            ]);
        }
        // Associate the legal entity with the user
        $user->legalEntity()->associate($this->legalEntity);
        $user->save();

        // Assign the 'OWNER' role to the user
        $user->assignRole('OWNER');

        // Send an email with the owner credentials to the user
        Mail::to($user->email)->send(new OwnerCredentialsMail($user->email));

        return $user;
    }


    /**
     * Create a new license with the provided data.
     *
     * @param array $data The data to fill the license with.
     */
    public function createLicense(array $data): void
    {
        $license = License::firstOrNew(['uuid' => $data['id']]);
        $license->fill($data);
        $license->uuid = $data['id'];
        $license->is_primary = true;
        if (isset($this->legalEntity)) {
            $this->legalEntity->licenses()->save($license);
        }
    }


    // Fetch data from Addresses component
    public function fetchDataFromAddressesComponent(): void
    {
        $this->dispatch('fetchAddressData');
    }

    // Address data fetched
    public function addressDataFetched($addressData): void
    {
        $this->legal_entity_form->residence_address = $addressData;

        if (is_array($this->legal_entity_form->residence_address) && !empty($this->legal_entity_form->residence_address)) {
            $this->currentStep++;
        }

        $this->putLegalEntityInCache();
    }

    public function render()
    {
        return view('livewire.legal-entity.legal-entities');
    }
}
