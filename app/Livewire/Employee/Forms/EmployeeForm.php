<?php

namespace App\Livewire\Employee\Forms;

use App\Core\Arr;
use App\Rules\BirthDate;
use App\Rules\Name;
use Livewire\Form;

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

    protected function rules(): array
    {
        return array_merge(
            $this->partyRules(),
            $this->documentsRules(),
            $this->educationsRules(),
            $this->specialitiesRules(),
            $this->scienceDegreesRules(),
            $this->qualificationsRules(),
        );
    }

    protected function partyRules(): array
    {
        return [
            'party.lastName'         => ['required', new Name()],
            'party.firstName'        => ['required', new Name()],
            'party.secondName'       => [new Name()],
            'party.gender'           => ['required'],
            'party.birthDate'        => ['required', 'date', new BirthDate()],
            'party.phones.*.number'  => 'required|string|digits:13',
            'party.phones.*.type'    => 'required|string',
            'party.email'            => 'required|email',
            'party.taxId'            => 'required|min:8|max:10',
            'party.employeeType'     => 'required|string',
            'party.position'         => 'required|string',
            'party.startDate'        => 'date',
        ];
    }

    protected function documentsRules(): array
    {
        return [
            'documents'           => 'required|array|min:1',
            'documents.*.type'    => 'required|string|min:3',
            'documents.*.number'  => 'required|string|min:3',
            'documents.*.issuedBy'=> 'nullable|string',
            'documents.*.issuedAt'=> 'nullable|date',
        ];
    }

    protected function educationsRules(): array
    {
        return [
            'educations'                            => 'array',
            'educations.*.country'                  => 'required|string',
            'educations.*.city'                     => 'required|string|min:3',
            'educations.*.institution_name'         => 'required|string|min:3',
            'educations.*.speciality'               => 'required|string|min:3',
            'educations.*.degree'                   => 'required|string|min:3',
            'educations.*.issued_date'              => 'required|date',
            'educations.*.diploma_number'           => 'required|string|min:3',
        ];
    }

    protected function specialitiesRules(): array
    {
        return [
            'specialities'                          => 'array',
            'specialities.*.speciality'             => 'required|string|min:3',
            'specialities.*.level'                  => 'required|string|min:3',
            'specialities.*.attestation_name'       => 'required|string|min:3',
            'specialities.*.certificate_number'     => 'required|string|min:3',
            'specialities.*.speciality_officio'     => 'required|boolean',
        ];
    }

    protected function scienceDegreesRules(): array
    {
        return [
            'scienceDegrees'                            => 'array',
            'scienceDegrees.*.country'                  => 'nullable|string',
            'scienceDegrees.*.city'                     => 'nullable|string',
            'scienceDegrees.*.degree'                   => 'nullable|string',
            'scienceDegrees.*.institution_name'         => 'nullable|string',
            'scienceDegrees.*.diploma_number'           => 'nullable|string',
            'scienceDegrees.*.speciality'               => 'nullable|string',
        ];
    }

    protected function qualificationsRules(): array
    {
        return [
            'qualifications'                            => 'array',
            'qualifications.*.type'                     => 'nullable|string',
            'qualifications.*.institution_name'         => 'nullable|string',
            'qualifications.*.speciality'               => 'nullable|string',
            'qualifications.*.issuedDate'               => 'nullable|date',
            'qualifications.*.certificate_number'       => 'nullable|string',
        ];
    }

    /**
     * Валідація конкретного блоку форми (не повна форма)
     */
    public function rulesForModelValidate(string $model): array
    {
        return $this->validate($this->rulesForModel($model)->toArray());
    }

    /**
     * Додаткова валідація перед відправкою форми на API
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

        if (isset($this->party['taxId']) && empty($this->party['taxId'])) {
            return [
                'error' => true,
                'message' => __('validation.custom.documentsEmpty'),
            ];
        }

        if (in_array($this->party['employeeType'], $doctorTypes)) {
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

    public function validated(): array
    {
        return Arr::snakeKeys($this->validate());
    }
}
