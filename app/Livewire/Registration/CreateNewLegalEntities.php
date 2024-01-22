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
    public function mount(Employee $employee, LegalEntity $legalEntity, Person $person)
    {

        //Get legal entity by Auth user
//        Auth::user()->person->employee->legalEntity;

        $this->legalEntity = $legalEntity;

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

    public function removePhone($key)
    {
        if (isset($this->phones[$key])) {
            unset($this->phones[$key]);
        }
    }

    public function increaseStep()
    {

        $this->resetErrorBag();

        $this->validateData();

        $this->currentStep++;

        if ($this->currentStep > $this->totalSteps) {
            $this->currentStep = $this->totalSteps;
        }
    }

    public function decreaseStep()
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




    public function saveLegalEntity($data)
    {
        $this->legalEntity = LegalEntity::firstOrNew(['uuid' => $data['id'] ?? '']);

        $this->legalEntity->setAttribute('uuid', $data['id'] ?? '');
        $this->legalEntity->setAttribute('addresses',$data['residence_address'] ?? []);
        $this->legalEntity->setAttribute('kveds', $data['edr']['kveds'] ?? []);
        $this->legalEntity->setAttribute('legal_form', $data['edr']['legal_form'] ?? '');
        $this->legalEntity->setAttribute('name', $data['edr']['name'] ?? '');
        $this->legalEntity->setAttribute('short_name', $data['edr']['short_name'] ?? '');
        $this->legalEntity->setAttribute('public_name', $data['edr']['public_name'] ?? '');
        $this->legalEntity->fill($data);
        $this->legalEntity->save();
        $this->legal_entity_form->fillData($this->legalEntity);

    }

    private function updatePersonAndEmployee($person, $personData)
    {
        $person->update($personData);
        if ($person->employee) {
            $person->employee->update($personData);
        } else {
            $this->createEmployee($person, $personData);
        }
    }

    private function createPersonAndEmployee($user, $personData)
    {
        $person = Person::create($personData);
        $user->person()->associate($person)->save();
        $this->createEmployee($person, $personData);
    }

    private function createEmployee($person, $personData)
    {
        $employee = $this->legalEntity->employee()->create($personData);
        $employee->person()->associate($person)->save();
    }


    public function stepEdrpou(): array|object
    {

        $this->legal_entity_form->rulesForEdrpou();

        $data = (new LegalEntitiesRequestApi())->get($this->legal_entity_form->edrpou);

        if (empty($data)) {
            return [];
        }

        $this->saveLegalEntity($data);

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
        dd($this->legal_entity_form);
        }

    public function stepAdditionalInformation()
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

    public function searchKoatuuLevel2()
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

    public function render()
    {
        return view('livewire.registration.create-new-legal-entities');
    }

}
