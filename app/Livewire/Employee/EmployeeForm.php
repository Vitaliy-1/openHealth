<?php

namespace App\Livewire\Employee;

use App\Livewire\Employee\Forms\EmployeeFormRequest;
use App\Models\Division;
use App\Models\Employee;
use App\Models\LegalEntity;
use App\Traits\FormTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class EmployeeForm extends Component
{
    use FormTrait;


    const CACHE_PREFIX = 'register_employee_form';

    public EmployeeFormRequest $employee_request;

    protected string $employeeCacheKey;

    public Employee  $employee ;

    public object $employees;
    public LegalEntity $legalEntity;

    public string $mode = 'create';

    public ?array $dictionaries_field = [
        'PHONE_TYPE',
        'COUNTRY',
        'SETTLEMENT_TYPE',
        'DIVISION_TYPE',
        'SPECIALITY_LEVEL',
        'GENDER',
        'QUALIFICATION_TYPE',
        'SCIENCE_DEGREE',
        'SPEC_QUALIFICATION_TYPE',
        'EMPLOYEE_TYPE',
        'POSITION',
    ];

    public \Illuminate\Database\Eloquent\Collection $divisions;
    public \Illuminate\Database\Eloquent\Collection $healthcareServices;

    public array $tableHeaders;

    public function boot(Employee $employee ): void
    {
        $this->employee = $employee;
        $this->employeeCacheKey = self::CACHE_PREFIX . '-'. Auth::user()->legalEntity->uuid;
    }
    public function mount(Employee $employee,Request $request)
    {
        $this->tableHeaders();
        $this->getLegalEntity();
        $this->getDivisions();
        $this->getDictionary();
        if (Cache::has($this->employeeCacheKey)){
            $employeeData = Cache::get($this->employeeCacheKey, []);
            if (isset($employeeData[$request->input('id')])) {
                $this->employee_request->fill(Cache::get($this->employeeCacheKey, [])[$request->input('id')]);
            }
        }


    }

    public function getHealthcareServices($id)
    {
        $this->healthcareServices = Division::find($id)
            ->healthcareService()
            ->get();
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

    public function getDivisions()
    {
        $this->divisions = $this->legalEntity->division()
            ->where('status','ACTIVE')
            ->get();
    }

    public function getEmployees()
    {
        if ($this->legalEntity->employee()->exists()){
            $this->employees = $this->legalEntity->employee()->get();
        }
        if ($this->legalEntity->employee()->doesntExist()){
            $this->employees = new Employee();
        }
    }


    public function updateRow($object){

    }

    public function store($model)
    {
        $this->resetErrorBag();
        $cacheData[] = $this->employee_request->toArray();
        Cache::put($this->employeeCacheKey, $cacheData, now()->days(90));
        $this->employee_request->rulesForModelValidate($model);

    }


    public function edit(Employee $employee)
    {

    }



    public function render()
    {
        return view('livewire.employee.employee-form');
    }
}
