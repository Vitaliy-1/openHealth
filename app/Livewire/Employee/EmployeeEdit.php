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
        return view('livewire.employee.employee-edit');
    }
}
