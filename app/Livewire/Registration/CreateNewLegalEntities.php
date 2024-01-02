<?php

namespace App\Livewire\Registration;

use AllowDynamicProperties;
use App\Helpers\JsonHelper;
use App\Livewire\Registration\Forms\LegalEntitiesForms;
use App\Models\Person;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Attributes\Validate;

use Illuminate\Support\Facades\Validator;
 class CreateNewLegalEntities extends Component
 {

     public LegalEntitiesForms $legal_entities;

     public int $totalSteps = 8;


     public int $currentStep = 1;
     public array $dictionaries;

     public ?array $phones = [];

     public function mount(){
         $this->getPhones();
         $this->dictionaries = JsonHelper::searchValue('DICTIONARIES_PATH', [
             'PHONE_TYPE',
             'LICENSE_TYPE',
             'GENDER',
             'SPECIALITY_LEVEL'
         ]);
    }

    public function addRowPhone()
    {
        $this->phones[] = ['type' => '', 'phone' => ''];
    }
    public function removePhone($key)
    {
        if (isset($this->phones[$key])) {
            unset($this->phones[$key]);
        }
    }

     public function increaseStep(){

        $this->resetErrorBag();
        $this->validateData();
        $this->currentStep++;

        if($this->currentStep > $this->totalSteps){
            $this->currentStep = $this->totalSteps;
        }
    }

    public function decreaseStep(){
        $this->resetErrorBag();
        $this->currentStep--;
        if($this->currentStep < 1){
            $this->currentStep = 1;
        }
    }

     /**
      * @throws ValidationException
      */
     public function validateData(){
         return match ($this->currentStep) {
             1 => $this->legal_entities->getRulesForEdrpou(),
             2 => $this->legal_entities->getRulesForOwner(),
             3 => $this->legal_entities->getRulesForContact(),
             4 => $this->legal_entities->getRulesForAddress(),
             6 => $this->legal_entities->getRulesForLicense(),
             8 => $this->legal_entities->getRulesForPublicOffer(),
             default => [],
         };
    }


    public function register(){
        ///Create/Update Legal Entity V2
    }


    public function getPhones(): void
    {
        if (empty($this->phones)){
              $this->addRowPhone();
        }

    }


    public function render()
    {
        return view('livewire.registration.create-new-legal-entities');
    }
}
