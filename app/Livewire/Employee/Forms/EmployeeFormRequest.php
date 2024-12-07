<?php

namespace App\Livewire\Employee\Forms;

use App\Rules\AgeCheck;
use App\Rules\Cyrillic;
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
        'documents.type'   => 'required|string|min:3',
        'documents.number' => 'required|string|min:3',
    ])]
    public ?array $documents = [];

    #[Validate([
        'educations.country'         => 'required|string',
        'educations.city'            => 'required|string|min:3',
        'educations.institutionName' => 'required|string|min:3',
        'educations.diplomaNumber'   => 'required|string|min:3',
        'educations.degree'          => 'required|string|min:3',
        'educations.speciality'      => 'required|string|min:3',
    ])]

    public ?array $educations = [
        'country'         => '',
    ];

    #[Validate([
        'specialities.speciality'        => 'required|string|min:3',
        'specialities.level'             => 'required|string|min:3',
        'specialities.qualificationType' => 'required|string|min:3',
        'specialities.attestationName'   => 'required|string|min:3',
        'specialities.attestationDate'   => 'required|date',
        'specialities.certificateNumber' => 'required|string|min:3',

    ])]
    public ?array $specialities = [];

    #[Validate([
        'scienceDegree.country'         => 'required|string',
        'scienceDegree.city'            => 'required|string',
        'scienceDegree.degree'          => 'required|string',
        'scienceDegree.institutionName' => 'required|string',
        'scienceDegree.diplomaNumber'   => 'required|string',
        'scienceDegree.speciality'      => 'required|string',

    ])]
    public ?array $scienceDegree = [
        'country' => '',
    ];

    #[Validate([
        'qualifications.type'              => 'required|string',
        'qualifications.institutionName'   => 'required|string',
        'qualifications.speciality'        => 'required|string',
        'qualifications.issuedDate'        => 'required|date',
        'qualifications.certificateNumber' => 'required|string',
    ])]
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
        if (empty($this->party['documents'])) {
            return [
                'error'   => true,
                'message' => __('validation.custom.documentsEmpty'),
            ];
        }

        if (isset($this->party['taxId']) && empty($this->party['taxId'])) {
            return [
                'error'   => true,
                'message' => __('validation.custom.documentsEmpty'),
            ];
        }

        if (isset($this->employeeType) && $this->employeeType == 'DOCTOR' && empty($this->specialities)) {
            return [
                'error'   => true,
                'message' => __('validation.custom.specialitiesTable'),
            ];
        }

        if (isset($this->employeeType) && $this->employeeType == 'DOCTOR' && empty($this->educations)) {
            return [
                'error'   => true,
                'message' => __('validation.custom.educationsTable'),
            ];
        }

        return [
            'error'   => false,
            'message' => '',
        ];
    }


}
