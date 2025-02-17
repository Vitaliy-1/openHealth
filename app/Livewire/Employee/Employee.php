<?php

namespace App\Livewire\Employee;

use App\Traits\FormTrait;
use Livewire\Component;
use App\Livewire\Employee\Forms\EmployeeForm as Form;

class Employee extends Component
{
    use FormTrait;

    public Form $form;

    /**
     * @var array|string[] Set dictionaries to load with the component
     */
    public ?array $dictionaries_field = [
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

    public function mount()
    {
        $this->getDictionary();
    }
}
