<?php

namespace App\Livewire\Employee;

use App\Helpers\JsonHelper;
use App\Models\Employee;
use App\Models\LegalEntity;
use Livewire\Attributes\Validate;
use Livewire\Component;

class EmployeeForm extends Component
{
    #[Validate([
//        'employee.last_name' => 'required|min:3',
//        'employee.first_name' => 'required|min:3',
//        'employee.second_name' => 'required|min:3',
//        'employee.gender' => 'required|string',
//        'employee.birth_date' => 'required|date',
//        'employee.no_tax_id' => 'boolean',
//        'employee.tax_id' => 'exclude_if:employee.no_tax_id,true|required|integer|digits:10',
//        'employee.documents.type' => 'exclude_if:employee.no_tax_id,false|required|string',
//        'employee.documents.number' => 'exclude_if:employee.no_tax_id,false|required|string',
//        'employee.phones.*.number' => 'required|string:digits:13',
//        'employee.phones.*.type' => 'required|string',
//        'employee.email' => 'required|email',
//        'employee.educations.country' => 'required|string',
//        'employee.educations.city' => 'required|string',
//        'employee.educations.institution_name' => 'required|string',
//        'employee.educations.diploma_number' => 'required|string',
//        'employee.educations.degree' => 'required|string',
//        'employee.educations.speciality' => 'required|string',
//        'employee.position' => 'required|string'
    ])]




    public string $view = 'employee.employee-index';

    public ?array  $employee = [];

    public   object $employees;
    public LegalEntity $legalEntity;

    public array $phones = ['type' => '', 'number' => ''];

    public string $mode = 'create';

    public ?array $dictionaries = [];

    public string|bool $showModal = false;

    public function mount()
    {
        $this->tableHeaders();
        $this->getLegalEntity();
        $this->dictionaries = JsonHelper::searchValue('DICTIONARIES_PATH', [
            'PHONE_TYPE',
            'COUNTRY',
            'SETTLEMENT_TYPE',
            'DIVISION_TYPE',
            'SPECIALITY_LEVEL',
            'GENDER',
            'SPEC_QUALIFICATION_TYPE',
            'POSITION',
        ]);
    }

    public function tableHeaders(): void
    {
        $this->tableHeaders = [
            __('ID E-health '),
            __('ПІБ'),
            __('Телефон'),
            __('Email'),
            __('Посада'),
            __('Статус'),
            __('Дія'),
        ];
    }

    public function getLegalEntity()
    {
        $this->legalEntity = auth()->user()->legalEntity;
    }

    public function getEmployees()
    {
        $this->employees = $this->legalEntity->employee()->get();
    }

    public function store()
    {
        $this->validate();

        $this->closeModal();

        return $this->redirect(route('employee.index'));
    }


    public function createPasportData($modal)
    {
        $this->mode = 'create';

        $this->openModal($modal);
    }

    public function createEducation($modal)
    {

        $this->openModal($modal);

    }

    public function createSpeciality($modal)
    {
        $this->mode = 'create';
        $this->openModal($modal);
    }
    public function edit(Employee $employee)
    {
        $this->mode = 'edit';
        $this->employee = $employee;
        $this->openModal();
    }


    public function update(Employee $employee)
    {
        $this->validate([
            'employee.name' => 'required',
            'employee.email' => 'required|email',
            'employee.position' => 'required',
        ]);

        $this->employee->save();
        $this->closeModal();
    }

    public function openModal($modal)
    {
        $this->showModal = $modal;
        $this->phones = [];
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->phones = [];
    }

    public function addRowPhone(): array
    {
        return $this->phones[] = ['type' => '', 'number' => ''];
    }

    public function removePhone($key)
    {
        if (isset($this->phones[$key])) {
            unset($this->phones[$key]);
        }
    }

    public function render()
    {
        return view('livewire.employee.employee-form');
    }
}
