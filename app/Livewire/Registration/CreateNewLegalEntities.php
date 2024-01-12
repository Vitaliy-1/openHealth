<?php

namespace App\Livewire\Registration;

use App\Helpers\JsonHelper;
use App\Http\Controllers\Ajax\AjaxController;
use App\Livewire\Registration\Forms\LegalEntitiesFormBuilder;
use App\Livewire\Registration\Forms\LegalEntitiesForms;
use App\Livewire\Registration\Forms\LegalEntitiesRequestApi;
use App\Models\Employee;
use App\Models\LegalEntities;
use App\Models\Person;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CreateNewLegalEntities extends Component
{

    public LegalEntitiesForms $legal_entities;

    public object $legal_entity_owner;

    public int $totalSteps = 8;

    public int $currentStep = 1;

    public array $dictionaries;

    public ?array $phones = [];

    public ?array $formBuilder;

    public ?array $koatuu_level1;

    public ?array $koatuu_level2;

    public ?array $koatuu_level3;
    public function mount()
    {
        $this->getPhones();

        $this->koatuu_level1 = DB::table('koatuu_level1')->get()
            ->toArray();

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
        dd($this->legal_entities);
    }

    public function getPhones()
    {
        if (empty($this->phones)) {
            return $this->addRowPhone();
        }
    }

    public function stepEdrpou(): array|object
    {

        $this->legal_entities->rulesForEdrpou();

        //Get legal entity data builder
        $this->formBuilder = ( new LegalEntitiesRequestApi())->get($this->legal_entities->edrpou);

        //Builder Save legal entity
        $data = LegalEntitiesFormBuilder::saveBuilderLegalEntity($this->formBuilder);



//        dd($this->legal_entities->residence_address);
        //Create new legal entity
        if ($data) {
            return $this->legal_entity_owner = LegalEntities::saveOrUpdate(
                ['legal_entities_uuid' => $data['legal_entities_uuid']],
                $data
            );
        }
        return [];
    }

    public function stepOwner(): void
    {

        $this->legal_entities->rulesForOwner();


        //Get user
        $user = Auth::user();

        //Get person data builder
        $personData = LegalEntitiesFormBuilder::saveBuilderPerson($this->legal_entities->owner);

        //Update person and employee
        if ($user->person){

            //Update person
            $person = tap($user->person)->update($personData);
            //Update employee
            $person->employee->update($this->legal_entities->owner);
        }
        //Create new person and employee
        else{
            DB::transaction(function () use ($personData, $user) {
                // Step 1: Create a new person
                $person = Person::create($personData);

                // Step 2: Associate the person with the user
                $user->person()->associate($person);

                $user->save();
                // Step 3: Create a new employee
                $employee = new Employee($personData);

                // Step 4: Associate the employee with the legal entity
                $this->legal_entity_owner->employee()->save($employee);

                // Step 5: Associate the person with the employee
                $employee->person()->associate($person);

                $employee->save();
            });
        }

        $this->legal_entities->contact = LegalEntitiesFormBuilder::getBuilderContact($this->formBuilder);


        if (isset($this->formBuilder['phones']) && !empty($this->formBuilder['phones']) ) {
            $this->phones = $this->formBuilder['phones'];
        }

    }

    public function stepContact(): void
    {
        $this->legal_entities->rulesForContact();

        $this->legal_entities->residence_address = LegalEntitiesFormBuilder::getBuilderRegionAddress($this->formBuilder);

    }

    public function stepAccreditation(): void
    {
        $this->legal_entities->license = LegalEntitiesFormBuilder::getBuilderLicense($this->formBuilder);
    }

    public function stepAddress(): void
    {
        $this->legal_entities->rulesForAddress();

        $this->legal_entities->accreditation = LegalEntitiesFormBuilder::getBuilderAccreditation($this->formBuilder);
    }

    public function stepLicense(): void
    {
        $this->legal_entities->rulesForLicense();

        $this->legal_entities->additional_information = LegalEntitiesFormBuilder::getBuilderAdditionalInformation($this->formBuilder);

    }

    public function stepAdditionalInformation()
    {

    }

    public function stepPublicOffer(): void
    {
        $this->legal_entities->rulesForPublicOffer();
    }

    public function setField($property,$key, $value)
    {
        $this->legal_entities->$property[$key] = $value;
    }

    public function searchKoatuuLevel2()
    {
        $area_id = $this->legal_entities->residence_address['area'] ?? '';

        $region = $this->legal_entities->residence_address['region'] ?? '';

        if (empty($area) && strlen($region) <= 1) {
            return;
        }

        $this->koatuu_level2 = DB::table('koatuu_level2')
            ->where('koatuu_level1_id',$area_id)
            ->where('name', 'ilike', '%' . $region. '%')
            ->take(5)->get()->toArray();

    }

    public function searchKoatuuLevel3()
    {
        $area_id = $this->legal_entities->residence_address['area'] ?? '';

        $region = $this->legal_entities->residence_address['region'] ?? '';

        $settlement = $this->legal_entities->residence_address['settlement'] ?? '';

        if (empty($area) && empty($area_id) && strlen($settlement) <= 1) {
            return false;
        }

        $this->koatuu_level3 = DB::table('koatuu_level3')
            ->where('koatuu_level1_id',$area_id)
            ->where('koatuu_level2_id',$region)
            ->where('name', 'ilike', '%' . $settlement. '%')
            ->take(5)->get()->toArray();
    }


    public function render()
    {
        return view('livewire.registration.create-new-legal-entities');
    }

}
