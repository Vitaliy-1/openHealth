<?php

namespace App\Livewire\LegalEntity\Forms;

use App\Models\User;
use App\Rules\AgeCheck;
use App\Rules\Cyrillic;
use App\Rules\UniqueEdrpou;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LegalEntitiesForms extends Form
{

    public string $type = 'PRIMARY_CARE';

    #[Validate(['required', 'regex:/^(\d{8,10}|[А-ЯЁЇІЄҐ]{2}\d{6})$/', new UniqueEdrpou()])]
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
            'owner.phones'                 => 'required|array',
            'owner.phones.*.number'        => 'required|string|regex:/^\+?\d{12}$/',
            'owner.phones.*.type'          => 'required|string|in:MOBILE,LAND_LINE',
            'owner.email'            => 'required|email|regex:/^([a-z0-9+-]+)(\.[a-z0-9+-]+)*@([a-z0-9-]+\.)+[a-z]{2,6}$/ix',
            'owner.position'         => 'required|string'
        ],
        message: [
            'owner.email.unique'           => 'Поле :attribute вже зарееєстровано в системі.',
            'owner.phones.required'          => 'Поле з номерами телефонів є обов\'язковим.',
            'owner.phones.array'             => 'Поле з номерами телефонів повинно бути масивом.',
            'owner.phones.*.number.required' => 'Номер телефону є обов\'язковим.',
            'owner.phones.*.number.regex'    => 'Номер телефону повинен містити 12 цифр',
            'owner.phones.*.type.required'   => 'Тип телефону є обов\'язковим.',
            'owner.phones.*.type.in'         => 'Тип телефону повинен бути "МОБІЛЬНИЙ" або "СТАЦІОНАРНИЙ"',

        ]
    )]
    public ?array $owner = [];

    #[Validate([
        'phones'          => 'required|array',
        'phones.*.number' => 'required|string|regex:/^\+?\d{12}$/',
        'phones.*.type'   => 'required|string|in:MOBILE,LAND_LINE'
    ],
        message: [
            'phones.required'          => 'Поле з номерами телефонів є обов\'язковим.',
            'phones.array'             => 'Поле з номерами телефонів повинно бути масивом.',
            'phones.*.number.required' => 'Номер телефону є обов\'язковим.',
            'phones.*.number.regex'    => 'Номер телефону повинен містити 12 цифр',
            'phones.*.type.required'   => 'Тип телефону є обов\'язковим.',
            'phones.*.type.in'         => 'Тип телефону повинен бути "МОБІЛЬНИЙ" або "СТАЦІОНАРНИЙ"',
        ]
    )]
    public ?array $phones = [];

    #[Validate([
        'website' => ['required', 'regex:/^(https?:\/\/)?(www\.)?([a-z0-9\-]+\.)+[a-z]{2,}$/i']
    ])]
    public string $website = '';

    #[Validate('required|email|regex:/^([a-z0-9+-]+)(.[a-z0-9+-]+)*@([a-z0-9-]+.)+[a-z]{2,6}$/ix')]
    public string $email = '';

    public ?array $residence_address = [];
    #[Validate([
        'accreditation.category' => ['required_with:accreditation.order_no,accreditation.order_date,accreditation.issued_date,accreditation.expiry_date', 'string'],
        'accreditation.order_no' => ['required_with:accreditation.category,accreditation.order_date,accreditation.issued_date,accreditation.expiry_date', 'string', 'min:2'],
        'accreditation.order_date' => ['required_with:accreditation.category,accreditation.order_no,accreditation.issued_date,accreditation.expiry_date', 'date'],
        'accreditation.issued_date' => ['nullable', 'date'],
        'accreditation.expiry_date' => ['nullable', 'date'],
    ])]
    public ?array $accreditation = [];

    #[Validate([
        'license.type'             => 'required|string',
        'license.issued_by'        => ['required', 'string','min:3',new Cyrillic()],
        'license.issued_date'      => 'required|date|min:3',
        'license.active_from_date' => 'required|date|min:3',
        'license.order_no'         => 'required|string',
        'license.license_number'   => [
            'nullable',
            'string',
            'regex:/^(?!.*[ЫЪЭЁыъэё@$^#])[a-zA-ZА-ЯҐЇІЄа-яґїіє0-9№\"!\^\*)\]\[(&._-].*$/'
        ],
    ])]
    public array|string $license = [];

    #[Validate([
        'archive.date'  => 'required_with:archive.place|date',
        'archive.place' => 'required_with:archive.date|string',
    ])]
    public ?array $archive = [];

    #[Validate([
        'receiver_funds_code' => 'nullable|string|regex:/^[0-9]+$/'
    ])]
    public ?string $receiver_funds_code = '';


    #[Validate(['min:3', new Cyrillic()])]
    public ?string $beneficiary = '';

    public ?array $public_offer = [];

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

        // Validate email
        $this->validate($this->rulesForModel('email')->toArray());
        // Validate website
        $this->validate($this->rulesForModel('website')->toArray());
        // Validate phones array rules
        $this->validate($this->rulesForModel('phones')->toArray());
    }

    /**
     * @throws ValidationException
     */
    public function rulesForAccreditation(): void
    {
        // Validate accreditation array rules
        $this->validate($this->rulesForModel('accreditation')->toArray());
    }

    /**
     * @throws ValidationException
     */
    public function rulesForLicense()
    {
        // Validate license array rules
        $this->validate($this->rulesForModel('license')->toArray());
    }

    /**
     * @throws ValidationException
     */
    public function rulesForAdditionalInformation(): void
    {
        // Validate archive array rules
        $this->validate($this->rulesForModel('archive')->toArray());
        // Validate beneficiary
        $this->validate($this->rulesForModel('beneficiary')->toArray());
        // Validate receiver_funds_code
        $this->validate($this->rulesForModel('receiver_funds_code')->toArray());

    }

}
