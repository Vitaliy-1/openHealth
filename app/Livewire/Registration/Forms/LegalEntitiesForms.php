<?php

namespace App\Livewire\Registration\Forms;

use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LegalEntitiesForms extends Form
{
    #[Validate('required|integer|digits:8')]
    public string $edrpou = '';

    #[Validate([
        'owner.last_name' => 'required|min:3',
        'owner.first_name' => 'required|min:3',
        'owner.gender' => 'required|string',
        'owner.birth_date' => 'required|date',
        'owner.no_tax_id' => 'boolean',
        'owner.tax_id' => 'exclude_if:owner.no_tax_id,false|required|string',
        'owner.documents.type' => 'exclude_if:owner.no_tax_id,true|required|string',
        'owner.documents.number' => 'exclude_if:owner.no_tax_id,true|required|string',
        'owner.phones.*.phone' => 'required|string:digits:13',
        'owner.phones.*.type' => 'required|string',
        'owner.email' => 'required|email',
        'owner.position' => 'required|string',
    ])]

    public ?array $owner= ['no_tax_id' => false];

    #[Validate([
        'contact.phones.*.phone' => 'required|string:digits:13',
        'contact.phones.*.type' => 'required|string',
        'contact.email' => 'required|email',
    ])]
    public ?array $contact = [];

    #[Validate([
//        'residence_address.country' => 'required|string|min:3',//TODO: validate country? default UA
        'residence_address.region' => 'required|string|min:3',
        'residence_address.area' => 'required|string|min:3',
        'residence_address.settlement' => 'required|string|min:3',
        'residence_address.settlement_type' => 'required|string|min:3',
    ])]
    public ?array $residence_address = [];

    //TODO: validate acrreditation.category ?

    public ?array $accreditation = [];

    #[Validate([
        //TODO: validate license exclude_if: license.id
//        'license.category' => 'required|string|min:3',//TODO: validate license.category
        'license.issued_by' => 'required|string|min:3',
        'license.issued_date' => 'required|date|min:3',
        'license.active_from_date' => 'required|date|min:3',
        'license.order_no' => 'required|string',
    ])]
    public ?array $license = [];
    public ?array $additional_information = [];


    #[Validate([
        'public_offer.consent' => 'required|on',
        'public_offer.digital_signature' => 'required|file||max:2048'
    ])]
    public array $public_offer = [];
    /**
     * @throws ValidationException
     */
    public function getRulesForEdrpou(): void
    {
        $this->validate($this->rulesForModel('edrpou')->toArray());
        //TODO: request api data for edrpou
    }

    /**
     * @throws ValidationException
     */
    public function getRulesForOwner(): void
    {
        $this->validate($this->rulesForModel('owner')->toArray());
    }

    /**
     * @throws ValidationException
     */
    public function getRulesForContact(): void
    {
        $this->validate($this->rulesForModel('contact')->toArray());
    }

    /**
     * @throws ValidationException
     */
    public function getRulesForAddress(): void
    {
        $this->validate($this->rulesForModel('residence_address')->toArray());
    }

    /**
     * @throws ValidationException
     */
    public function getRulesForLicense()
    {
        $this->validate($this->rulesForModel('license')->toArray());
    }

    /**
     * @throws ValidationException
     */
    public function getRulesForPublicOffer(): void
    {
        $this->validate($this->rulesForModel('public_offer')->toArray());
    }
}
