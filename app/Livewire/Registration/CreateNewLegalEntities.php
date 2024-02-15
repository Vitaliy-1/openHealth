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
    protected ?array $entityCache;
    protected string $ownerCacheKey;
    protected ?array $ownerCache;

    protected $listeners = ['addressDataFetched'];

    protected string $edrpouKey = '54323454';

    public ?array $addresses = [];

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
        //Check if the user has a legal entity
        if (\auth()->user()->legalEntity) {
            // Get Legal Entity from the user
            $user = \auth()->user();
            $this->legalEntity = $user->legalEntity;
        } // Search Legal entity in the cache by user ID
        elseif (Cache::has($this->entityCacheKey)) {
            $this->legalEntity = Cache::get($this->entityCacheKey);
            $this->legal_entity_form->fill($this->legalEntity->toArray());
        } else {
            // Create a new Legal Entity
            $this->legalEntity = new LegalEntity();
        }
        // Search Legal entity in the cache by user ID
        if (Cache::has($this->ownerCacheKey)) {
            $this->legal_entity_form->owner = Cache::get($this->ownerCacheKey);
        }

        foreach ($this->legal_entity_form as $index => $field) {
            if (empty($this->legal_entity_form->{$index})) {
                $this->currentStep++;
                return;
            }
        }

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
            $this->legalEntity = (new LegalEntity())->fill($normalizedData);
            $this->legal_entity_form->fillData($this->legalEntity);
            $this->legalEntity->save();
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

    public function saveLegalEntity(): void
    {
        $this->legalEntity->save();
    }
    // Step  1
    public function stepEdrpou(): void
    {
        $this->legal_entity_form->rulesForEdrpou();
        $data = (new LegalEntitiesRequestApi())->get($this->legal_entity_form->edrpou);
        if ($this->edrpouKey == $this->legal_entity_form->edrpou && !empty($data)) {
            $this->saveLegalEntityFromExistingData($data);
        } else {
            $this->putLegalEntityInCache();
        }
    }

    // Step  2
    public function stepOwner(): void
    {
        $this->legal_entity_form->rulesForOwner();

        $personData = $this->legal_entity_form->owner;

        Cache::put($this->ownerCacheKey, $personData, now()->days(90));

        if (isset($this->legalEntity->phones) && !empty($this->legalEntity->phones)) {
            $this->phones = $this->legalEntity->phones;
        }

    }

    // Step  3
    public function stepContact(): void
    {
        $this->legal_entity_form->rulesForContact();
    }
    // Step  4
    public function stepAddress(): bool
    {
        $this->fetchDataFromAddressesComponent();
        return true;
    }

    // Step  5
    public function stepAccreditation(): void
    {

        $this->legalEntity->update(['accreditation' => $this->legal_entity_form->accreditation ?? '']);
    }

    // Step  6
    public function stepLicense(): void
    {
        $this->legal_entity_form->rulesForLicense();

        $this->legalEntity->update(['license' => $this->legal_entity_form->license ?? '']);
    }

    // Step  7
    public function stepAdditionalInformation(): void
    {
        $this->legalEntity->update([
            'archive' => $this->legal_entity_form->additional_information ?? '',
            'beneficiary' => $this->legal_entity_form->additional_information['beneficiary'] ?? '',
            'receiver_funds_code' => $this->legal_entity_form->additional_information['receiver_funds_code'] ?? '',
        ]);
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
