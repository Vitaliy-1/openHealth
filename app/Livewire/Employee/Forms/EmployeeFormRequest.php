<?php

namespace App\Livewire\Employee\Forms;

use App\Rules\AgeCheck;
use App\Rules\Cyrillic;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

use function Livewire\of;

class EmployeeFormRequest extends Form
{


    public string $status = 'NEW';

    #[Validate([
        'party.lastName'        => ['required', 'min:3', new Cyrillic()],
        'party.firstName'       => ['required', 'min:3', new Cyrillic()],
        'party.gender'          => 'required|string',
        'party.birthDate'       => ['required', 'date', new AgeCheck()],
        'party.phones.*.number' => 'required|string:digits:13',
        'party.phones.*.type'   => 'required|string',
        'party.email'           => 'required|email',
        'party.taxId'           => 'required|min:8|max:10',
        'party.employeeType'    => 'required|string',
        'party.position'        => 'required|string',
        'party.startDate'       => 'date',
    ])]
    public ?array $party = [
        'position' => '',
    ];

    #[Validate([
        'document.type'   => 'required|string|min:3',
        'document.number' => 'required|string|min:3',
    ])]
    public ?array $document = [];
    public ?array $documents = [];

    #[Validate([
        'education.country'         => 'required|string',
        'education.city'            => 'required|string|min:3',
        'education.institutionName' => 'required|string|min:3',
        'education.diplomaNumber'   => 'required|string|min:3',
        'education.degree'          => 'required|string|min:3',
        'education.speciality'      => 'required|string|min:3',
    ])]
    public ?array $education = [
        'country' => '',
    ];
    public ?array $educations = [];

    #[Validate([
        'speciality.speciality'        => 'required|string|min:3',
        'speciality.level'             => 'required|string|min:3',
        'speciality.qualificationType' => 'required|string|min:3',
        'speciality.attestationName'   => 'required|string|min:3',
        'speciality.attestationDate'   => 'required|date',
        'speciality.certificateNumber' => 'required|string|min:3',

    ])]
    public ?array $speciality = [];
    public ?array $specialities = [];

    #[Validate([
        'scienceDegree.country'         => 'required|string',
        'scienceDegree.city'            => 'required|string',
        'scienceDegree.degree'          => 'required|string',
        'scienceDegree.institutionName' => 'required|string',
        'scienceDegree.diplomaNumber'   => 'required|string',
        'scienceDegree.speciality'      => 'required|string',

    ])]
    public ?array $scienceDegree = [];
    #[Validate([
        'qualification.type'              => 'required|string',
        'qualification.institutionName'   => 'required|string',
        'qualification.speciality'        => 'required|string',
        'qualification.issuedDate'        => 'required|date',
        'qualification.certificateNumber' => 'required|string',
    ])]
    public ?array $qualification = [];
    public ?array $qualifications = [];

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
