<?php

namespace App\Livewire\LegalEntity;

use App\Classes\Cipher\Api\CipherApi;
use App\Helpers\JsonHelper;
use App\Livewire\LegalEntity\Forms\LegalEntitiesForms;
use App\Livewire\LegalEntity\Forms\LegalEntitiesRequestApi;
use App\Models\Employee;
use App\Models\LegalEntity;
use App\Models\Person;
use App\Traits\FormTrait;
use Illuminate\Support\Facades\Auth;
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
    protected string $edrpouKey = '54323454';

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
            'property' => 'addresses',
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


    #[Validate('required|string|max:255')]
    public string $knedp = '';

    #[Validate('required|max:1024')] // 1MB Max
    public   $keyContainerUpload;

    #[Validate('required|string|max:255')]
    public string $password = '';

    protected $rules = [
        'knedp' => 'required|string|max:255',
    ];


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
            'ACCREDITATION_CATEGORY'
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
            if (!empty($this->legal_entity_form->{$field})) {
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
                        $normalizedData['addresses'] = $value;
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

        $data = LegalEntitiesRequestApi::getLegalEntitie($this->legal_entity_form->edrpou);

        if ($this->edrpouKey == $this->legal_entity_form->edrpou && !empty($data)) {
            $this->saveLegalEntityFromExistingData($data);
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
        $this->legal_entity_form->addresses = [];

        $this->fetchDataFromAddressesComponent();

        if (empty($this->addresses)) {
            throw ValidationException::withMessages(
                ['area' => 'The addresses field is required.']);
        }

        $this->legal_entity_form->addresses = $this->addresses;
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

//         $this->validate();
         $base64Data =  (new CipherApi())->sendSession(
             json_encode($this->legal_entity_form->toArray()),
             $this->password,
             $this->convertFileToBase64(),
             $this->knedp
         );
        dd($base64Data);
        $data = [
            'signed_legal_entity_request' => '1',
            'signed_content_encoding' => '2',
        ];

        $request = LegalEntitiesRequestApi::_createOrUpdate($data);
        if (!empty($request) ){
            $this->saveLegalEntityFromExistingData($request);
            $this->legalEntity->fill($request);
            $this->legalEntity->save();
            $user = Auth::user();
            $user->legalEntity()->associate($this->legalEntity);
            $user->save();
            Cache::forget($this->entityCacheKey);
            Cache::forget($this->ownerCacheKey);
            $user->assignRole('Owner');
            $this->redirect('/legal-entities/edit');
        }
    }

    public function fetchDataFromAddressesComponent():void
    {
       $this->dispatch('fetchAddressData');
    }

    public function setAddressesFields()
    {
        $this->dispatch('setAddressesFields', $this->legal_entity_form->addresses ?? []);
    }

    public function addressDataFetched($addressData): void
    {
        $this->addresses = $addressData;

    }
    public function convertFileToBase64(): ?string
    {
        if ($this->keyContainerUpload && $this->keyContainerUpload->exists()) {
            $filePath = $this->keyContainerUpload->storeAs('uploads/kep/', 'kep.ppx', 'public');

            if ($filePath) {
                $fileContents = file_get_contents(storage_path('app/public/' . $filePath));
                if ($fileContents !== false) {
                    $base64Content = base64_encode($fileContents);

                    \Storage::disk('public')->delete($filePath);
                    return $base64Content;
                }
            }
        }

        return null;
    }
    public function render()
    {
        return view('livewire.legal-entity.create-new-legal-entities');
    }
}
