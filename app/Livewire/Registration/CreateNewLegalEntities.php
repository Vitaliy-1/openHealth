<?php

namespace App\Livewire\Registration;

use App\Models\Person;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Validator;
class CreateNewLegalEntities extends Component
{


    public array $form = [
        'edrpou' => '',
        'owner'=>[
            'email' => '',
            'last_name' => '',
            'first_name ' => '',
            'second_name' => '',
            'gender' => '',
            'birth_date' => '',
            'invalid_tax_id' => '',
            'tax_id' => '',
            'position' => '',
            'documents' => [],
            'phones' => [
                [ 'type' => '', 'phone'=> ''],
            ],
        ],


    ];


    public int $totalSteps = 4;

    public int $currentStep = 1;


    public function mount(){
        $this->currentStep = 1;
    }

    public function addRowPhones()
    {
        $this->form['owner']['phones'][] = ['type' => '', 'phone' => ''];
    }

    public function removeRowPhones($key)
    {
        unset($this->form['owner']['phones'][$key]) ;
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
    public function validateData(){

        if($this->currentStep == 1){
            $this->validate([
                'form.edrpou'=>'required|integer|digits_between:8,10',
            ]);
        }
        elseif($this->currentStep == 2){
            $this->validate([
                'form.owner.last_name' => 'required|min:3',
                'form.owner.first_name' => 'required|min:3',
                'form.owner.gender' => 'required|string',
                'form.owner.birth_date' => 'required|date',
                'form.owner.invalid_tax_id' => 'required|boolean',
                'form.owner.tax_id' => 'exclude_if:form.invalid_tax_id,false|required|string',
                'form.owner.documents.type' => 'exclude_if:form.invalid_tax_id,false|required|string',
                'form.owner.documents.number' => 'exclude_if:form.invalid_tax_id,false|required|string',
                'form.owner.documents.issued_at' => 'exclude_if:form.invalid_tax_id,false|required|string',
                'form.owner.phones.*.phone' => 'required|string:digits:13',
                'form.owner.phones.*.type' => 'required|string',
                'form.owner.email' => 'required|email',
                'form.owner.position' => 'required|string',
            ]);


        }
        elseif($this->currentStep == 3){
            $this->validate([

            ]);
        }
        elseif($this->currentStep == 4){
            $this->validate([
            ]);
        }
    }

    public function register(){
        ///Create/Update Legal Entity V2
    }

    public function render()
    {
        return view('livewire.registration.create-new-legal-entities');
    }
}
