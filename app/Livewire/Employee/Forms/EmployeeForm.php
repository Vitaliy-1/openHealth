<?php

namespace App\Livewire\Employee\Forms;

use App\Rules\BirthDate;
use App\Rules\Email;
use App\Rules\Name;
use Illuminate\Support\Facades\Validator;
use Livewire\Form;
use App\Rules\PhoneNumber;
use Illuminate\Validation\ValidationException;

/**
 * @property-read array $rules
 */
class EmployeeForm extends Form
{
    public string $status = 'NEW';

    public array $party = [
        'position'      => '',
        'employeeType'  => '',
        'phones'        => [
            [
                'type'   => '',
                'number' => '',
            ]
        ],
    ];

    public array $documents = [];
    public array $educations = [];
    public array $specialities = [];
    public array $scienceDegrees = [];
    public array $qualifications = [];


    public ?string $knedp = null;
    public $keyContainerUpload;
    public ?string $password = null;


    //TODO rework validation rules
    /**
     * Defines the validation rules for the form.
     * @return array
     */
    protected function rules(): array
    {
        return array_merge(
            $this->partyRules(),
            $this->documentsRules(),
            $this->educationsRules(),
            $this->specialitiesRules(),
            $this->scienceDegreesRules(),
            $this->qualificationsRules(),
            $this->kepRules()
        );
    }

    /**
     * Defines validation rules for party-related data.
     * @return array
     */
    protected function partyRules(): array
    {
        return [
            'party.lastName'         => ['required', new Name()],
            'party.firstName'        => ['required', new Name()],
            'party.secondName'       => [new Name()],
            'party.gender'           => ['required'],
            'party.birthDate'        => ['required', 'date', new BirthDate()],
            'party.phones.*.number'  => [new PhoneNumber()],
            'party.phones.*.type'    => 'required',
            'party.employeeType'     => ['required', 'string'],
            'party.position'         => ['required', 'string'],
            'party.taxId'            => ['nullable', 'string', 'digits:10'],
            'party.noTaxId'          => ['boolean'],
            'party.email'            => ['nullable', 'email', new Email()],
            'party.startDate'        => ['required', 'date'],
        ];
    }

