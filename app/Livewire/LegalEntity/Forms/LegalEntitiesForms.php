<?php

namespace App\Livewire\LegalEntity\Forms;

use App\Rules\ExpiryDate;
use Livewire\Form;
use App\Rules\Name;
use App\Models\User;
use App\Rules\Email;
use App\Rules\TaxId;
use App\Rules\Cyrillic;
use App\Rules\BirthDate;
use App\Rules\DocumentNumber;
use App\Rules\InDictionary;
use App\Rules\UniqueEdrpou;
use App\Exceptions\CustomValidationException;
use App\Rules\AgeCheck;
use App\Rules\PhoneNumber;
use Illuminate\Validation\ValidationException;

class LegalEntitiesForms extends Form
{
    public string $type = 'PRIMARY_CARE';

    protected string $positionKeys;

    public string $edrpou = '';

    public ?array $owner = [];

    public ?array $phones = [];

    public string $website = '';

    public string $email = '';

    public ?array $residenceAddress = [];

    public bool $archivationShow = false;

    public bool $accreditationShow = false;

    public ?array $accreditation = [];

    public array|null $license = [];

    public ?array $archive = [];

    public ?string $receiverFundsCode = '';

    public ?string $beneficiary = '';

    public ?array $publicOffer = [];

    public array $security = [
        'redirect_uri' => 'https://openhealths.com/ehealth/oauth',
    ];

