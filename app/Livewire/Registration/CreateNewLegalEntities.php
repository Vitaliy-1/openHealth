<?php

namespace App\Livewire\Registration;

use App\Helpers\JsonHelper;
use App\Livewire\Registration\Forms\LegalEntitiesForms;
use App\Livewire\Registration\Forms\LegalEntitiesRequestApi;
use App\Models\Employee;
use App\Models\Koatuu\KoatuuLevel1;
use App\Models\LegalEntity;
use App\Models\Person;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class CreateNewLegalEntities extends Component
{
    const CACHE_PREFIX = 'register_legal_entity_form';

    public LegalEntitiesForms $legal_entity_form;

    public LegalEntity $legalEntity;

    public Person $person;

    public Employee $employee;


    public int $totalSteps = 8;

    public int $currentStep = 1;

    public array $dictionaries;

    public ?array $phones = [];

    /**
     * @var string The Cache ID to store Legal Entity being filled by the current user
     */
    protected string $entityCacheKey;
    protected string $ownerCacheKey;

    protected $listeners = ['addressDataFetched'];
    protected string $edrpouKey = '54323454';

    public ?array $addresses = [];

    public array $steps = [
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
            'property'=> 'phones',
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

    public function boot(): void
    {
        $this->entityCacheKey = self::CACHE_PREFIX . '-' . Auth::id() . '-' . LegalEntity::class;
        $this->ownerCacheKey = self::CACHE_PREFIX . '-' . Auth::id() . '-' . Employee::class;
    }

    public function mount(): void
    {

        $this->getLegalEntity();

        $this->dictionaries = JsonHelper::searchValue('DICTIONARIES_PATH', [
            'PHONE_TYPE',
            'POSITION',
            'LICENSE_TYPE',
            'SETTLEMENT_TYPE',
            'GENDER',
            'SPECIALITY_LEVEL',
            'ACCREDITATION_CATEGORY'
        ]);

        $this->getPhones();

    }

    public function getLegalEntity(): void
    {

         // Search Legal entity in the cache by user ID
        if (Cache::has($this->entityCacheKey)) {
            $this->legalEntity = Cache::get($this->entityCacheKey);
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

    public function removePhone($key)
    {
        if (isset($this->phones[$key])) {
            unset($this->phones[$key]);
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

    public function stepFields(): void
    {
        foreach ($this->steps as $field => $step) {
            if (empty($this->legal_entity_form->{$field})) {
                $this->currentStep = $step['step'];
                break;
            }
        }
    }

    public function changeStep(int $step, string $property): void
    {
        if (empty($this->legal_entity_form->{$property})){
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
    public function validateData()
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

    public function getPhones()
    {
        if (empty($this->phones)) {
            return $this->addRowPhone();
        }
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

            $this->legal_entity_form->fill($normalizedData);

            $this->putLegalEntityInCache();
        }
    }

    public function putLegalEntityInCache(): void
    {
        // Fill the Legal Entity with the form data
        $this->legalEntity->fill($this->legal_entity_form->toArray());
        // Check if the Legal Entity has changed cache
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
            // If the Legal Entity has not changed, return false
            if (empty(array_diff_assoc($this->legal_entity_form->owner,
                Cache::get($this->ownerCacheKey)))) {
                return false; // If
            }
        }
        return true; // Return true if the Legal Entity has changed
    }
    public function checkEdrpouChange(){
         if (Cache::has($this->entityCacheKey)
             && $this->legal_entity_form->edrpou != Cache::get($this->entityCacheKey)->edrpou)
             Cache::forget($this->entityCacheKey);
             Cache::forget($this->ownerCacheKey);
    }

    public function saveLegalEntity(): void
    {
        $this->legalEntity->save();
    }

    // #Step  1 Request to Ehealth API
    public function stepEdrpou(): void
    {
        $this->legal_entity_form->rulesForEdrpou();

        $this->checkEdrpouChange();

        $data = (new LegalEntitiesRequestApi())->get($this->legal_entity_form->edrpou);

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
    public function stepAddress(): bool
    {
        $this->fetchDataFromAddressesComponent();

        return true;
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
        $this->legal_entity_form->rulesForPublicOffer();
    }

    public function setField($property, $key, $value)
    {
        $this->legal_entity_form->$property[$key] = $value;
    }

    public function setAddressesFields()
    {
        $this->dispatch('setAddressesFields', $this->legal_entity_form->addresses ?? []);
    }

    public function fetchDataFromAddressesComponent()
    {
        $this->dispatch('fetchAddressData');
    }

    public function addressDataFetched($addressData): void
    {
        $this->addresses = $addressData;
    }

    public function render()
    {
        return view('livewire.registration.create-new-legal-entities');
    }
}
