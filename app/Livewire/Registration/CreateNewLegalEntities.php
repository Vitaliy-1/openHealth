<?php

namespace App\Livewire\Registration;

use AllowDynamicProperties;
use App\Helpers\JsonHelper;
use App\Models\Person;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Validate;

use Illuminate\Support\Facades\Validator;
 class CreateNewLegalEntities extends Component
{


    public array $form = [
        'edrpou' => '',
        'type'=>'PRIMARY_CARE',
        'email'=> '',
        'website'=>'',
        'phones'=> [
            [ 'type' => '', 'phone'=> ''],
        ],
        'owner'=> [
            'email' => '',
            'last_name' => '',
            'first_name ' => '',
            'second_name' => '',
            'gender' => '',
            'birth_date' => '',
            'invalid_tax_id' => false,
            'tax_id' => '',
            'position' => '',
            'documents' => [],
            'phones' => [
                [ 'type' => '', 'phone'=> ''],
            ],
        ],
        'residence_address'=>[
            'type'=>'RESIDENCE',
            'country'=>'UA',
            'area'=>'',
            'region'=>'',
            'settlement'=>'',
            'settlement_type'=>'',
            'settlement_id'=> ''
        ],
        'accreditation'=>[
            'category'=>'',
            'issued_date'=>'',
            'expiry_date'=>'',
            'order_no'=>'',
            'order_date'=>'',
        ],
        'license'=> [
            'type'=>'MSP',
            'issued_by'=>'',
            'issued_at'=>'',
            'active_from_date'=>'',
            'order_no'=>'',
            'license_number'=>'',
            'expiry_date'=>'',
            'what_licensed'=>''
        ]
    ];

    public int $totalSteps = 7;

    public int $currentStep;
    public array $dictionaries;

    public function mount(){
        $this->currentStep = 1;


        $this->dictionaries = JsonHelper::searchValue('DICTIONARIES_PATH', [
                'PHONE_TYPE',
                'LICENSE_TYPE'
            ]
        );
    }

    public function addRowPhones(&$phonesArray)
    {
        $phonesArray[] = ['type' => '', 'phone' => ''];
    }

    public function addRowPhonesForOwner()
    {
        $this->addRowPhones($this->form['owner']['phones']);
    }

    public function addRowPhonesForGeneral()
    {
        $this->addRowPhones($this->form['phones']);
    }

    public function removePhonesForOwner($key)
    {
        if (isset($this->form['owner']['phones'][$key])) {
            unset($this->form['owner']['phones'][$key]);
        }
    }

    public function removePhonesForGeneral($key)
    {
        if (isset($this->form['phones'][$key])) {
            unset($this->form['phones'][$key]);
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
                'form.owner.tax_id' => 'exclude_if:form.owner.invalid_tax_id,true|required|string',
                'form.owner.documents.type' => 'exclude_if:form.owner.invalid_tax_id,true|required|string',
                'form.owner.documents.number' => 'exclude_if:form.owner.invalid_tax_id,true|required|string',
                'form.owner.documents.issued_at' => 'exclude_if:form.owner.invalid_tax_id,true|required|string',
                'form.owner.phones.*.phone' => 'required|string:digits:13',
                'form.owner.phones.*.type' => 'required|string',
                'form.owner.email' => 'required|email',
                'form.owner.position' => 'required|string',
            ]);
        }
        elseif($this->currentStep == 3){
            $this->validate([
                'form.email' => 'required|email',
                'form.phones.*.phone' => 'required|string:digits:13',
                'form.phones.*.type' => 'required|string',
            ]);
        }
        elseif($this->currentStep == 4){
            $this->validate([
                'form.residence_address.region'=> 'required|string|min:3',
                'form.residence_address.area' => 'required|string|min:3',
                'form.residence_address.settlement' => 'required|string|min:3',
                'form.residence_address.settlement_type' => 'required|string|min:3',
            ]);
        }


        elseif($this->currentStep == 6){
            $this->validate([
                'form.license.issued_by'=> 'required|string|min:3',
                'form.license.issued_date'=> 'required|date|min:3',
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
