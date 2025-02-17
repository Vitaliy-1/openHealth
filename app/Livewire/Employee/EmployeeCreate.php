<?php

namespace App\Livewire\Employee;

class EmployeeCreate extends Employee
{
    public function save()
    {
        $this->form->validate();
    }

    public function render()
    {
        return view('livewire.employee.employee-create');
    }
}
