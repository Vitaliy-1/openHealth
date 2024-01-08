<?php

namespace App\Livewire\Registration;

use App\Helpers\JsonHelper;
use App\Livewire\Registration\Forms\LegalEntitiesFormBuilder;
use App\Livewire\Registration\Forms\LegalEntitiesForms;
use App\Livewire\Registration\Forms\LegalEntitiesRequestApi;
use App\Models\LegalEntities;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CreateNewLegalEntities extends Component
{

    public LegalEntitiesForms $legal_entities;

    public int $totalSteps = 8;

    public int $currentStep = 1;

    public array $dictionaries;

    public ?array $phones = [];

    public ?array $formBuilder;

    public function mount()
    {
        $this->getPhones();
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


        $this->formBuilder = ( new LegalEntitiesRequestApi())->get($this->legal_entities->edrpou);

        $data = LegalEntitiesFormBuilder::saveBuilderLegalEntity($this->formBuilder);

        if ($data) {
            return LegalEntities::saveOrUpdate(
                ['legal_entities_uuid' => $data['legal_entities_uuid']],
                $data
            );
        }

        return [];
    }

    public function stepOwner(): void
    {
        $this->legal_entities->rulesForOwner();

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

    public function render()
    {
        return view('livewire.registration.create-new-legal-entities');
    }

}
