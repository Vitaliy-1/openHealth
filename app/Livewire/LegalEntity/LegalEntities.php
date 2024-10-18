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
use App\Traits\Cipher;
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

    const CACHE_PREFIX = 'register_legal_entity_form';

    public LegalEntitiesForms $legal_entity_form;

    public LegalEntity $legalEntity;

    public Employee $employee;

    public int $totalSteps = 8;

    public int $currentStep = 1;
    /**
     * @var string The Cache ID to store Legal Entity being filled by the current user
     */
    protected string $entityCacheKey;
    protected string $ownerCacheKey;

    protected $listeners = ['addressDataFetched'];

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

    public ?array $addresses;

    public ?object  $file = null;
    /**
     * @var array|null
     */
    public ?array $getCertificateAuthority;


    public function rules(): array
    {
        return [
            'knedp'                                  => 'required|string',
            'file'                     => 'required|file|mimes:dat,zs2,sk,jks,pk8,pfx',
            'password'                               => 'required|string|max:255',
            'legal_entity_form.public_offer.consent' => 'accepted',
        ];
    }

    public array $dictionaries_field = [
        'PHONE_TYPE',
        'POSITION',
        'LICENSE_TYPE',
        'SETTLEMENT_TYPE',
        'GENDER',
        'SPECIALITY_LEVEL',
        'ACCREDITATION_CATEGORY',
        'DOCUMENT_TYPE'
    ];

    public function boot(): void
    {
        $this->entityCacheKey = self::CACHE_PREFIX . '-' . Auth::id() . '-' . LegalEntity::class;
        $this->ownerCacheKey = self::CACHE_PREFIX . '-' . Auth::id() . '-' . Employee::class;
    }


    public function mount(): void
    {

        if (Auth::user()->hasRole('OWNER')) {
          $this->redirect('/legal-entity/edit');
        }
        $this->getLegalEntity();
        $this->getDictionary();
        $this->stepFields();
        $this->setCertificateAuthority();
        $this->getOwnerFields();
    }


    public function getOwnerFields(): void
    {
        $fields = [
            'POSITION'      => ['P1', 'P2', 'P3', 'P32', 'P4', 'P6', 'P5'],
            'DOCUMENT_TYPE' => ['PASSPORT', 'NATIONAL_ID']
        ];

        foreach ($fields as $type => $keys) {
            $this->getDictionariesFields($keys, $type);
        }
    }


    public function getLegalEntity(): void
    {

        // Search Legal entity in the cache by user ID
        if (Cache::has($this->entityCacheKey)) {
            $this->legalEntity = new LegalEntity();
            $this->legalEntity->fill(Cache::get($this->entityCacheKey)->toArray());
            $this->legal_entity_form->fill($this->legalEntity->toArray());
        } else {
            // new Legal Entity clas
            $this->legalEntity = new LegalEntity();
        }
        // Search Legal entity in the cache by user ID
        if (Cache::has($this->ownerCacheKey)) {
            $this->legal_entity_form->owner = Cache::get($this->ownerCacheKey);
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

    public function getTitleByStep(int $currentStep): string
    {
        foreach ($this->steps as $step) {
            if ($step['step'] === $currentStep) {
                return $step['title'];
            }
        }
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

    public function changeStep(int $step): void
    {
        if (!$this->arePreviousStepsFilled($step)) {
            return;
        }
        $this->currentStep = $step;
    }

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
    public function validateData(): bool|array|null
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

    public function register()
    {
        $this->stepPublicOffer();
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

    public function putLegalEntityInCache(): void
    {
        $this->legalEntity->fill($this->legal_entity_form->toArray());

        if (!Cache::has($this->entityCacheKey) || $this->checkChanges()) {
            Cache::put($this->entityCacheKey, $this->legalEntity, now()->days(90));
        }
    }

    public function checkChanges(): bool
    {
        if (Cache::has($this->entityCacheKey)) {
            // If the Legal Entity has not changed, return false
            if (empty(array_diff_assoc($this->legalEntity->getAttributes(),
                Cache::get($this->entityCacheKey)->getAttributes()))) {
                return false; // If
            }
        }
        return true; // Return true if the Legal Entity has changed
    }

    public function checkOwnerChanges(): bool
    {
        if (Cache::has($this->ownerCacheKey)) {
            $cachedOwner = Cache::get($this->ownerCacheKey);

            $legalEntityOwner = $this->legal_entity_form->owner;

            if (serialize($cachedOwner) === serialize($legalEntityOwner)) {
                return false;
            }
        }
        return true; // Return true if the Legal Entity has changed
    }


    // #Step  1 Request to Ehealth API get Legal Entity
    public function stepEdrpou(): void
    {
        $this->legal_entity_form->rulesForEdrpou();
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

        $personData = $this->legal_entity_form->owner;

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

    public function updatedFile(): void{
        $this->keyContainerUpload = $this->file ;
    }


    //Final Step
    public function stepPublicOffer(): void
    {
        //TODO: Upload Files with Traits

        $this->validate($this->rules());

        // Preparing data for public offer and security fields
        $this->legal_entity_form->public_offer = $this->preparePublicOffer();

        $this->legal_entity_form->security = $this->prepareSecurityData();

        // Converting the form data to an array
        $data = $this->prepareDataForRequest($this->legal_entity_form->toArray());

        // Sending encrypted data
        $base64Data = $this->sendEncryptedData($data);

        if (isset($base64Data['errors'])) {
            $this->dispatchErrorMessage($base64Data['errors']);
            return;
        }

        // Preparing data for API request
        $request = LegalEntitiesRequestApi::_createOrUpdate([
            'signed_legal_entity_request' => $base64Data,
            'signed_content_encoding'     => 'base64',
        ]);

        // Handling request errors
        if (isset($request['errors']) && is_array($request['errors'])) {
            $this->dispatchErrorMessage(__('Сталася невідома помилка'), $request['errors']);
            return;
        }

        // Successful request handling
        if (!empty($request)) {
            $this->handleSuccessResponse($request);
        }

        $this->dispatchErrorMessage(__('Сталася невідома помилка'), $request['errors']);

    }

    private function preparePublicOffer(): array
    {
        return [
            'consent_text' => 'Sample consent_text',
            'consent'      => true
        ];
    }

    private function prepareSecurityData(): array
    {
        return [
            'redirect_uri' => 'https://openhealths.com',
        ];
    }

    private function prepareDataForRequest(array $data): array
    {

        if (isset($data['owner']['documents'])) {
            $data['owner']['documents'] = [$data['owner']['documents']];
        }

        $data['owner']['no_tax_id'] = empty($data['owner']['tax_id']);
        $data['archive'] = [$data['archive'] ?? []];

        return removeEmptyKeys($data);
    }


    private function dispatchErrorMessage(string $message, array $errors = []): void
    {
        $this->dispatch('flashMessage', [
            'message' => $message,
            'type'    => 'error',
            'errors'  => $errors
        ]);
    }

    private function handleSuccessResponse(array $request): void
    {
        $this->createLegalEntity($request);
        $this->createUser();
        $this->createLicense($request['data']['license']);

        Cache::forget($this->entityCacheKey);
        Cache::forget($this->ownerCacheKey);

        $this->redirect('/legal-entities/edit');
    }


    /**
     * Create a new legal entity based on the provided data.
     *
     * @param array $data The data needed to create the legal entity.
     * @return void
     */
    public function createLegalEntity(array $data): void
    {
        // Fill the legal entity with the data
        $this->legalEntity->fill($data['data']);

        // Set UUID from data or default to empty string
        $this->legalEntity->uuid = $data['data']['id'] ?? '';

        // Set client secret from data or default to empty string
        $this->legalEntity->client_secret = $data['urgent']['security']['client_secret'] ?? '';

        // Set client id from data or default to null
        $this->legalEntity->client_id = $data['urgent']['security']['client_id'] ?? null;
        // Save the legal entity
        $this->legalEntity->save();
    }

    /**
     * Create a new user with provided email and assign them as the owner of a legal entity.
     * If the user already exists, associate them with the legal entity.
     * If the user does not exist, create a new user with a random password.
     *
     * @return User The created or updated user
     */
    public function createUser(): User
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Get the email address of the legal entity owner from the form or set it to null
        $email = $this->legal_entity_form->owner['email'] ?? null;

        // Generate a random password
        $password = Str::random(10);

        // Check if a user with the same email as the legal entity owner already exists
        if ($user->email === $email) {
            $user->legalEntity()->associate($this->legalEntity);
            $user->save();
        } elseif (User::where('email', $email)->exists()) {
            // If user exists, get the user and associate them with the legal entity
            $user = User::where('email', $email)->first();
            $user->legalEntity()->associate($this->legalEntity);
            $user->save();
        } else {
            // If user does not exist, create a new user and assign them to the legal entity
            $user = User::create([
                'email'    => $email,
                'password' => Hash::make($password),
            ]);
            $user->legalEntity()->associate($this->legalEntity);
            $user->save();
        }

        // Assign the 'OWNER' role to the user
        $user->assignRole('OWNER');

        // Send an email with owner credentials to the user
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
        $license = new License();

        $license->fill($data);
        $license->legal_entity_id = $this->legalEntity->id;
        $license->is_primary = true;
        $license->save();
        $license->legalEntity()->associate($this->legalEntity);
    }


    public function fetchDataFromAddressesComponent(): void
    {

        $this->dispatch('fetchAddressData');
    }

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
