<?php

namespace App\Livewire\Employee;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class EmployeeIndex extends Component
{

    const CACHE_PREFIX = 'register_employee_form';

    public object $employees;
    public array $tableHeaders = [];

    protected string $employeeCacheKey;
    public int $storeId = 0;
    public \Illuminate\Support\Collection $employeesCache;


    public function boot(Employee $employee): void
    {
        $this->employeeCacheKey = self::CACHE_PREFIX . '-'. Auth::user()->legalEntity->uuid;
    }


    public function mount()
    {
        $this->tableHeaders();
        $this->getLastStoreId();
        $this->getEmployees();
//        $this->employees =auth()->user()->legalEntity-;
    }

    public function getLastStoreId()
    {
        if (Cache::has($this->employeeCacheKey) && !empty(Cache::get($this->employeeCacheKey))) {
            $this->storeId = array_key_last(Cache::get($this->employeeCacheKey));
        }
        $this->storeId ++;
    }

    public function getEmployeesCache(): void
    {
        if (Cache::has($this->employeeCacheKey)) {
            $this->employeesCache = collect(Cache::get($this->employeeCacheKey))->map(function ($data) {
                return (new Employee())->forceFill($data);
            });
        }
    }

    public function getEmployees()
    {
        $this->employees = Auth::user()->legalEntity->employee;
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

        return redirect()->route('employee.form', ['id' => $this->storeId]);

    }

    public function render()
    {
        return view('livewire.employee.employee-index');
    }
}
