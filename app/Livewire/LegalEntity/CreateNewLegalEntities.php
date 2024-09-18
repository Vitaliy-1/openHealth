<?php

namespace App\Livewire\LegalEntity;

use App\Classes\Cipher\Api\CipherApi;
use App\Helpers\JsonHelper;
use App\Livewire\LegalEntity\Forms\LegalEntitiesForms;
use App\Livewire\LegalEntity\Forms\LegalEntitiesRequestApi;
use App\Mail\OwnerCredentialsMail;
use App\Models\Employee;
use App\Models\LegalEntity;
use App\Models\License;
use App\Models\Person;
use App\Models\User;
use App\Traits\FormTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Livewire\WithFileUploads;
use Mockery\Exception;

/**
 *
 */
class CreateNewLegalEntities extends Component
{

    use FormTrait, WithFileUploads;

    const CACHE_PREFIX = 'register_legal_entity_form';

    public LegalEntitiesForms $legal_entity_form;

    public LegalEntity $legalEntity;

    public Person $person;

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
        ],
        'owner'         => [
            'title'    => 'Власник',
            'step'     => 2,
            'property' => 'owner',
        ],
        'phones'        => [
            'title'    => 'Контакти',
            'step'     => 3,
            'property' => 'phones',
        ],
        'addresses'     => [
            'title'    => 'Адреси',
            'step'     => 4,
            'property' => 'residence_address',
        ],
        'accreditation' => [
            'title'    => 'Акредитація',
            'step'     => 5,
            'property' => 'residence_address'
        ],
        'license'       => [
            'title'    => 'Ліцензії',
            'step'     => 6,
            'property' => 'license'

        ],
        'beneficiary'   => [
            'title'    => 'Додаткова інформація',
            'step'     => 7,
            'property' => 'license'
        ],
        'public_offer'  => [
            'title'    => 'Завершити реєстрацію',
            'step'     => 8,
            'property' => 'public_offer'
        ],
    ];

    public ?array $addresses;

    /**
     * @var array|null
     */
    public ?array $getCertificateAuthority;
    public string $knedp = '';
    public $keyContainerUpload;

    public string $password = '';

    public function rules(): array
    {
        return [
            'knedp'                                  => 'required|string',
            'keyContainerUpload'                     => 'required|file|mimes:dat,zs2,sk,jks,pk8,pfx',
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


    public function setCertificateAuthority(): array|null
    {
        return $this->getCertificateAuthority = (new CipherApi())->getCertificateAuthority();
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

    public function saveLegalEntity(): void
    {
        $this->legalEntity->save();
    }

    // #Step  1 Request to Ehealth API get Legal Entity
    public function stepEdrpou(): void
    {
        $this->legal_entity_form->rulesForEdrpou();
        $getLegalEntity = [];

        if (\auth()->user()->legalEntity) {
            $getLegalEntity = LegalEntitiesRequestApi::getLegalEntitie($this->legal_entity_form->edrpou);
        }
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

    /**
     * @throws ValidationException
     */
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

    //Final Step
    public function stepPublicOffer(): void
    {

        $this->validate($this->rules());

        $this->legal_entity_form->public_offer = [
            'consent_text' => 'Тестове consent_text',
            'consent'      => true
        ];

        $this->legal_entity_form->security = [
            'redirect_uri' => env('APP_URL'),
        ];

        $data = $this->legal_entity_form->toArray();

        if (isset($this->legal_entity_form->owner['documents'])) {
            $data['owner']['documents'] = [$this->legal_entity_form->owner['documents']];
        }

        $data['owner']['no_tax_id'] = empty($this->legal_entity_form->owner['tax_id']);

        $data['archive'] = [$this->legal_entity_form->archive ?? []];

        $removeKeyEmpty = removeEmptyKeys($data);

        $base64Data = (new CipherApi())->sendSession(
            json_encode($removeKeyEmpty),
            $this->password,
            $this->keyContainerUpload,
            $this->knedp
        );
        if (isset($base64Data['errors'])) {
            $this->dispatch('flashMessage', [
                'message' => $base64Data['errors'],
                'type'    => 'error'
            ]);
            return;
        }

        $data = [
//          'signed_legal_entity_request' => $base64Data,
           'signed_legal_entity_request'             => 'MIJa6wYJKoZIhvcNAQcCoIJa3DCCWtgCAQExDjAMBgoqhiQCAQEBAQIBMIIHhgYJKoZIhvcNAQcBoIIHdwSCB3N7CgkiZWRycG91IjogIjMxMzk4MjE1NTkiLAoJInR5cGUiOiAiUFJJTUFSWV9DQVJFIiwKCSJyZXNpZGVuY2VfYWRkcmVzcyI6IHsKCQkidHlwZSI6ICJSRVNJREVOQ0UiLAoJCSJjb3VudHJ5IjogIlVBIiwKCQkiYXJlYSI6ICLQnC7QmtCY0IfQkiIsCgkJInNldHRsZW1lbnQiOiAi0JrQuNGX0LIiLAoJCSJzZXR0bGVtZW50X3R5cGUiOiAiQ0lUWSIsCgkJInNldHRsZW1lbnRfaWQiOiAiYWRhYTRhYmYtZjUzMC00NjFjLWJjYmYtYTBhYzIxMGQ5NTViIiwKCQkic3RyZWV0X3R5cGUiOiAiU1RSRUVUIiwKCQkic3RyZWV0IjogItCR0L7RgNC40YHQv9GW0LvRjNGB0YzQutCwIiwKCQkiYnVpbGRpbmciOiAiMjbQtyIsCgkJImFwYXJ0bWVudCI6ICIxMTIiLAoJCSJ6aXAiOiAiMDIwOTMiCgl9LAoJInBob25lcyI6IFsKCQl7CgkJCSJ0eXBlIjogIk1PQklMRSIsCgkJCSJudW1iZXIiOiAiKzM4MDUwNjQ5MTI0NCIKCQl9CgldLAoJImVtYWlsIjogInZpdGFsaXliZXpzaEBnbWFpbC5jb20iLAoJIndlYnNpdGUiOiAid3d3Lm9wZW5oZWFsdGhzLmNvbSIsCgkiYmVuZWZpY2lhcnkiOiAi0JHQtdC30YjQtdC50LrQviDQktGW0YLQsNC70ZbQuSDQk9GA0LjQs9C+0YDQvtCy0LjRhyIsCgkib3duZXIiOiB7CgkJImZpcnN0X25hbWUiOiAi0JLRltGC0LDQu9GW0LkiLAoJCSJsYXN0X25hbWUiOiAi0JHQtdC30YjQtdC50LrQviIsCgkJInNlY29uZF9uYW1lIjogItCT0YDQuNCz0L7RgNC+0LLQuNGHIiwKCQkidGF4X2lkIjogIjMxMzk4MjE1NTkiLAoJCSJub190YXhfaWQiOiBmYWxzZSwKCQkiYmlydGhfZGF0ZSI6ICIxOTg1LTEyLTE4IiwKCQkiZ2VuZGVyIjogIk1BTEUiLAoJCSJlbWFpbCI6ICJ2aXRhbGl5YmV6c2hAZ21haWwuY29tIiwKCQkiZG9jdW1lbnRzIjogWwoJCQl7CgkJCQkidHlwZSI6ICJQQVNTUE9SVCIsCgkJCQkibnVtYmVyIjogItCh0J45NTk5OTMiLAoJCQkJImlzc3VlZF9ieSI6ICLQlNC10YHQvdGP0L3RgdGM0LrQuNC8INCg0JIg0JPQoyDQnNCS0KEg0LIg0LzRltGB0YLRliDQmtC40ZTQstGWIiwKCQkJCSJpc3N1ZWRfYXQiOiAiMjAwMi0wMy0yOCIKCQkJfQoJCV0sCgkJInBob25lcyI6IFsKCQkJewoJCQkJInR5cGUiOiAiTU9CSUxFIiwKCQkJCSJudW1iZXIiOiAiKzM4MDUwNjQ5MTI0NCIKCQkJfQoJCV0sCgkJInBvc2l0aW9uIjogIlAyIgoJfSwKCSJhY2NyZWRpdGF0aW9uIjogewoJCSJjYXRlZ29yeSI6ICJTRUNPTkQiLAoJCSJpc3N1ZWRfZGF0ZSI6ICIyMDE3LTAyLTI4IiwKCQkiZXhwaXJ5X2RhdGUiOiAiMjAyNy0wMi0yOCIsCgkJIm9yZGVyX25vIjogImZkMTIzNDQzIiwKCQkib3JkZXJfZGF0ZSI6ICIyMDE3LTAyLTI4IgoJfSwKCSJsaWNlbnNlIjogewoJCSJ0eXBlIjogIk1TUCIsCgkJImxpY2Vuc2VfbnVtYmVyIjogImZkMTIzNDQzIiwKCQkiaXNzdWVkX2J5IjogItCa0LLQsNC70ZbRhNGW0LrQsNGG0LnQvdCwINC60L7QvNGW0YHRltGPIiwKCQkiaXNzdWVkX2RhdGUiOiAiMjAxNy0wMi0yOCIsCgkJImV4cGlyeV9kYXRlIjogIjIwMjctMDItMjgiLAoJCSJhY3RpdmVfZnJvbV9kYXRlIjogIjIwMTctMDItMjgiLAoJCSJ3aGF0X2xpY2Vuc2VkIjogItGA0LXQsNC70ZbQt9Cw0YbRltGPINC90LDRgNC60L7RgtC40YfQvdC40YUg0LfQsNGB0L7QsdGW0LIiLAoJCSJvcmRlcl9ubyI6ICLQktCQNDMyMzQiCgl9LAoJImFyY2hpdmUiOiBbCgkJewoJCQkiZGF0ZSI6ICIyMDE3LTAyLTI4IiwKCQkJInBsYWNlIjogItCy0YPQuy4g0JPR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',
          'signed_content_encoding'     => 'base64',
        ];

        $request = LegalEntitiesRequestApi::_createOrUpdate($data);
        if (isset($request['errors']) && is_array($request['errors'])) {
            $this->dispatch('flashMessage', [
                'message' => __('Сталася помилка'),
                'type'    => 'error',
                'errors'  => $request['errors']
            ]);
            return;
        }

        if (!empty($request)) {
           $this->createLegalEntity($request);
            $this->createUser();
            $this->createLicense($request['data']['license']);
            Cache::forget($this->entityCacheKey);
            Cache::forget($this->ownerCacheKey);
            $this->redirect('/legal-entities/edit');
        }

    }


    /**
     * Create a new legal entity based on the provided data.
     *
     * @param array $data The data needed to create the legal entity.
     * @return void
     */
    public function createLegalEntity($data): void
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
    public function createUser()
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
    public function createLicense( array $data): void
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
        return view('livewire.legal-entity.create-new-legal-entities');
    }
}
