<?php

namespace App\Livewire\LegalEntity\Forms;

use App\Models\LegalEntity;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LegalEntitiesForms extends Form
{

    public string $type = 'PRIMARY_CARE';
    #[Validate('required|integer|digits_between:6,10')]
    public string $edrpou = '';

    #[Validate([
        'owner.last_name' => 'required|min:3',
        'owner.first_name' => 'required|min:3',
        'owner.gender' => 'required|string',
        'owner.birth_date' => 'required|date',
        'owner.no_tax_id' => 'boolean',
        'owner.tax_id' => 'exclude_if:owner.no_tax_id,true|required|integer|digits_between:6,10',
        'owner.documents.type' => 'exclude_if:owner.no_tax_id,false|required|string',
        'owner.documents.number' => 'exclude_if:owner.no_tax_id,false|required|string',
        'owner.phones.*.number' => 'required|string:digits:13',
        'owner.phones.*.type' => 'required|string',
        'owner.email' => 'required|email',
        'owner.position' => 'required|string'
    ])]

    public ?array $owner = [
//        'no_tax_id' => false
    ];

    #[Validate([
        'phones.*.number' => 'required|string:digits:13',
        'phones.*.type' => 'required|string'
    ])]
    public ?array $phones = [];

    public string $website = '';

    #[Validate('required|email')]
    public string $email = '';


//    #[Validate([
//        'residence_address.area' => 'required',
//        'residence_address.region' => 'required',
//        'residence_address.settlement' => 'required',
//        'residence_address.settlement_type' => 'required',
//        'residence_address.street_type' => 'required',
//    ])]

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

    public ?array $archive = [];
    public ?string $receiver_funds_code = '';
    public ?string $beneficiary = '';

    #[Validate([
//        'public_offer.consent' => 'required|on',
//        'public_offer.digital_signature' => 'required|file|max:2048'
    ])]

    public array $public_offer = [
        'consent_text' => 'Тестове consent_text',
        'consent' => true
    ];

    public array  $security =  [
        'redirect_uri' => 'https://openhealths.com/ehealth/oauth',
    ];

        /**
     * @var array|mixed
     */

    /**
     * @throws ValidationException
     */
    public function rulesForEdrpou(): array
    {

       return $this->validate($this->rulesForModel('edrpou')->toArray());

    }

    /**
     * @throws ValidationException
     */
    public function rulesForOwner(): void
    {
        $this->validate($this->rulesForModel('owner')->toArray());
    }

    /**
     * @throws ValidationException
     */
    public function rulesForContact(): void
    {
        $this->validate($this->rulesForModel('email')->toArray());
        $this->validate($this->rulesForModel('phones')->toArray());
    }

    /**
     * @throws ValidationException
//     */
//    public function rulesForAddress(): void
//    {
//        $this->validate($this->rulesForModel('addresses')->toArray());
//    }

    /**
     * @throws ValidationException
     */
    public function rulesForLicense()
    {
        $this->validate($this->rulesForModel('license')->toArray());
    }

    /**
     * @throws ValidationException
     */
    public function rulesForPublicOffer(): void
    {
        $this->validate($this->rulesForModel('public_offer')->toArray());
    }


}
