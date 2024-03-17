<?php

namespace App\Livewire\Employee;

use App\Livewire\Employee\Forms\Api\EmployeeRequestApi;
use App\Livewire\Employee\Forms\EmployeeFormRequest;
use App\Models\Division;
use App\Models\Employee;
use App\Models\LegalEntity;
use App\Models\Person;
use App\Models\User;
use App\Traits\FormTrait;
use Carbon\Carbon;
use Dotenv\Util\Str;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;
use Livewire\Component;
use phpDocumentor\Reflection\Types\Integer;

class EmployeeForm extends Component
{
    use FormTrait;


    const CACHE_PREFIX = 'register_employee_form';

    public EmployeeFormRequest $employee_request;

    protected string $employeeCacheKey;

    public  Employee  $employee ;

    public object $employees;
    public LegalEntity $legalEntity;

    public string $mode = 'create';

    public array $success = [
        'message' => '',
        'status' => false,
    ];

    public ?array $error = [
        'message' => '',
        'status' => false,
    ];

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
    private mixed $employee_id;

    public function boot( ): void
    {

        $this->employeeCacheKey = self::CACHE_PREFIX . '-'. Auth::user()->legalEntity->uuid;
    }


    public function mount(Request $request, $id = null)
    {
        $this->setTableHeaders();
        $this->getLegalEntity();
        $this->getDivisions();
        $this->getDictionary();
        if ($request->has('store_id')) {
            $this->request_id = $request->input('store_id');
        }
        if (isset($id)) {
            $this->employee_id = $id;
        }

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
        if (Cache::has($this->employeeCacheKey) && isset($this->request_id)) {
            $employeeData = Cache::get($this->employeeCacheKey, []);
            if (isset($employeeData[$this->request_id])) {
               $this->employee = ( new Employee())->forceFill(Cache::get($this->employeeCacheKey, [])[$this->request_id]);
               if (!empty($this->employee->employee)){
                   $this->employee_request->fill(
                       [
                           'employee' => $this->employee->employee,
                       ]
                   );
               }
            }
        }

        if (isset($this->employee_id)) {
            $this->employee = Employee::find($this->employee_id);
            $this->employee->educations = $this->employee->doctor['educations'] ?? [];
            $this->employee->specialities = $this->employee->doctor['specialities'] ?? [];
            $this->employee->qualifications = $this->employee->doctor['qualifications'] ?? [];
            $this->employee->science_degree = $this->employee->doctor['science_degree'] ?? [];
            $this->employee->documents = $this->employee->party['documents'] ?? [];
            $this->employee->positions = [ [
                'position' => $this->employee->position,
                'start_date' => $this->employee->start_date,
            ]];
            if (!empty($this->employee)){
                $this->employee_request->fill(
                    [
                        'employee' => $this->employee->party,
                    ]
                );

            }
        }
    }

    /**
     * Set the table headers for the E-health table.
     */
    public function setTableHeaders(): void
    {
        // Define the table headers
        $this->tableHeaders = [
            __('ID E-health'),
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

        $this->success['status'] = true;

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


    public function sendApiRequest()
    {


        if (isset(Cache::get($this->employeeCacheKey, [])[$this->request_id])) {
            $this->employee_request->fill(Cache::get($this->employeeCacheKey, [])[$this->request_id]);
        }

        if ($this->employee_request->employee
            && $this->employee_request->role
            && $this->employee_request->positions
            && $this->employee_request->qualifications
            && $this->employee_request->science_degree
            && $this->employee_request->specialities
            && $this->employee_request->educations) {

           $employeeRequest =  EmployeeRequestApi::createEmployeeRequest($this->legalEntity->uuid,$this->employee_request->toArray());
           $person = $this->savePerson($employeeRequest);
           $this->saveUser($employeeRequest['party'],$person);
           $this->saveEmployee($employeeRequest,$person);

           $this->forgetCacheIndex();
           return redirect(route('employee.index'));

        } else {
            $this->error['message'] = 'Заповніть всі поля';
            $this->error['status'] = true;
        }
        $this->getEmployee();
    }

    private function forgetCacheIndex(){

        $cache = Cache::get($this->employeeCacheKey, []);
        if (isset($cache[$this->request_id])) {
            unset($cache[$this->request_id]);
            Cache::put($this->employeeCacheKey, $cache, now()->addDays(90));
        }
    }


    public function savePerson($data)
    {
        return Person::create($data['party']);
    }

    public function saveUser($party, $person)
    {
        return $person->user()->create([
            'email' => \Illuminate\Support\Str::random(3) . $party['email'],
            'password' => Hash::make( \Illuminate\Support\Str::random(8)),
            'legal_entity_id' => $this->legalEntity->id,
        ]);
    }


    public function saveEmployee($data,$person){
        $employee = new Employee();
        $employee->fill($data);
        $employee->uuid = $data['id'];
        $employee->division_uuid = $data['division_id'] ?? null;
        $employee->legal_entity_uuid = $data['legal_entity_id'] ?? null;
        $employee->legal_entity_id = $this->legalEntity->id;
        $person->employee()->save($employee);
        return $employee;
   }

    public function render()
    {
        return view('livewire.employee.employee-form');
    }


}
