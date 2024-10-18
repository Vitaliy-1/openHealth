<?php

namespace App\Livewire\LegalEntity;

use App\Classes\Cipher\Api\CipherApi;
use App\Helpers\JsonHelper;
use App\Livewire\LegalEntity\Forms\LegalEntitiesForms;
use App\Livewire\LegalEntity\Forms\LegalEntitiesRequestApi;
use App\Mail\OwnerCredentialsMail;
use App\Models\Employee;
use App\Models\LegalEntity;
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
            'knedp'              => 'required|string',
            'keyContainerUpload' => 'required|file|mimes:dat,zs2,sk,jks,pk8,pfx',
            'password'           => 'required|string|max:255',
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
            'redirect_uri' => 'https://openhealths.com'
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
          'signed_legal_entity_request'        => 'MIIViAYJKoZIhvcNAQcCoIIVeTCCFXUCAQExDjAMBgoqhiQCAQEBAQIBMIIHhgYJKoZIhvcNAQcBoIIHdwSCB3N7CgkiZWRycG91IjogIjMxMzk4MjE1NTkiLAoJInR5cGUiOiAiUFJJTUFSWV9DQVJFIiwKCSJyZXNpZGVuY2VfYWRkcmVzcyI6IHsKCQkidHlwZSI6ICJSRVNJREVOQ0UiLAoJCSJjb3VudHJ5IjogIlVBIiwKCQkiYXJlYSI6ICLQnC7QmtCY0IfQkiIsCgkJInNldHRsZW1lbnQiOiAi0JrQuNGX0LIiLAoJCSJzZXR0bGVtZW50X3R5cGUiOiAiQ0lUWSIsCgkJInNldHRsZW1lbnRfaWQiOiAiYWRhYTRhYmYtZjUzMC00NjFjLWJjYmYtYTBhYzIxMGQ5NTViIiwKCQkic3RyZWV0X3R5cGUiOiAiU1RSRUVUIiwKCQkic3RyZWV0IjogItCR0L7RgNC40YHQv9GW0LvRjNGB0YzQutCwIiwKCQkiYnVpbGRpbmciOiAiMjbQtyIsCgkJImFwYXJ0bWVudCI6ICIxMTIiLAoJCSJ6aXAiOiAiMDIwOTMiCgl9LAoJInBob25lcyI6IFsKCQl7CgkJCSJ0eXBlIjogIk1PQklMRSIsCgkJCSJudW1iZXIiOiAiKzM4MDUwNjQ5MTI0NCIKCQl9CgldLAoJImVtYWlsIjogInZpdGFsaXliZXpzaEBnbWFpbC5jb20iLAoJIndlYnNpdGUiOiAid3d3Lm9wZW5oZWFsdGhzLmNvbSIsCgkiYmVuZWZpY2lhcnkiOiAi0JHQtdC30YjQtdC50LrQviDQktGW0YLQsNC70ZbQuSDQk9GA0LjQs9C+0YDQvtCy0LjRhyIsCgkib3duZXIiOiB7CgkJImZpcnN0X25hbWUiOiAi0JLRltGC0LDQu9GW0LkiLAoJCSJsYXN0X25hbWUiOiAi0JHQtdC30YjQtdC50LrQviIsCgkJInNlY29uZF9uYW1lIjogItCT0YDQuNCz0L7RgNC+0LLQuNGHIiwKCQkidGF4X2lkIjogIjMxMzk4MjE1NTkiLAoJCSJub190YXhfaWQiOiBmYWxzZSwKCQkiYmlydGhfZGF0ZSI6ICIxOTg1LTEyLTE4IiwKCQkiZ2VuZGVyIjogIk1BTEUiLAoJCSJlbWFpbCI6ICJ2aXRhbGl5YmV6c2hAZ21haWwuY29tIiwKCQkiZG9jdW1lbnRzIjogWwoJCQl7CgkJCQkidHlwZSI6ICJQQVNTUE9SVCIsCgkJCQkibnVtYmVyIjogItCh0J45NTk5OTMiLAoJCQkJImlzc3VlZF9ieSI6ICLQlNC10YHQvdGP0L3RgdGM0LrQuNC8INCg0JIg0JPQoyDQnNCS0KEg0LIg0LzRltGB0YLRliDQmtC40ZTQstGWIiwKCQkJCSJpc3N1ZWRfYXQiOiAiMjAwMi0wMy0yOCIKCQkJfQoJCV0sCgkJInBob25lcyI6IFsKCQkJewoJCQkJInR5cGUiOiAiTU9CSUxFIiwKCQkJCSJudW1iZXIiOiAiKzM4MDUwNjQ5MTI0NCIKCQkJfQoJCV0sCgkJInBvc2l0aW9uIjogIlAyIgoJfSwKCSJhY2NyZWRpdGF0aW9uIjogewoJCSJjYXRlZ29yeSI6ICJTRUNPTkQiLAoJCSJpc3N1ZWRfZGF0ZSI6ICIyMDE3LTAyLTI4IiwKCQkiZXhwaXJ5X2RhdGUiOiAiMjAyNy0wMi0yOCIsCgkJIm9yZGVyX25vIjogImZkMTIzNDQzIiwKCQkib3JkZXJfZGF0ZSI6ICIyMDE3LTAyLTI4IgoJfSwKCSJsaWNlbnNlIjogewoJCSJ0eXBlIjogIk1TUCIsCgkJImxpY2Vuc2VfbnVtYmVyIjogImZkMTIzNDQzIiwKCQkiaXNzdWVkX2J5IjogItCa0LLQsNC70ZbRhNGW0LrQsNGG0LnQvdCwINC60L7QvNGW0YHRltGPIiwKCQkiaXNzdWVkX2RhdGUiOiAiMjAxNy0wMi0yOCIsCgkJImV4cGlyeV9kYXRlIjogIjIwMjctMDItMjgiLAoJCSJhY3RpdmVfZnJvbV9kYXRlIjogIjIwMTctMDItMjgiLAoJCSJ3aGF0X2xpY2Vuc2VkIjogItGA0LXQsNC70ZbQt9Cw0YbRltGPINC90LDRgNC60L7RgtC40YfQvdC40YUg0LfQsNGB0L7QsdGW0LIiLAoJCSJvcmRlcl9ubyI6ICLQktCQNDMyMzQiCgl9LAoJImFyY2hpdmUiOiBbCgkJewoJCQkiZGF0ZSI6ICIyMDE3LTAyLTI4IiwKCQkJInBsYWNlIjogItCy0YPQuy4g0JPR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',
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
            $this->saveLegalEntityFromExistingData($request['data']);
            $this->legalEntity->client_secret = $request['urgent']['security']['client_secret'] ?? '';
            $this->legalEntity->client_id = $request['urgent']['security']['client_id'] ?? null;
            $this->legalEntity->save();
            $this->createUser();
//            Cache::forget($this->entityCacheKey);
//            Cache::forget($this->ownerCacheKey);
            $this->redirect('/legal-entities/edit');
        }

    }

    public function createUser()
    {
        $user = Auth::user();
        $email = $this->legal_entity_form->owner['email'] ?? null;
        $password = Str::random(10);

        if ($user->email === $email) {
            $user->legalEntity()->associate($this->legalEntity);
            $user->save();
        }elseif (User::where('email', $email)->exists()) {
            $user = User::where('email', $email)->first();
            $user->legalEntity()->associate($this->legalEntity);
            $user->save();
        }
        else {
            $user = User::create([
                'email'    => $email,
                'password' => Hash::make($password),
            ]);
            $user->legalEntity()->associate($this->legalEntity);
            $user->save();
        }

        $user->assignRole('OWNER');


        Mail::to($user->email)->send(new OwnerCredentialsMail($user->email));

        return $user;
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
