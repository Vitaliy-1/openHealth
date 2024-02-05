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
    public LegalEntitiesForms $legal_entity_form;

    public LegalEntity $legalEntity;

    public Person $person;

    public Employee $employee;

    public int $totalSteps = 8;

    public int $currentStep = 1;

    public array $dictionaries;

    public ?array $phones = [];

    public ?object $koatuu_level1;

    public ?object $koatuu_level2;

    public ?object $koatuu_level3;

    /**
     * @var string The Cache ID to store Legal Entity being filled by the current user
     */
    protected string $entityCacheKey;

    public function boot(): void
    {
        $this->entityCacheKey = Auth::user()->id . '-' . LegalEntity::class;
    }

    public function mount(Employee $employee, LegalEntity $legalEntity, Person $person): void
    {
        /**
         * Legal Entity associated with the form
         * To get Legal Entity from a user, use: Auth::user()->person->employee->legalEntity;
         */

         // Search Legal entity in the cache by user ID
        if (Cache::has($this->entityCacheKey)) {
            $this->legalEntity = Cache::get($this->entityCacheKey);
            // Prefill form data as it already exists
            $this->legal_entity_form->fillData($this->legalEntity);
            if (!empty($this->legalEntity->edrpou)) {
                $this->currentStep = 2;
            }
        } else {
            $this->legalEntity = $legalEntity;
        }

        $this->person = $person;

        $this->employee = $employee;

        $this->koatuu_level1 = KoatuuLevel1::all();

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

    public function addRowPhone(): array
    {
        return $this->phones[] = ['type' => '', 'number' => ''];
    }

    public function removePhone($key): void
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

    public function saveLegalEntityFromExistingData(array $data): void
    {
        $normalizedData = [];
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
    }

    private function updatePersonAndEmployee($person, $personData): void
    {
        $person->update($personData);
        if ($person->employee) {
            $person->employee->update($personData);
        } else {
            $this->createEmployee($person, $personData);
        }
    }

    private function createPersonAndEmployee($user, $personData): void
    {
        $person = Person::create($personData);
        $user->person()->associate($person)->save();
        $this->createEmployee($person, $personData);
    }

    private function createEmployee($person, $personData): void
    {
        $employee = $this->legalEntity->employee()->create($personData);
        $employee->person()->associate($person)->save();
    }

    public function stepEdrpou(): array
    {
        $this->legal_entity_form->rulesForEdrpou();

        $data = (new LegalEntitiesRequestApi())->get($this->legal_entity_form->edrpou);
        empty($data) ?
            $this->legalEntity = (new LegalEntity())->fill(['edrpou' => $this->legal_entity_form->edrpou]) :
            $this->saveLegalEntityFromExistingData($data);

        return [];
    }

    public function stepOwner(): void
    {
        $this->legal_entity_form->rulesForOwner();
        //Get user
        $user = Auth::user();
        //Get person data builder
        $personData = $this->legal_entity_form->owner;

        DB::transaction(function () use ($personData, $user) {
            if ($user->person) {
                $this->updatePersonAndEmployee($user->person, $personData);
            } else {
                $this->createPersonAndEmployee($user, $personData);
            }
        });

        if (isset($this->legalEntity->phones) && !empty($this->legalEntity->phones) ) {
            $this->phones = $this->legalEntity->phones;
        }
    }

    public function stepContact(): void
    {
        $this->legal_entity_form->rulesForContact();
        $this->legalEntity->update(
            [
                'email'=>$this->legal_entity_form->contact['email'] ?? '',
                'website'=>$this->legal_entity_form->contact['website'] ?? '',
                'phones'=>$this->legal_entity_form->contact['phones'] ?? '',
            ]);
    }

    public function stepAccreditation(): void
    {
        $this->legalEntity->update(['accreditation'=>$this->legal_entity_form->accreditation ?? '']);
    }

    public function stepAddress(): void
    {
        $this->legal_entity_form->rulesForAddress();
        $this->legalEntity->update(['address'=>$this->legal_entity_form->residence_address ?? '']);
    }

    public function stepLicense(): void
    {
        $this->legal_entity_form->rulesForLicense();

        $this->legalEntity->update(['license'=>$this->legal_entity_form->license ?? '']);
    }

    public function stepAdditionalInformation(): void
    {
        $this->legalEntity->update([
            'archive'=>$this->legal_entity_form->additional_information ?? '',
            'beneficiary'=>$this->legal_entity_form->additional_information['beneficiary'] ?? '',
            'receiver_funds_code'=>$this->legal_entity_form->additional_information['receiver_funds_code'] ?? '',
        ]);
    }

    public function stepPublicOffer(): void
    {
        $this->legal_entity_form->rulesForPublicOffer();
    }

    public function setField($property,$key, $value)
    {
        $this->legal_entity_form->$property[$key] = $value;
    }

    public function searchKoatuuLevel2(): void
    {
        $area = $this->legal_entity_form->residence_address['area'] ?? '';

        $region = $this->legal_entity_form->residence_address['region'] ?? '';

        if (empty($area) && strlen($region) <= 1) {
            return;
        }

        $this->koatuu_level2 = $this->koatuu_level1
            ->where('name',$area)
            ->first()
            ->koatuu_level2()
            ->where('name', 'ilike', '%' . $region. '%')
            ->take(5)->get();
    }

    public function searchKoatuuLevel3()
    {
        $area_id = $this->legal_entity_form->residence_address['area'] ?? '';

        $region = $this->legal_entity_form->residence_address['region'] ?? '';

        $settlement = $this->legal_entity_form->residence_address['settlement'] ?? '';

        if (empty($area) && empty($area_id) && strlen($settlement) <= 1) {
            return false;
        }

        $this->koatuu_level3 = $this->koatuu_level2
            ->find($region)
            ->koatuu_level3()
            ->where('name', 'ilike', '%' . $settlement. '%')
            ->take(5)->get();
    }

    public function dehydrateLegalEntity()
    {
        Cache::put($this->entityCacheKey, $this->legalEntity, now()->addDays(90));
    }

    public function render()
    {
        return view('livewire.registration.create-new-legal-entities');
    }
}