    public function rules(): array
    {
        return [
            'edrpou' => ['required', 'regex:/^(\d{8,10}|[А-ЯЁЇІЄҐ]{2}\d{6})$/', new UniqueEdrpou()],
            'owner.lastName' => ['required', 'min:3', new Name()],
            'owner.firstName' => ['required', 'min:3', new Name()],
            'owner.secondName' => ['nullable', new Name()],
            'owner.gender' => 'required|string',
            'owner.birthDate' => ['required', 'date', new BirthDate(), new AgeCheck()],
            'owner.noTaxId' => 'boolean|nullable',
            'owner.taxId' => ['required', new TaxId($this->owner['noTaxId'])],
            'owner.documents.type' => ['required','string', new InDictionary('DOCUMENT_TYPE')],
            'owner.documents.number' => ['required', 'string', new DocumentNumber($this->owner['documents']['type'] ?? '')],
            'owner.phones' => 'required|array',
            'owner.phones.*.number' => ['required', 'string', new PhoneNumber()],
            'owner.phones.*.type' => ['required', 'string', new InDictionary('PHONE_TYPE')],
            'owner.email' => ['required','email',new Email()],
            'owner.position' => ['required','string', new InDictionary('POSITION')],
            'email' => ['required','email',new Email()],
            'website' => ['required', 'regex:/^(https?:\/\/)?(www\.)?([a-z0-9\-]+\.)+[a-z]{2,}$/i'],
            'phones' => 'required|array',
            'phones.*.number' => ['required', 'string', new PhoneNumber()],
            'phones.*.type' => ['required', 'string', new InDictionary('PHONE_TYPE')],
            'accreditation.category' => ['required', 'string'],
            'accreditation.orderNo' => ['required', 'string', 'min:2'],
            'accreditation.orderDate' => ['required', 'date'],
            'accreditation.issuedDate' => ['nullable', 'date'],
            'accreditation.expiryDate' => ['nullable', 'date', new ExpiryDate($this->accreditation['issuedDate'] ?? '')],
            'license.type' => 'required|string',
            'license.issuedBy' => ['required', 'string','min:3',new Cyrillic()],
            'license.issuedDate' => 'required|date|min:3',
            'license.activeFromDate' => 'required|date|min:3',
            'license.expiryDate' => ['nullable', 'date', new ExpiryDate($this->license['activeFromDate'] ?? '')],
            'license.orderNo' => 'required|string',
            'license.licenseNumber' => ['nullable', 'string', 'regex:/^(?!.*[ЫЪЭЁыъэё@$^#])[a-zA-ZА-ЯҐЇІЄа-яґїіє0-9№\"!\^\*)\]\[(&._-].*$/'],
            'receiverFundsCode' => 'nullable|string|regex:/^[0-9]+$/',
            'beneficiary' => ['min:3', new Cyrillic()],
            'archive.date'  => 'required|date',
            'archive.place' => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'edrpou.required' => __('Це поле є обов\'язковим до заповнення'),
            'edrpou.regex' => __('Поле має хибний формат'),
            'edrpou.unique_edrpou' => __('Такий номер вже існує'),
            'owner.firstName.required' => __('Iм\'я є обов\'язковим до заповнення'),
            'owner.lastName.required' => __('Прізвище є обов\'язковим до заповнення'),
            'owner.firstName' => __('Поле має хибний формат. (Дозволено лише кирилічні символи)'),
            'owner.lastName' => __('Поле має хибний формат. (Дозволено лише кирилічні символи)'),
            'owner.secondName' => __('Поле має хибний формат. (Дозволено лише кирилічні символи)'),
            'owner.birthDate.required' => __('Дата народження є обов\'язковою до заповнення'),
            'owner.age_check' => 'Вік власника має бути не менше 18 років',
            'owner.gender' => __('Це поле є обов\'язковим до заповнення'),
            'owner.phones' => __('Контактний телефон є обов\'язковим до заповнення'),
            'owner.taxId.required' => __('Номер ІПН чи РНОКПП є обов\'язковим до заповнення'),
            'owner.documents.type.required' => __('Тип документа є обов\'язковим до заповнення'),
            'owner.position.required' => __('Посада є обов\'язковою до заповнення'),
            'owner.email.unique' => 'Поле :attribute вже зареєстровано в системі',
            'owner.phones.required' => 'Поле з номерами телефонів є обов\'язковим',
            'owner.phones.array' => 'Поле з номерами телефонів повинно бути масивом',
            'owner.phones.*.number.required' => 'Номер телефону є обов\'язковим',
            'owner.phones.*.number.regex' => 'Номер телефону повинен містити 12 цифр',
            'owner.phones.*.type.required' => 'Тип телефону є обов\'язковим',
            'owner.phones.*.type' => 'Тип телефону повинен бути "МОБІЛЬНИЙ" або "СТАЦІОНАРНИЙ"',
            'website.required' => __('Це поле є обов\'язковим до заповнення'),
            'website' => __('Поле має хибний формат'),
            'phones.required' => 'Поле з номерами телефонів є обов\'язковим',
            'phones.array' => 'Поле з номерами телефонів повинно бути масивом',
            'phones.*.number.required' => 'Номер телефону є обов\'язковим',
            'phones.*.number.regex' => 'Номер телефону повинен містити 12 цифр',
            'phones.*.type.required' => 'Тип телефону є обов\'язковим',
            'phones.*.type' => 'Тип телефону повинен бути "МОБІЛЬНИЙ" або "СТАЦІОНАРНИЙ"',
            'accreditation.category.required' => __('Категорія є обов\'язковою до заповнення'),
            'accreditation.orderNo.required' => __('Номер наказу є обов\'язковим до заповнення'),
            'accreditation.orderDate.required' => __('Дата наказу є обов\'язковою до заповнення'),
            'accreditation.orderNo.min' => __('Поле має хибний формат. (Мінімальна довжина - 2 символи)'),
            'accreditation.category' => __('Поле має хибний формат'),
            'accreditation.orderNo' => __('Поле має хибний формат'),
            'license.issuedDate' => __('Дата видачі є обов\'язковою до заповнення'),
            'license.activeFromDate' => __('Дата початку дії є обов\'язковою до заповнення'),
            'license.issuedBy.min' => __('Поле має хибний формат. (Мінімальна довжина - 3 символи)'),
            'license.issuedBy' => __('Потрібно вказати орган, який видав документ'),
            'license.orderNo' => __('Номер наказу є обов\'язковим до заповнення'),
            'receiverFundsCode' => __('Поле має хибний формат. (Дозволено лише цифри)'),
            'beneficiary.min' => __('Поле має хибний формат. (Мінімальна довжина - 3 символи)'),
            'beneficiary' => __('Поле має хибний формат. (Дозволено лише кирилічні символи)'),
            'archive.date' => __('Це поле є обов\'язковим до заповнення'),
            'archive.place' => __('Це поле є обов\'язковим до заповнення'),
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
        $emailRules = $this->rulesForModel('email')->toArray();

        // Validate website
        $websiteRules = $this->rulesForModel('website')->toArray();

        // Validate phones array rules
        $phonesRules = $this->rulesForModel('phones')->toArray();

        $modelRules = array_merge($emailRules, $websiteRules, $phonesRules);

        $this->validate($modelRules);
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
        $archiveRules = $this->rulesForModel('archive')->toArray();

        // Validate beneficiary
        $beneficiaryRules = $this->rulesForModel('beneficiary')->toArray();

        // Validate receiver_funds_code
        $fundsCodeRules = $this->rulesForModel('receiverFundsCode')->toArray();

        $modelRules = array_merge($archiveRules, $beneficiaryRules, $fundsCodeRules);

        $this->validate($modelRules);
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
