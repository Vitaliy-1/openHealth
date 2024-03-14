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

    public Employee  $employee ;

    public object $employees;
    public LegalEntity $legalEntity;

    public string $mode = 'create';

    public array $success = [
        'message' => '',
        'status' => false,
    ] ;

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
            $employee = Employee::with('person')->find($this->employee_id);

            if (!empty($this->employee->employee)){
                $this->employee_request->fill(
                    [
                        'employee' => $this->employee->employee,
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
            $cache =   Cache::get($this->employeeCacheKey, []);

           unset($cache[$this->request_id]);
           Cache::put($this->employeeCacheKey, $cache, now()->days(90));
           $person = $this->savePerson($employeeRequest);
           $this->saveUser($employeeRequest['party'],$person);
           $this->saveEmployee($employeeRequest,$person);


           return redirect(route('employee.index'));

        } else {
            $this->error['message'] = 'Заповніть всі поля';
            $this->error['status'] = true;
        }
        $this->getEmployee();
    }
    public function render()
    {
        return view('livewire.employee.employee-form');
    }

    public function saveUser($party,$person)
    {
        $user = new User();
        $user->email = \Illuminate\Support\Str::random(3).$party['email'];
        $user->password = Hash::make( \Illuminate\Support\Str::random(8));
        $user->legal_entity_id = $this->legalEntity->id;
        $person->user()->save($user);

        return $user;
    }

    public function savePerson($data)
    {
        $person = new Person();
        $person->last_name = $data['party']['last_name'];
        $person->first_name = $data['party']['first_name'];
        $person->second_name = $data['party']['second_name'];
        $person->email = \Illuminate\Support\Str::random(3).$data['party']['email'];
        $person->phones = $data['party']['phones'];
        $person->gender = $data['party']['gender'];
        $person->birth_date = $data['party']['birth_date'];
        $person->tax_id = '32132132';
        $person->no_tax_id = $data['party']['no_tax_id'];
        $person->educations = $data['doctor']['educations'];
        $person->specialities = $data['doctor']['specialities'];
        $person->qualifications = $data['doctor']['qualifications'];
        $person->science_degree = $data['doctor']['science_degree'];

        $person->save();

        return $person;
   }

     public function saveEmployee($data,$person){
        $employee = new Employee();
        $employee->employee_type = $data['employee_type'];
        $employee->is_active = false;
        $employee->status = $data['status'];
        $employee->start_date = Carbon::now()->format('Y-m-d H:i:s');
        $employee->position = $data['position'];
        $employee->speciality =$data['doctor']['specialities'];
        $employee->legal_entity_id = $this->legalEntity->id;
        $employee->uuid = $data['id'];
        $person->employee()->save($employee);
        return $employee;
   }


}
