<?php

namespace App\Livewire\Employee;

use App\Models\Employee\Employee as Employee;

class EmployeeEdit extends EmployeeComponent
{
    protected Employee $employee;

    public function mount(int|\App\Services\LegalEntityContext $id = null): void
    {
        $this->employee = Employee::findOrFail($id);

        parent::mount();
    }

    public function render()
    {
        $pageTitle = __('forms.edit_employee' . ' : ' . $this->employee->getFullNameAttribute());

        return view('livewire.employee.employee-edit', compact('pageTitle'));
    }
}
