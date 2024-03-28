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
    public string $employee_id;
    /**
     * @var mixed|string
     */
    public mixed $key_property;

    public function boot(): void
    {
        $this->employeeCacheKey = self::CACHE_PREFIX . '-' . Auth::user()->legalEntity->uuid;
    }

    public function mount(Request $request, $id = '')
    {
        $this->setTableHeaders();
        $this->getLegalEntity();
        $this->getDivisions();

        if ($request->has('store_id')) {
            $this->request_id = $request->input('store_id');
        }
        if (!empty($id)) {

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


    public function dictionaryUnset(): array
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

        if (isset($this->employee_id)) {
             $employeeData = Employee::find($this->employee_id);
            if (empty($employeeData)) {
                abort(404);
            }

            $this->employee = $employeeData;
            $this->employee->educations = $this->employee->doctor['educations'] ?? [];
            $this->employee->specialities = $this->employee->doctor['specialities'] ?? [];
            $this->employee->qualifications = $this->employee->doctor['qualifications'] ?? [];
            $this->employee->science_degree = $this->employee->doctor['science_degree'] ?? [];

//            dd($this->employee->science_degree);
            $this->employee->documents = $this->employee->party['documents'] ?? [];
            if (!empty($this->employee)) {
                $this->employee_request->fill(
                    [
                        'employee' => $this->employee->party,

                    ],
                );
                $this->employee_request->employee['position'] = $this->employee->position;
                $this->employee_request->employee['start_date'] = $this->employee->start_date;
                $this->employee_request->employee['employee_type'] = $this->employee->employee_type;

            }
        }

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
        $this->dictionaryUnset();
    }


    public function store($model)
    {

        $this->employee_request->rulesForModelValidate($model);

        $this->resetErrorBag();

        if (isset($this->request_id)) {
            $this->storeCacheEmployee($model);
        }
        if (isset($this->employee_id)) {
            $this->storeEmployee($model);
        }

        $this->closeModalModel();
        $this->success['status'] = true;
        $this->getEmployee();

    }


    public function storeCacheEmployee($model)
    {
        $cacheData = [];
        if ($this->hasCache()) {
            $cacheData = $this->getCache();
        }
        if ($model == 'employee' || $model == 'science_degree' || $model == 'positions') {
            $cacheData[$this->request_id][$model] = $this->employee_request->{$model};
        } else {
            $cacheData[$this->request_id][$model][] = $this->employee_request->{$model};
        }
        $this->putCache($cacheData);
    }

    public function storeEmployee($model)
    {
        if ($model == 'employee') {
            $this->employee->position = $this->employee_request->employee['position'];
            $this->employee->start_date = $this->employee_request->employee['start_date'];
            $this->employee->party = $this->employee_request->employee;
        } elseif ($model == 'documents') {
            $party = $this->employee->party;
            $party['documents'][] = $this->employee_request->documents;
            $this->employee->party = $party;
        } elseif ($model == 'science_degree') {
            $doctor = $this->employee->doctor;
            $doctor['science_degree'] = $this->employee_request->science_degree;
            $this->employee->doctor = $doctor;
        }
        else {
            $doctor = $this->employee->doctor;
            $doctor[$model][] = $this->employee_request->{$model};
            $this->employee->doctor = $doctor;
        }
        $this->employee->save();

    }


    public function edit($model, $key_property = '')
    {

        $this->key_property = $key_property;
        $this->mode = 'edit';
        $this->openModal($model);
        if (isset($this->request_id)) {
            $this->editCacheEmployee($model, $key_property);
        }
        if (isset($this->employee_id)) {

            $this->editEmployee($model, $key_property);
        }

        $this->getEmployee();


    }


    public function editCacheEmployee($model, $key_property = '')
    {
        $cacheData = $this->getCache();

        if (empty($key_property) && $key_property !== 0) {
            $this->employee_request->{$model} = $cacheData[$this->request_id][$model];
        } else {
            $this->employee_request->{$model} = $cacheData[$this->request_id][$model][$key_property];
        }
    }

    public function editEmployee($model, $key_property = '')
    {
        if ($model == 'documents') {
            $this->employee_request->{$model} = $this->employee->party[$model][$key_property];
        }
        elseif ($model == 'science_degree') {
            $this->employee_request->{$model} = $this->employee->doctor[$model];
        }
        else{
            $this->employee_request->{$model} = $this->employee->doctor[$model][$key_property];
        }


    }


    public function getCache()
    {
        return Cache::get($this->employeeCacheKey, []);
    }

    public function putCache($cacheData)
    {
        Cache::put($this->employeeCacheKey, $cacheData, now()->days(90));
    }

    public function hasCache()
    {
        return Cache::has($this->employeeCacheKey);
    }

    public function update($model, $key_property)
    {

        $this->employee_request->rulesForModelValidate($model);
        $this->resetErrorBag();
        if (isset($this->request_id)) {
            $this->updateCacheEmployee($model, $key_property);
        }
        if (isset($this->employee_id)) {
            $this->updateEmployee($model, $key_property);
        }
        $this->closeModalModel($model);
    }

    public function updateCacheEmployee($model, $key_property)
    {
        if ($this->hasCache()) {
            $cacheData = $this->getCache();
            $cacheData[$this->request_id][$model][$key_property] = $this->employee_request->{$model};
            $this->putCache($cacheData);
        }

    }


    public function updateEmployee($model, $key_property)
    {
        if ($model === 'documents') {
            $party = $this->employee->party;
            $party[$model][$key_property] = $this->employee_request->{$model};
            $this->employee->party = $party;
        }

        else {
            $doctor = $this->employee->doctor;
            $doctor[$model][$key_property] = $this->employee_request->{$model};
            $this->employee->doctor = $doctor;
        }

        $this->employee->save();

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

        if (isset($this->request_id) && isset($cacheData[$this->request_id])) {
            $this->employee_request->fill($cacheData[$this->request_id]);
        }

        if (isset($this->employee_id)) {
            $this->employee_request->fill($this->employee->toArray());
            $this->employee_request->documents = $this->employee->party['documents'];
            $this->employee_request->role = $this->employee->party['documents'];
        }

        $error = $this->employee_request->validateBeforeSendApi();

        if (!$error['status']) {
            $employeeRequest = EmployeeRequestApi::createEmployeeRequest($this->legalEntity->uuid, $this->employee_request->toArray());

            if (isset($this->request_id)) {
                $person = $this->savePerson($employeeRequest);
                $this->saveUser($employeeRequest['party'], $person);
                $this->saveEmployee($employeeRequest, $person);
                $this->forgetCacheIndex();
            }
            unset($employeeRequest['id']);
            unset($employeeRequest['legal_entity_id']);
            unset($employeeRequest['division_id']);
            $this->employee->update($employeeRequest);

            return redirect(route('employee.index'));


        } else {
            $this->error['status'] = $error['status'];
            $this->error['message'] = $error['message'];
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
