<?php

namespace App\Livewire\Employee;

use App\Livewire\Employee\Forms\Api\EmployeeRequestApi;
use App\Models\Employee\Employee as Employee;

class EmployeeEdit extends EmployeeComponent
{
    protected Employee $employee;

    public function mount(int $id = null): void
    {
        $this->employee = Employee::findOrFail($id);

        parent::mount();
    }

    public function render()
    {
        $pageTitle =  __('Змінити дані по співробітнику');
        $currentEmployee = EmployeeRequestApi::getEmployees($this->employee->legalEntityUuid);

        $pageTitle =  dd($currentEmployee[0]);

        return view('livewire.employee.employee-edit', compact(['pageTitle', 'currentEmployee']));
    }
}
