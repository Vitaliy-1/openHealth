<?php

namespace App\Livewire\Employee;

use App\Models\Employee\Employee as Employee;

class EmployeeCreate extends EmployeeComponent
{
    public function mount(): void
    {
        parent::mount();
    }

    public function save()
    {
        $this->form->validate();
    }

    public function render()
    {
        return view('livewire.employee.employee-create');
    }
}
