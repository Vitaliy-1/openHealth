<?php

namespace App\Livewire\LegalEntity\Forms;

use App\Models\User;
use App\Rules\AgeCheck;
use App\Rules\Cyrillic;
use App\Rules\InDictionaryCheck;
use App\Rules\UniqueEdrpou;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Exceptions\CustomValidationException;

class LegalEntitiesForms extends Form
{
    public string $type = 'PRIMARY_CARE';

    protected string $positionKeys;

    #[Validate(['required', 'regex:/^(\d{8,10}|[А-ЯЁЇІЄҐ]{2}\d{6})$/', new UniqueEdrpou()])]
    public string $edrpou = '';

    #[Validate(
        [
            'owner.lastName'        => ['required', 'min:3', new Cyrillic()],
            'owner.firstName'       => ['required', 'min:3', new Cyrillic()],
            'owner.secondName'      => ['nullable', new Cyrillic()],
            'owner.gender'           => 'required|string',
            'owner.birthDate'       => ['required', 'date', new AgeCheck()],
            'owner.noTaxId'        => 'boolean|nullable',
            'owner.taxId'           => 'required|integer|digits:10',
            'owner.documents.type'   => ['required','string', new InDictionaryCheck('document_type')],
            'owner.documents.number' => 'required|string',
            'owner.phones'           => 'required|array',
            'owner.phones.*.number'  => 'required|string|regex:/^\+?\d{12}$/',
            'owner.phones.*.type'    => ['required', 'string', new InDictionaryCheck('phone_type')],
            'owner.email'            => 'required|email|regex:/^([a-z0-9+-]+)(\.[a-z0-9+-]+)*@([a-z0-9-]+\.)+[a-z]{2,6}$/ix',
            'owner.position'         => ['required','string', new InDictionaryCheck('position')]
        ],
        message: [
            'owner.email.unique'           => 'Поле :attribute вже зареєстровано в системі',
            'owner.phones.required'          => 'Поле з номерами телефонів є обов\'язковим',
            'owner.phones.array'             => 'Поле з номерами телефонів повинно бути масивом',
            'owner.age_check'                => 'Вік власника має бути не менше 18 років',
            'owner.phones.*.number.required' => 'Номер телефону є обов\'язковим',
            'owner.phones.*.number.regex'    => 'Номер телефону повинен містити 12 цифр',
            'owner.phones.*.type.required'   => 'Тип телефону є обов\'язковим',
            'owner.phones.*.type'         => 'Тип телефону повинен бути "МОБІЛЬНИЙ" або "СТАЦІОНАРНИЙ"'
        ]
    )]
    public ?array $owner = [];

    #[Validate([
        'phones'          => 'required|array',
        'phones.*.number' => 'required|string|regex:/^\+?\d{12}$/',
        'phones.*.type'   => ['required', 'string', new InDictionaryCheck('phone_type')]
    ],
        message: [
            'phones.required'          => 'Поле з номерами телефонів є обов\'язковим',
            'phones.array'             => 'Поле з номерами телефонів повинно бути масивом',
            'phones.*.number.required' => 'Номер телефону є обов\'язковим',
            'phones.*.number.regex'    => 'Номер телефону повинен містити 12 цифр',
            'phones.*.type.required'   => 'Тип телефону є обов\'язковим',
            'phones.*.type'         => 'Тип телефону повинен бути "МОБІЛЬНИЙ" або "СТАЦІОНАРНИЙ"',
        ]
    )]
    public ?array $phones = [];

    #[Validate([
        'website' => ['required', 'regex:/^(https?:\/\/)?(www\.)?([a-z0-9\-]+\.)+[a-z]{2,}$/i']
    ])]
    public string $website = '';

    #[Validate('required|email|regex:/^([a-z0-9+-]+)(.[a-z0-9+-]+)*@([a-z0-9-]+.)+[a-z]{2,6}$/ix')]
    public string $email = '';

    public ?array $residenceAddress = [];

    public bool $archivationShow = false;

    public bool $accreditationShow = false;

    #[Validate([
        'accreditation.category' => ['required', 'string'],
        'accreditation.orderNo' => ['required', 'string', 'min:2'],
        'accreditation.orderDate' => ['required', 'date'],
        'accreditation.issuedDate' => ['nullable', 'date'],
        'accreditation.expiryDate' => ['nullable', 'date'],
    ])]
    public ?array $accreditation = [];

    #[Validate([
        'license.type'             => 'required|string',
        'license.issuedBy'        => ['required', 'string','min:3',new Cyrillic()],
        'license.issuedDate'      => 'required|date|min:3',
        'license.activeFromDate' => 'required|date|min:3',
        'license.orderNo'         => 'required|string',
        'license.licenseNumber'   => [
            'nullable',
            'string',
            'regex:/^(?!.*[ЫЪЭЁыъэё@$^#])[a-zA-ZА-ЯҐЇІЄа-яґїіє0-9№\"!\^\*)\]\[(&._-].*$/'
        ],
    ])]
    public array|null $license = [];

    #[Validate([
        'archive.date'  => 'required|date',
        'archive.place' => 'required|string',
    ])]
    public ?array $archive = [];

    #[Validate([
        'receiverFundsCode' => 'nullable|string|regex:/^[0-9]+$/'
    ])]
    public ?string $receiverFundsCode = '';


    #[Validate(['min:3', new Cyrillic()])]
    public ?string $beneficiary = '';

    public ?array $publicOffer = [];

    public array $security = [
        'redirect_uri' => 'https://openhealths.com/ehealth/oauth',
    ];

    public function messages(): array
    {
        return [
            'owner.firstName' => __('Iм\'я є обов\'язковим до заповнення'),
            'owner.lastName' => __('Прізвище є обов\'язковим до заповнення'),
            // 'owner.secondName' => __('Це поле є обов\'язковим до заповнення'),
            'owner.birthDate.required' => __('Дата народження є обов\'язковою до заповнення'),
            'owner.gender' => __('Це поле є обов\'язковим до заповнення'),
            'owner.phones' => __('Контактний телефон є обов\'язковим до заповнення'),
            'owner.taxId' => __('Номер ІПН чи РНОКПП є обов\'язковим до заповнення'),
            'owner.documents.type.required' => __('Тип документа є обов\'язковим до заповнення'),
            'owner.position.required' => __('Посада є обов\'язковою до заповнення'),
            'accreditation.category' => __('Категорія є обов\'язковою до заповнення'),
            'accreditation.orderNo' => __('Номер наказу є обов\'язковим до заповнення'),
            'accreditation.orderDate' => __('Дата наказу є обов\'язковою до заповнення'),
            'license.issuedDate' => __('Дата видачі є обов\'язковою до заповнення'),
            'license.activeFromDate' => __('Дата початку дії є обов\'язковою до заповнення'),
            'license.issuedBy' => __('Потрібно вказати орган, який видав документ'),
            'license.orderNo' => __('Номер наказу є обов\'язковим до заповнення')
        ];
    }

    public function onEditValidate()
    {
        $errors = [];

        try {
            $errors = $this->component->addressValidation();

            try {
                $this->rulesForSignificancy();
            } catch(ValidationException $e) {
                $errors = array_merge($e->errors(), $errors);
            }

            $this->validate();

            if (!empty($errors)) {
                throw ValidationException::withMessages($errors);
            }
        } catch(ValidationException $err) {
            $errors = array_merge($err->errors(), $errors);

            // Throw an validation error from Division's side
            throw ValidationException::withMessages($errors);
        }

        // $this->customRulesValidation(); // TODO: Uncomment this after adding custom rules
    }

    public function rulesForAddresses()
    {
        $errors = [];

        $errors = $this->component->addressValidation();

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

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
                'legalEntityForm.owner.email' => 'Цей користувач вже зареєстрований як співробітник в іншому закладі',
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
        $this->validate($this->rulesForModel('receiverFundsCode')->toArray());

    }

    public function rulesForSignificancy()
    {
        $this->component->validate($this->component->getRules());
    }

    /**
     * Rules for business-logic validation
     *
     * @return string
     */
    public function customRulesValidation(): void
    {
        foreach ($this->customRules() as $rule) {
            try {
                $rule->validate('', '', fn() => null);
            } catch (CustomValidationException $e) {
                $this->component->dispatch('flashMessage', ['message' => $e->getMessage(), 'type' => 'error']);
            }
        }
    }

    /**
     * TODO: add rule for next cases:
     *  - Check custom validation rules (mostly for business-logic)
     *
     * @return array
     */
    protected function customRules(): array
    {
        $validationRules = [];

        $customValidationRules = [
            // Place here the custom validation rules to be checked through creation/updating of the LegalEntity
        ];

        $validationRules = array_merge($validationRules, $customValidationRules);

        return $validationRules;
    }
}
