<?php

namespace App\Livewire\LegalEntity;

use App\Classes\Cipher\Api\CipherApi;
use App\Helpers\JsonHelper;
use App\Livewire\LegalEntity\Forms\LegalEntitiesForms;
use App\Livewire\LegalEntity\Forms\LegalEntitiesRequestApi;
use App\Models\Employee;
use App\Models\LegalEntity;
use App\Models\Person;
use App\Models\User;
use App\Traits\FormTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Livewire\WithFileUploads;
class CreateNewLegalEntities extends Component
{

    use FormTrait,WithFileUploads;
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
        'edrpou' => [
            'title' => 'ЄДРПОУ',
            'step' => 1,
            'property' => 'edrpou',
        ],
        'owner' => [
            'title' => 'Власник',
            'step' => 2,
            'property' => 'owner',
        ],
        'phones' => [
            'title' => 'Контакти',
            'step' => 3,
            'property' => 'phones',
        ],
        'addresses' => [
            'title' => 'Адреси',
            'step' => 4,
            'property' => 'residence_address',
        ],
        'accreditation' => [
            'title' => 'Акредитація',
            'step' => 5,
            'property' => 'accreditation'
        ],
        'license' => [
            'title' => 'Ліцензії',
            'step' => 6,
            'property' => 'license'

        ],
        'beneficiary' => [
            'title' => 'Додаткова інформація',
            'step' => 7,
            'property' => 'license'
        ],
        'public_offer' => [
            'title' => 'Завершити реєстрацію',
            'step' => 8,
            'property' => 'license'
        ],
    ];

    public ?array $addresses;

    public  ? array $getCertificateAuthority;

    public string $knedp = '';

    public   $keyContainerUpload;

    public string $password = '';

    public function rules()
    {
        return [
            'knedp' => 'required|string',
            'keyContainerUpload' => 'required|file|mimes:dat,zs2,sk,jks,pk8,pfx',
            'password' => 'required|string|max:255',
            'legal_entity_form.public_offer.consent' => 'accepted',

        ];
    }

    public function boot(): void
    {
        $this->entityCacheKey = self::CACHE_PREFIX . '-' . Auth::id() . '-' . LegalEntity::class;
        $this->ownerCacheKey = self::CACHE_PREFIX . '-' . Auth::id() . '-' . Employee::class;
    }

    public function mount(): void
    {

        $this->getLegalEntity();
        $this->setCertificateAuthority();
        $this->dictionaries = JsonHelper::searchValue('DICTIONARIES_PATH', [
            'PHONE_TYPE',
            'POSITION',
            'LICENSE_TYPE',
            'SETTLEMENT_TYPE',
            'GENDER',
            'SPECIALITY_LEVEL',
            'ACCREDITATION_CATEGORY',
            'DOCUMENT_TYPE'
        ]);
    }

    public function setCertificateAuthority():array|null
    {
       return $this->getCertificateAuthority = (new CipherApi())->getCertificateAuthority();
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

        $this->stepFields();

    }

    public function addRowPhone(): array
    {
        return $this->phones[] = ['type' => '', 'number' => ''];
    }

    public function increaseStep(): void
    {
        $this->resetErrorBag();
        $this->validateData();
        $this->currentStep++;
        $this->putLegalEntityInCache();

        if ($this->currentStep > $this->totalSteps ) {
            $this->currentStep = $this->totalSteps;
        }

    }

    public function stepFields(): void
    {
        foreach ($this->steps as $field => $step) {
            if (!empty($this->legal_entity_form->{$field})
            ) {
                continue;
            }
            $this->currentStep = $step['step'];
            break;
        }
    }

    public function changeStep(int $step, string $property): void
    {
        if (empty($this->legal_entity_form->{$property})) {
            return;
        }
        $this->currentStep = $step;

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

        if ($this->checkOwnerChanges() && !Cache::has($this->ownerCacheKey)) {
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

    }

    // Step  6 Create/Update License
    public function stepLicense(): void
    {
        $this->legal_entity_form->rulesForLicense();

    }

    // Step  7 Create/Update Additional Information
    public function stepAdditionalInformation(): void
    {

    }

    //Final Step
    public function stepPublicOffer(): void
    {



            $this->validate();


         $this->legal_entity_form->public_offer = [
             'consent_text' => 'Тестове consent_text',
             'consent' => true
         ];

        $this->legal_entity_form->owner['documents'] = [$this->legal_entity_form->owner['documents']];
        $this->legal_entity_form->owner['no_tax_id'] = !isset($this->legal_entity_form->owner['tax_id']);

        $this->legal_entity_form->security = [
            'redirect_uri' => 'https://openhealths.com'
          ];
         $removeKeyEmpty = removeEmptyKeys($this->legal_entity_form->toArray());

        $base64Data =  (new CipherApi())->sendSession(
             json_encode($removeKeyEmpty),
             $this->password,
             $this->keyContainerUpload,
             $this->knedp
         );

        $data = [
            'signed_legal_entity_request' =>    $base64Data,
//          'signed_legal_entity_request' =>       'MIIVlgYJKoZIhvcNAQcCoIIVhzCCFYMCAQExDjAMBgoqhiQCAQEBAQIBMIIHlAYJKoZIhvcNAQcBoIIHhQSCB4F7CgkiZWRycG91IjogIjMxMzk4MjE1NTkiLAoJInR5cGUiOiAiUFJJTUFSWV9DQVJFIiwKCSJyZXNpZGVuY2VfYWRkcmVzcyI6IHsKCQkidHlwZSI6ICJSRVNJREVOQ0UiLAoJCSJjb3VudHJ5IjogIlVBIiwKCQkiYXJlYSI6ICLQnC7QmtCY0IfQkiIsCgkJInNldHRsZW1lbnQiOiAi0JrQuNGX0LIiLAoJCSJzZXR0bGVtZW50X3R5cGUiOiAiQ0lUWSIsCgkJInNldHRsZW1lbnRfaWQiOiAiYWRhYTRhYmYtZjUzMC00NjFjLWJjYmYtYTBhYzIxMGQ5NTViIiwKCQkic3RyZWV0X3R5cGUiOiAiU1RSRUVUIiwKCQkic3RyZWV0IjogItCR0L7RgNC40YHQv9GW0LvRjNGB0YzQutCwIiwKCQkiYnVpbGRpbmciOiAiMjbQtyIsCgkJImFwYXJ0bWVudCI6ICIxMTIiLAoJCSJ6aXAiOiAiMDIwOTMiCgl9LAoJInBob25lcyI6IFsKCQl7CgkJCSJ0eXBlIjogIk1PQklMRSIsCgkJCSJudW1iZXIiOiAiKzM4MDUwNjQ5MTI0NCIKCQl9CgldLAoJImVtYWlsIjogInZpdGFsaXliZXpzaEBnbWFpbC5jb20iLAoJIndlYnNpdGUiOiAid3d3Lm9wZW5oZWFsdGhzLmNvbSIsCgkiYmVuZWZpY2lhcnkiOiAi0JHQtdC30YjQtdC50LrQviDQktGW0YLQsNC70ZbQuSDQk9GA0LjQs9C+0YDQvtCy0LjRhyIsCgkib3duZXIiOiB7CgkJImZpcnN0X25hbWUiOiAi0JLRltGC0LDQu9GW0LkiLAoJCSJsYXN0X25hbWUiOiAi0JHQtdC30YjQtdC50LrQviIsCgkJInNlY29uZF9uYW1lIjogItCT0YDQuNCz0L7RgNC+0LLQuNGHIiwKCQkidGF4X2lkIjogIjMxMzk4MjE1NTkiLAoJCSJub190YXhfaWQiOiBmYWxzZSwKCQkiYmlydGhfZGF0ZSI6ICIxOTg1LTEyLTE4IiwKCQkiZ2VuZGVyIjogIk1BTEUiLAoJCSJlbWFpbCI6ICJ2aXRhbGl5YmV6c2hAZ21haWwuY29tIiwKCQkiZG9jdW1lbnRzIjogWwoJCQl7CgkJCQkidHlwZSI6ICJQQVNTUE9SVCIsCgkJCQkibnVtYmVyIjogItCh0J45NTk5OTMiLAoJCQkJImlzc3VlZF9ieSI6ICLQlNC10YHQvdGP0L3RgdGM0LrQuNC8INCg0JIg0JPQoyDQnNCS0KEg0LIg0LzRltGB0YLRliDQmtC40ZTQstGWIiwKCQkJCSJpc3N1ZWRfYXQiOiAiMjAwMi0wMy0yOCIKCQkJfQoJCV0sCgkJInBob25lcyI6IFsKCQkJewoJCQkJInR5cGUiOiAiTU9CSUxFIiwKCQkJCSJudW1iZXIiOiAiKzM4MDUwNjQ5MTI0NCIKCQkJfQoJCV0sCgkJInBvc2l0aW9uIjogIlAyIgoJfSwKCSJhY2NyZWRpdGF0aW9uIjogewoJCSJjYXRlZ29yeSI6ICJTRUNPTkQiLAoJCSJpc3N1ZWRfZGF0ZSI6ICIyMDE3LTAyLTI4IiwKCQkiZXhwaXJ5X2RhdGUiOiAiMjAyNy0wMi0yOCIsCgkJIm9yZGVyX25vIjogImZkMTIzNDQzIiwKCQkib3JkZXJfZGF0ZSI6ICIyMDE3LTAyLTI4IgoJfSwKCSJsaWNlbnNlIjogewoJCSJ0eXBlIjogIk1TUCIsCgkJImxpY2Vuc2VfbnVtYmVyIjogImZkMTIzNDQzIiwKCQkiaXNzdWVkX2J5IjogItCa0LLQsNC70ZbRhNGW0LrQsNGG0LnQvdCwINC60L7QvNGW0YHRltGPIiwKCQkiaXNzdWVkX2RhdGUiOiAiMjAxNy0wMi0yOCIsCgkJImV4cGlyeV9kYXRlIjogIjIwMjctMDItMjgiLAoJCSJhY3RpdmVfZnJvbV9kYXRlIjogIjIwMTctMDItMjgiLAoJCSJ3aGF0X2xpY2Vuc2VkIjogItGA0LXQsNC70ZbQt9Cw0YbRltGPINC90LDRgNC60L7RgtC40YfQvdC40YUg0LfQsNGB0L7QsdGW0LIiLAoJCSJvcmRlcl9ubyI6ICLQktCQNDMyMzQiCgl9LAoJImFyY2hpdmUiOiBbCgkJewoJCQkiZGF0ZSI6ICIyMDE3LTAyLTI4IiwKCQkJInBsYWNlIjogItCy0YPQuy4g0JPR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',
            'signed_content_encoding' => 'base64',
        ];

        $request = LegalEntitiesRequestApi::_createOrUpdate($data);

        if (!empty($request) ){
            $this->saveLegalEntityFromExistingData($request['data']);
            $this->legalEntity->client_secret = $request['urgent']['security']['client_secret'] ?? '';
            $this->legalEntity->client_id = $request['urgent']['security']['client_id'] ?? null;
            $this->legalEntity->save();
            $this->createUser();
            Cache::forget($this->entityCacheKey);
            Cache::forget($this->ownerCacheKey);
            $this->redirect('/legal-entities/edit');
        }

    }

    public function createUser()
    {
        $user = Auth::user();
        $user->legalEntity()->associate($this->legalEntity);
        $user->save();

        // Check if the authenticated user's email matches the owner's email
        if ($user->email === $this->legal_entity_form->owner['email']) {
            $user->assignRole('Owner');
            return $user; // Return the authenticated user if the update is done
        }

        // Create a new user if the email does not match
        $user = User::create([
            'email' => $this->legal_entity_form->owner['email'] ?? null,
            'password' => Hash::make(Str::random(10)),
        ]);

        $user->legalEntity()->associate($this->legalEntity);
        $user->save(); // Save the new user with the association
        $user->assignRole('Owner');

        return $user;
    }

    public function fetchDataFromAddressesComponent():void
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