    /**
     * Defines validation rules for document-related data.
     * @return array
     */
    protected function documentsRules(): array
    {
        return [
            'documents'               => ['required', 'array', 'min:1'],
            'documents.*.type'        => ['required', 'string'],
            'documents.*.series'      => ['nullable', 'string', 'max:255'],
            'documents.*.number'      => ['required', 'string', 'max:255'],
            'documents.*.issuedAt'    => ['required', 'date'],
            'documents.*.issuedBy'    => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Defines validation rules for education-related data.
     * @return array
     */
    protected function educationsRules(): array
    {
        return [
            'educations'                    => ['nullable', 'array'],
            'educations.*.institution_name' => ['required', 'string', 'max:255'],
            'educations.*.country'          => ['required', 'string', 'max:255'],
            'educations.*.city'             => ['required', 'string', 'max:255'],
            'educations.*.degree'           => ['required', 'string', 'max:255'],
            'educations.*.diploma_number'   => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Defines validation rules for speciality-related data.
     * @return array
     */
    protected function specialitiesRules(): array
    {
        return [
            'specialities'                  => ['nullable', 'array'],
            'specialities.*.speciality'     => ['required', 'string', 'max:255'],
            'specialities.*.level'          => ['required', 'string', 'max:255'],
            'specialities.*.attestation_name' => ['required', 'string', 'max:255'],
            'specialities.*.attestation_date' => ['required', 'date'],
            'specialities.*.certificate_number' => ['nullable', 'string', 'max:255'],
            'specialities.*.speciality_officio' => ['boolean'],
            'specialities.*.qualification_type' => ['nullable', 'string'],
        ];
    }

    /**
     * Defines validation rules for science degree-related data.
     * @return array
     */
    protected function scienceDegreesRules(): array
    {
        return [
            'scienceDegrees'                      => ['nullable', 'array'],
            'scienceDegrees.*.degree'             => ['required', 'string', 'max:255'],
            'scienceDegrees.*.country'            => ['required', 'string', 'max:255'],
            'scienceDegrees.*.city'               => ['required', 'string', 'max:255'],
            'scienceDegrees.*.institution_name'   => ['required', 'string', 'max:255'],
            'scienceDegrees.*.speciality'         => ['required', 'string', 'max:255'],
            'scienceDegrees.*.diploma_number'     => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Defines validation rules for qualification-related data.
     * @return array
     */
    protected function qualificationsRules(): array
    {
        return [
            'qualifications'                      => ['nullable', 'array'],
            'qualifications.*.type'               => ['required', 'string', 'max:255'],
            'qualifications.*.licence_series'     => ['nullable', 'string', 'max:255'],
            'qualifications.*.licence_number'     => ['nullable', 'string', 'max:255'],
            'qualifications.*.licence_expires_date' => ['nullable', 'date', 'after_or_equal:licence_issued_date'],
        ];
    }

    /**
     * Defines validation rules for KEP (Key Electronic Signature) related fields.
     * These are directly on the form object.
     * @return array
     */
    protected function kepRules(): array
    {
        return [
            'knedp'              => ['nullable', 'string'],
            'password'           => ['nullable', 'string'],
            'keyContainerUpload' => ['nullable', 'file', 'mimes:p7s,jks,pfx'],
        ];
    }

    /**
     * @throws ValidationException
     */
    public function validated(array $rules = null, array $messages = [], array $attributes = []): array
    {
        $rules = $rules ?? $this->rules(); // Якщо правила не передані, беремо з цього класу

        // DD для всіх даних форми перед валідацією
        // dd('Дані EmployeeForm перед валідацією:', $this->all());

        $validator = Validator::make($this->all(), $rules, $messages, $attributes);

        try {
            $validatedData = $validator->validate();
        } catch (ValidationException $e) {
            // DD для всіх помилок валідації
            dd('Помилки валідації EmployeeForm:', $e->errors());
            // Livewire автоматично обробляє ValidationException,
            // але ви можете додати додаткову логіку тут, якщо потрібно.
            throw $e;
        }

        return $validatedData;
    }

    public function reset(...$properties): void
    {
        parent::reset(...$properties);
        $this->party = [
            'position'      => '',
            'employeeType'  => '',
            'phones'        => [
                [
                    'type'   => '',
                    'number' => '',
                ]
            ],
        ];
        $this->documents = [];
        $this->educations = [];
        $this->specialities = [];
        $this->scienceDegrees = [];
        $this->qualifications = [];
        $this->knedp = null;
        $this->keyContainerUpload = null;
        $this->password = null;
        $this->status = 'NEW';
    }

    /**
     * Additional validation before sending the form to an external API.
     * (This method is not used for Livewire's form validation, but for additional checks)
     * @return array
     */
    public function validateBeforeSendApi(): array
    {
        $doctorTypes = config('ehealth.doctors_type');

        if (empty($this->documents)) {
            return [
                'error' => true,
                'message' => __('validation.custom.documentsEmpty'),
            ];
        }

        if (in_array($this->party['employeeType'], $doctorTypes, true)) {
            if (empty($this->specialities)) {
                return [
                    'error' => true,
                    'message' => __('validation.custom.specialityTable'),
                ];
            }

            if (empty($this->educations)) {
                return [
                    'error' => true,
                    'message' => __('validation.custom.educationTable'),
                ];
            }

            if (empty($this->scienceDegrees)) {
                return [
                    'error' => true,
                    'message' => __('validation.custom.science_degreesTable'),
                ];
            }
        }

        return [
            'error' => false,
            'message' => '',
        ];
    }
}
