<?php

namespace App\Livewire\Employee\Forms;

use App\Rules\AgeCheck;
use App\Rules\BirthDate;
use App\Rules\Cyrillic;
use App\Rules\InDictionaryCheck;
use App\Rules\Name;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

use function Livewire\of;

class EmployeeForm extends Form
{

    public string $status = 'NEW';

    /**
     * Default values are transferred to the Alpine on the frontend
     */
    public ?array $party = [
        'position' => '',
        'employeeType' => '',
        'phones' => [
            [
                'type' => '',
                'number' => '',
            ]
        ]
    ];

    public array $documents = [];
    public ?array $education = [
        'country' => '',
    ];
    public ?array $educations = [];
    public ?array $speciality = [];
    public ?array $specialities = [];
    public ?array $scienceDegree = [];
    public ?array $qualification = [];
    public ?array $qualifications = [];

    protected function rules()
    {
        return [
            // Party
            'party.lastName' => ['required', new Name()],
            'party.firstName' => ['required', new Name()],
            'party.secondName' => [new Name()],
            'party.gender' => ['required'],
            'party.birthDate' => ['required', 'date', new BirthDate()],
            'party.phones.*.number' => 'required|string:digits:13',
            'party.phones.*.type' => 'required|string',
            'party.email' => 'required|email',
            'party.taxId' => 'required|min:8|max:10',
            'party.employeeType' => 'required|string',
            'party.position' => 'required|string',
            'party.startDate' => 'date',

            // Documents
            'documents' => 'required',
            'documents.*.type' => 'required|string|min:3',
            'documents.*.number' => 'required|string|min:3',

            // Education
            'education.country'         => 'string',
            'education.city'            => 'string|min:3',
            'education.institutionName' => 'string|min:3',
            'education.diplomaNumber'   => 'string|min:3',
            'education.degree'          => 'string|min:3',
            'education.speciality'      => 'string|min:3',

            // Speciality
            'speciality.speciality'        => 'string|min:3',
            'speciality.level'             => 'string|min:3',
            'speciality.qualificationType' => 'string|min:3',
            'speciality.attestationName'   => 'string|min:3',
            'speciality.attestationDate'   => 'date',
            'speciality.certificateNumber' => 'string|min:3',

            // Science degree
            'scienceDegree.country'         => 'string',
            'scienceDegree.city'            => 'string',
            'scienceDegree.degree'          => 'string',
            'scienceDegree.institutionName' => 'string',
            'scienceDegree.diplomaNumber'   => 'string',
            'scienceDegree.speciality'      => 'string',

            // Qualification
            'qualification.type'              => 'string',
            'qualification.institutionName'   => 'string',
            'qualification.speciality'        => 'string',
            'qualification.issuedDate'        => 'date',
            'qualification.certificateNumber' => 'string',
        ];
    }

    /**
     * @throws ValidationException
     */
    public function rulesForModelValidate(string $model): array
    {
        return $this->validate($this->rulesForModel($model)->toArray());
    }

    public function validateBeforeSendApi(): array
    {

        $doctorTypes = config('ehealth.doctors_type'); // Get doctor types from config

        // Check if documents is empty
        if (empty($this->documents)) {
            return [
                'error'   => true,
                'message' => __('validation.custom.documentsEmpty'),
            ];
        }

        // Check if taxId is empty
        if (isset($this->party['taxId']) && empty($this->party['taxId'])) {
            return [
                'error'   => true,
                'message' => __('validation.custom.documentsEmpty'),
            ];
        }
        // Check if doctor type is empty
        if ( in_array($this->party['employeeType'],$doctorTypes) && empty($this->specialities)) {
            return [
                'error'   => true,
                'message' => __('validation.custom.specialityTable'),
            ];
        }
        // Check if doctor type is empty
        if ( in_array($this->party['employeeType'],$doctorTypes) && empty($this->educations)) {
            return [
                'error'   => true,
                'message' => __('validation.custom.educationTable'),
            ];
        }

        return [
            'error'   => false,
            'message' => '',
        ];
    }


}
