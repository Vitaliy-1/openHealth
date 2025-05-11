<?php

namespace App\Livewire\Employee\Forms;

use App\Rules\BirthDate;
use App\Rules\Name;
use Illuminate\Validation\ValidationException;
use Livewire\Form;

use function Livewire\of;

class EmployeeForm extends Form
{

    public string $status = 'NEW';

    public array $party = [
        'position' => '',
        'employee_type' => '',
        'start_date' => '',
        'phones' => [
            ['type' => '', 'number' => '']
        ],
        'documents' => [],
        'tax_id' => '',
        'no_tax_id' => false,
        'first_name' => '',
        'last_name' => '',
        'second_name' => '',
        'birth_date' => '',
        'gender' => '',
        'email' => '',
        'working_experience' => null,
        'about_myself' => '',
    ];

    public array $doctor = [
        'educations' => [],
        'qualifications' => [],
        'specialities' => [],
        'science_degree' => [
            'country' => '',
            'city' => '',
            'degree' => '',
            'institution_name' => '',
            'diploma_number' => '',
            'speciality' => '',
            'issued_date' => '',
        ],
    ];

    protected function rules(): array
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
        $doctorTypes = config('ehealth.doctors_type');

        if (empty($this->party['documents'])) {
            return [
                'error' => true,
                'message' => __('validation.custom.documentsEmpty'),
            ];
        }

        if (!$this->party['no_tax_id'] && empty($this->party['tax_id'])) {
            return [
                'error' => true,
                'message' => __('validation.custom.taxIdMissing'),
            ];
        }

        if (in_array($this->party['employee_type'], $doctorTypes)) {
            if (empty($this->doctor['specialities'])) {
                return [
                    'error' => true,
                    'message' => __('validation.custom.specialityTable'),
                ];
            }
            if (empty($this->doctor['educations'])) {
                return [
                    'error' => true,
                    'message' => __('validation.custom.educationTable'),
                ];
            }
        }

        return ['error' => false, 'message' => ''];
    }

    public function validated(): array
    {
        return $this->validate($this->rules());
    }

}
