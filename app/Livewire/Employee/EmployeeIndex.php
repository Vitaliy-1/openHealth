<?php

namespace App\Livewire\Employee;

use App\Models\Employee;
use Livewire\Component;

class EmployeeIndex extends Component
{

    public object $employees;
    public array $tableHeaders = [];


    public function mount()
    {
        $this->tableHeaders();
//        $this->employees =auth()->user()->legalEntity-;
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
    public function create()
    {


        return $this->redirect(route('employee.form'));

    }

    public function render()
    {
        return view('livewire.employee.employee-index');
    }
}
