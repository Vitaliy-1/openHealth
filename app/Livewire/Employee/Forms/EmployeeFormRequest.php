<?php

namespace App\Livewire\Employee\Forms;

use App\Rules\AgeCheck;
use App\Rules\Cyrillic;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EmployeeFormRequest extends Form
{
    #[Validate([
        'employee.last_name' => ['required', 'min:3', new Cyrillic()],
        'employee.first_name' => ['required', 'min:3', new Cyrillic()],
        'employee.gender' => 'required|string',
        'employee.birth_date' => ['required', 'date', new AgeCheck()] ,
        'employee.phones.*.number' => 'required|string:digits:13',
        'employee.phones.*.type' => 'required|string',
        'employee.email' => 'required|unique:users,email',
        'employee.position' => 'required|string',
        'employee.tax_id' => 'required|min:8|max:10',
        'employee.employee_type' => 'required|string',
    ])]

    public ?array $employee = [];

    #[Validate([
        'documents.type' => 'required|string',
        'documents.number' => 'required|string',
    ])]

    public ?array $documents = [];

    #[Validate([
        'educations.country' => 'required|string',
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
                'error'=> true,
                'message' => __('validation.custom.documents_empty'),
            ];
        }

        if (isset($this->employee['tax_id']) && empty($this->employee['tax_id'])){
           return [
               'error'=> true,
               'message' => __('validation.custom.documents_empty'),
           ];
        }
        if ( isset($this->employee['employee_type']) && $this->employee['employee_type'] == 'DOCTOR' && empty($this->specialities)){
            return [
                'error'=> true,
                'message' => __('validation.custom.specialities_table'),
            ];        }

        if (isset($this->employee['employee_type'])  && $this->employee['employee_type'] == 'DOCTOR' && empty($this->educations) ){
            return [
                'error'=> true,
                'message' => __('validation.custom.educations_table'),
            ];
        }
        return [
            'error'=> false,
            'message' => '',
        ];

    }




}
