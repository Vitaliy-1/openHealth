<?php

namespace App\Livewire\LegalEntity\Forms;

use App\Models\User;
use App\Rules\AgeCheck;
use App\Rules\Cyrillic;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LegalEntitiesForms extends Form
{

    public string $type = 'PRIMARY_CARE';
    #[Validate(['required', 'integer','regex:/^\d{6}$|^\d{10}$/'])]
    public string $edrpou = '';

    #[Validate(
        [
        'owner.last_name'        => ['required', 'min:3', new Cyrillic()],
        'owner.first_name'       => ['required', 'min:3', new Cyrillic()],
        'owner.second_name'      => [new Cyrillic()],
        'owner.gender'           => 'required|string',
        'owner.birth_date'       => ['required', 'date', new AgeCheck()],
        'owner.no_tax_id'        => 'boolean',
        'owner.tax_id'           => 'required|integer|digits:10',
        'owner.documents.type'   => 'required|string',
        'owner.documents.number' => 'required|string',
        'owner.phones.*.number'  => 'required|string:digits:13',
        'owner.phones.*.type'    => 'required|string',
        'owner.email'            => 'required|email|regex:/^([a-z0-9+-]+)(\.[a-z0-9+-]+)*@([a-z0-9-]+\.)+[a-z]{2,6}$/ix',
        'owner.position'         => 'required|string'
    ],
     message: [
        'owner.email.unique' => 'Поле :attribute вже зарееєстровано в системі.',
        ]
    )]
    public ?array $owner = [];

    #[Validate([
        'phones.*.number' => 'required|string:digits:13',
        'phones.*.type'   => 'required|string'
    ])]
    public ?array $phones = [];

    #[Validate('url|regex:/^(https?:\/\/)?([a-zA-Z0-9\-_]+\.)+[a-zA-Z]{2,}$/')]
    public string $website = '';

    #[Validate('required|email|regex:/^([a-z0-9+-]+)(.[a-z0-9+-]+)*@([a-z0-9-]+.)+[a-z]{2,6}$/ix')]
    public string $email = '';

    public ?array $residence_address = [];
    #[Validate([
        'accreditation.category'   => 'required|string',
        'accreditation.order_no'   => 'required|string:min:2',
        'accreditation.order_date' => 'required|date',
    ])]
    public ?array $accreditation = [];

    #[Validate([
        'license.type'             => 'required|string',
        'license.issued_by'        => 'required|string|min:3',
        'license.issued_date'      => 'required|date|min:3',
        'license.active_from_date' => 'required|date|min:3',
        'license.order_no'         => 'required|string',
    ])]
    public ?array $license = [];

    #[Validate([
        'archive.date'  => 'required_with:archive.place|date',
        'archive.place' => 'required_with:archive.date|string',
    ])]
    public ?array $archive = [];
    public ?string $receiver_funds_code = '';

    #[Validate([  'min:3', new Cyrillic()])]
    public ?string $beneficiary = '';

    #[Validate([
//        'public_offer.consent' => 'required|on',
//        'public_offer.digital_signature' => 'required|file|max:2048'
    ])]
    public array $public_offer = [];

    public array $security = [
        'redirect_uri' => 'https://openhealths.com/ehealth/oauth',
    ];


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
        $userQuery = User::where('email', $this->owner['email'])->first();
        if ($userQuery && $userQuery->legalEntity()->exists()) {
            throw ValidationException::withMessages([
                'legal_entity_form.owner.email' => 'Цей користувач вже зареєстрований як співробітник в іншому закладі',
            ]);
        }

    }

    /**
     * @throws ValidationException
     */
    public function rulesForContact(): void
    {
        $this->validate($this->rulesForModel('email')->toArray());
        $this->validate($this->rulesForModel('website')->toArray());
        $this->validate($this->rulesForModel('phones')->toArray());
    }

    /**
     * @throws ValidationException
     */
    public function rulesForAccreditation(): void
    {
        $this->validate($this->rulesForModel('accreditation')->toArray());
    }

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
    public function rulesForAdditionalInformation(): void
    {
        $this->validate($this->rulesForModel('archive')->toArray());
        $this->validate($this->rulesForModel('beneficiary')->toArray());

    }

    /**
     * @throws ValidationException
     */
    public function rulesForPublicOffer(): void
    {
        $this->validate($this->rulesForModel('public_offer')->toArray());
    }


}
