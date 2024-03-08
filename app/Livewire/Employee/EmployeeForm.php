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
use phpDocumentor\Reflection\Types\Integer;

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

    public $sucess = false;
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
    public string  $request_id;

    public function boot( ): void
    {

        $this->employeeCacheKey = self::CACHE_PREFIX . '-'. Auth::user()->legalEntity->uuid;
    }


    public function mount(Request $request)
    {
        $this->tableHeaders();
        $this->getLegalEntity();
        $this->getDivisions();
        $this->getDictionary();
        $this->request_id = $request->input('id');

        $this->getEmployee();

    }

    public function getHealthcareServices($id)
    {
        $this->healthcareServices = Division::find($id)
            ->healthcareService()
            ->get();
    }


    public function getEmployee(): void
    {
        if (Cache::has($this->employeeCacheKey)){
            $employeeData = Cache::get($this->employeeCacheKey, []);
            if (isset($employeeData[$this->request_id])) {
               $this->employee = ( new Employee())->forceFill(Cache::get($this->employeeCacheKey, [])[$this->request_id]);

                $this->employee_request->fill(
                   [
                       'employee' => $this->employee->employee,
                   ]
               );
            }
        }
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




    public function updateRow($object){

    }

    public function create($model)
    {
        $this->openModal($model);
        $this->getEmployee();
    }

    public function store($model)
    {

        $this->employee_request->rulesForModelValidate($model);
        $this->resetErrorBag();
        $cacheData = [];

        if (Cache::has($this->employeeCacheKey)){
            $cacheData =  Cache::get($this->employeeCacheKey, []);
        }

        if ($model == 'employee') {
            $cacheData[$this->request_id][$model] = $this->employee_request->{$model};
        } else {
            $cacheData[$this->request_id][$model][] = $this->employee_request->{$model};
        }


        Cache::put($this->employeeCacheKey, $cacheData, now()->days(90));

        $this->closeModal();
        $this->sucess = true;
        $this->getEmployee();

    }

    public function edit( $k, $model)
    {
        $this->openModal($model);
        $this->employee_request->{$model} = Cache::get($this->employeeCacheKey, [])[$this->request_id][$model][$k] ;
        $this->getEmployee();
    }

    public function closeModalModel(): void
    {
        $this->closeModal();
        $this->getEmployee();

    }


    public function sendApiRequest($model)
    {

        if (Cache::has($this->employeeCacheKey)) {
            $cacheData = Cache::get($this->employeeCacheKey, []);
        }

        if ($model == 'employee') {
            $cacheData[$this->request_id][$model] = $this->employee_request->{$model};
        } else {
            $cacheData[$this->request_id][$model][] = $this->employee_request->{$model};
        }

    }
    public function render()
    {
        return view('livewire.employee.employee-form');
    }
}
