<?php

namespace App\Livewire\Employee\Forms;

use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EmployeeFormRequest extends Form
{
    #[Validate([
        'employee.last_name' => 'required|min:3',
        'employee.first_name' => 'required|min:3',
        'employee.gender' => 'required|string',
        'employee.birth_date' => 'required|date' ,
        'employee.phones.*.number' => 'required|string:digits:13',
        'employee.phones.*.type' => 'required|string',
        'employee.email' => 'required|email',
        'employee.position' => 'required|string',
        'employee.tax_id' => 'nullable|integer|digits:8,10',
    ])]

    public ?array $employee = [];

    #[Validate([
        'documents.type' => 'required|string',
        'documents.number' => 'required|string',
    ])]

    public ?array $documents = [];

    #[Validate([
        'educations.country' => 'required|string|min:3',
        'educations.city' => 'required|string|min:3',
        'educations.institution_name' => 'required|string|min:3',
        'educations.diploma_number' => 'required|string|min:3',
        'educations.degree' => 'required|string|min:3',
        'educations.speciality' => 'required|string|min:3',
    ])]

    public ?array $educations = [];

    #[Validate([
        'specialities.speciality' => 'required|string|min:3',
        'specialities.level' => 'required|string|min:3',
        'specialities.qualification_type' => 'required|string|min:3',
        'specialities.attestation_name' => 'required|string|min:3',
        'specialities.attestation_date' => 'required|date',
        'specialities.certificate_number' => 'required|string|min:3',

    ])]
    public ?array  $specialities = [];

    #[Validate([
        'positions.position' => 'required|string',
    ])]

    public ?array $positions = [];

   #[Validate([
        'role.employee_type' => 'required|string',
        'role.division_id' => 'required|integer',
        'role.healthcare_service_id' => 'required|uuid',
    ])]

    public ?array $role = [];

    #[Validate([
        'science_degree.country' => 'required|string',
        'science_degree.city' => 'required|string',
        'science_degree.degree' => 'required|string',
        'science_degree.institution_name' => 'required|string',
        'science_degree.diploma_number' => 'required|string',
        'science_degree.speciality' => 'required|string',

    ])]

    public ?array $science_degree = [];

    #[Validate([
        'qualifications.type' => 'required|string',
        'qualifications.institution_name' => 'required|string',
        'qualifications.speciality' => 'required|string',
        'qualifications.issued_date' => 'required|date',
        'qualifications.certificate_number' => 'required|string',
    ])]

    public ?array  $qualifications = [];


    /**
     * @throws ValidationException
     */
    public function rulesForModelValidate(string $model): array
    {
        return $this->validate($this->rulesForModel($model)->toArray());
    }


    public function validateBeforeSendApi(): array
    {
        if (empty($this->employee)){
            return [
                'status'=> true,
                'message' => __('validation.custom.employee_table'),
            ];
        }

        if (!isset($this->employee['tax_id']) && empty($this->documents)){
            return [
                'status'=> true,
                'message' => __('validation.custom.documents_empty'),
            ];
        }
        if (empty($this->role) ){
            return [
                'status'=> true,
                'message' => __('validation.custom.role_table'),
            ];
        }

        if (empty($this->educations) ){
           return [
               'status'=> true,
               'message' => __('validation.custom.educations_table'),
           ];
       }

        if (empty($this->specialities) ){
            return [
                'status'=> true,
                'message' => __('validation.custom.specialities_table'),
            ];
        }

        return [
            'status'=> false,
            'message' => '',
        ];

    }




}
