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

    public Employee $employee;

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
        'DOCUMENT_TYPE',
        'SPEC_QUALIFICATION_TYPE',
        'EMPLOYEE_TYPE',
        'POSITION',
    ];

    public \Illuminate\Database\Eloquent\Collection $divisions;
    public \Illuminate\Database\Eloquent\Collection $healthcareServices;

    public array $tableHeaders;
    public string $request_id;
    private mixed $employee_id;
    /**
     * @var mixed|string
     */
    public mixed $key_property;

    public function boot(): void
    {
        $this->employeeCacheKey = self::CACHE_PREFIX . '-' . Auth::user()->legalEntity->uuid;
    }

    public function mount(Request $request, $id = null)
    {
        $this->setTableHeaders();
        $this->getLegalEntity();
        $this->getDivisions();
        if ($request->has('store_id')) {
            $this->request_id = $request->input('store_id');
        }
        if (isset($id)) {
            $this->employee_id = $id;
        }

        $this->getEmployee();
        $this->getDictionary();

    }

    public function getHealthcareServices($id)
    {
        $this->healthcareServices = Division::find($id)
            ->healthcareService()
            ->get();
    }


    public function getDictionaryUnset(): array
    {
        $dictionaries = $this->dictionaries;
        if (isset($this->employee['documents']) && !empty($this->employee['documents'])) {
            foreach ($this->employee['documents'] as $k => $document) {
                unset($dictionaries['DOCUMENT_TYPE'][$document['type']]);
            }

        }
        return $this->dictionaries = $dictionaries;
    }


    public function getEmployee(): void
    {
        if ($this->hasCache() && isset($this->request_id)) {
            $employeeData = $this->getCache();
            if (isset($employeeData[$this->request_id])) {
                $this->employee = (new Employee())->forceFill($employeeData[$this->request_id]);

                if (!empty($this->employee->employee)) {
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
            $this->employee->positions = [[
                'position' => $this->employee->position,
                'start_date' => $this->employee->start_date,
            ]];
            if (!empty($this->employee)) {
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
            ->where('status', 'ACTIVE')
            ->get();
    }


    public function create($model)
    {
        $this->mode = 'create';
        $this->employee_request->{$model} = [];
        $this->openModal($model);
        $this->getEmployee();
        $this->getDictionaryUnset();
    }


    public function store($model)
    {

        $this->employee_request->rulesForModelValidate($model);

        $this->resetErrorBag();
        $cacheData = [];
        if ($this->hasCache()) {
            $cacheData = $this->getCache();
        }
        if ($model == 'employee' || $model == 'science_degree' || $model == 'positions' ) {
            $cacheData[$this->request_id][$model] = $this->employee_request->{$model};
        } else {
            $cacheData[$this->request_id][$model][] = $this->employee_request->{$model};
        }
        $this->putCache($cacheData);
        $this->closeModal();
        $this->success['status'] = true;
        $this->getEmployee();

    }

    public function edit($model,$key_property = '' )
    {

        $this->key_property = $key_property;

        $this->mode = 'edit';
        $cacheData = $this->getCache();
        $this->openModal($model);
        if (empty($key_property)) {
            $this->employee_request->{$model} = $cacheData[$this->request_id][$model];
        }
        else{
            $this->employee_request->{$model} = $cacheData[$this->request_id][$model][$key_property];
        }

        $this->getEmployee();
    }


    public function getCache(){
         return Cache::get($this->employeeCacheKey, []);
    }

    public function putCache($cacheData){
        Cache::put($this->employeeCacheKey, $cacheData, now()->days(90));
    }

    public function hasCache(){
        return Cache::has($this->employeeCacheKey);
    }



    public function update($model,$key_property)
    {

        $this->employee_request->rulesForModelValidate($model);
        $this->resetErrorBag();
        if ($this->hasCache()) {
            $cacheData = $this->getCache();
            $cacheData[$this->request_id][$model][$key_property] = $this->employee_request->{$model};
            $this->putCache($cacheData);
        }
        $this->closeModalModel($model);
    }

    public function closeModalModel($model = null): void
    {
        if (!empty($model)) {
            $this->employee_request->{$model} = [];
        }
        $this->closeModal();
        $this->getEmployee();



    }


    public function sendApiRequest()
    {

        $cacheData = $this->getCache();

        if (isset($cacheData[$this->request_id])) {
            $this->employee_request->fill($cacheData[$this->request_id]);
        }

        if ($this->employee_request->employee
            && $this->employee_request->role
            && $this->employee_request->positions
            && $this->employee_request->qualifications
            && $this->employee_request->science_degree
            && $this->employee_request->specialities
            && $this->employee_request->educations) {
            $employeeRequest = EmployeeRequestApi::createEmployeeRequest($this->legalEntity->uuid, $this->employee_request->toArray());
            $person = $this->savePerson($employeeRequest);
            $this->saveUser($employeeRequest['party'], $person);
            $this->saveEmployee($employeeRequest, $person);

            $this->forgetCacheIndex();
            return redirect(route('employee.index'));

        } else {
            $this->error['message'] = 'Заповніть всі поля';
            $this->error['status'] = true;
        }
        $this->getEmployee();
    }

    private function forgetCacheIndex()
    {

        $cacheData = $this->getCache();
        if (isset($cacheData[$this->request_id])) {
            unset($cacheData[$this->request_id]);
           $this->putCache($cacheData);
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
            'password' => Hash::make(\Illuminate\Support\Str::random(8)),
            'legal_entity_id' => $this->legalEntity->id,
        ]);
    }


    public function saveEmployee($data, $person)
    {
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
