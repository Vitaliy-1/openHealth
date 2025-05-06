<?php

namespace App\Livewire\Employee;

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
        $currentEmployee = $this->employee;
        return view('livewire.employee.employee-edit', compact(['pageTitle', 'currentEmployee']));
    }
}
