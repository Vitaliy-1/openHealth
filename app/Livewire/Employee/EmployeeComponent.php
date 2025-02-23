<?php

namespace App\Livewire\Employee;

use App\Models\LegalEntity;
use App\Repositories\EmployeeRepository;
use App\Traits\FormTrait;
use App\Models\Employee\Employee;
use Livewire\Component;
use App\Livewire\Employee\Forms\EmployeeForm as Form;

class EmployeeComponent extends Component
{
    use FormTrait {
        getDictionary as traitGetDictionary;
    }

    public Form $form;

    /**
     * TODO remove when Repo class is implemented
     */
    protected EmployeeRepository $employeeRepository;

    /*
     * Legal entity instance associated with the logged in user
     */
    protected LegalEntity $legalEntity;

    /**
     * @var array|string[] Set dictionaries to load with the component
     */
    public ?array $dictionaryNames = [
        'PHONE_TYPE',
        'COUNTRY',
        'SETTLEMENT_TYPE',
        'SPECIALITY_TYPE',
        'DIVISION_TYPE',
        'SPECIALITY_LEVEL',
        'GENDER',
        'QUALIFICATION_TYPE',
        'SCIENCE_DEGREE',
        'DOCUMENT_TYPE',
        'SPEC_QUALIFICATION_TYPE',
        'EMPLOYEE_TYPE',
        'POSITION',
        'EDUCATION_DEGREE',
        'EMPLOYEE_TYPE',
    ];

    /*
     * Holds information about relation between employee type, e.g., ADMIN - administrator and position, like P5 - head of dept;
     * See config/ehealth.php employee_type for more details
     */
    public array $employeeTypePosition = [];

    public function mount(): void
    {
        $this->getDictionary();
        $this->legalEntity = auth()->user()->legalEntity;
    }

    public function boot(EmployeeRepository $employeeRepository): void
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Override FormTrait method to filter dictionary data to specific entity type
     */
    protected function getDictionary(): void
    {
        $this->traitGetDictionary();

        $this->dictionaries['EMPLOYEE_TYPE'] = $this->getDictionariesFields(
            config('ehealth.legal_entity_type.' . auth()->user()->legalEntity->type .'.roles'),
            'EMPLOYEE_TYPE'
        );

        // Employee can have only those positions which are allowed for his type/role
        foreach ($this->dictionaries['EMPLOYEE_TYPE'] as $employeeType => $description) {
            $keys = config("ehealth.employee_type.{$employeeType}.position", []);
            $this->employeeTypePosition[$employeeType] = $this->getDictionariesFields($keys, 'POSITION');
        }
    }
}
