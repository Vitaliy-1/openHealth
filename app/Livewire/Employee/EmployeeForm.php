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


//        $data = [
//            'signed_content' => 'MIJH3AYJKoZIhvcNAQcCoIJHzTCCR8kCAQExDjAMBgoqhiQCAQEBAQIBMIIDFwYJKoZIhvcNAQcBoIIDCASCAwR7CiAgImVtcGxveWVlX3JlcXVlc3QiOiB7CiAgICAicG9zaXRpb24iOiAiUDE2NCIsCiAgICAic3RhcnRfZGF0ZSI6ICIyMDI0LTA2LTI1IiwKICAgICJzdGF0dXMiOiAiTkVXIiwKICAgICJlbXBsb3llZV90eXBlIjogIkFETUlOIiwKICAgICJwYXJ0eSI6IHsKICAgICAgImZpcnN0X25hbWUiOiAi0KLQsNGA0LDQvSIsCiAgICAgICJsYXN0X25hbWUiOiAi0JLQsNC70LjQtNC40YHQu9Cw0LIiLAogICAgICAic2Vjb25kX25hbWUiOiAi0J7QtdC70LrRgdCw0L3QtNGA0L7QstC40YciLAogICAgICAibm9fdGF4X2lkIjogZmFsc2UsCiAgICAgICJ0YXhfaWQiOiAiMzI1NzYyMTA1MCIsCiAgICAgICJlbWFpbCI6ICJ2bGFkcHJvZm1ldEBnbWFpbC5jb20iLAogICAgICAiYmlydGhfZGF0ZSI6ICIxOTg5LTAzLTEwIiwKICAgICAgImdlbmRlciI6ICJNQUxFIiwKICAgICAgInBob25lcyI6IFsKICAgICAgICB7CiAgICAgICAgICAidHlwZSI6ICJNT0JJTEUiLAogICAgICAgICAgIm51bWJlciI6ICIrMzgwOTM1OTA3NDA2IgogICAgICAgIH0KICAgICAgXSwKICAgICAgImRvY3VtZW50cyI6IFsKICAgICAgICB7CiAgICAgICAgICAidHlwZSI6ICJQQVNTUE9SVCIsCiAgICAgICAgICAibnVtYmVyIjogItCc0JUxMjA1MTgiCiAgICAgICAgfQogICAgICBdLAoKICAgICAgIndvcmtpbmdfZXhwZXJpZW5jZSI6IDEwLAogICAgICAiYWJvdXRfbXlzZWxmIjogItCX0LDQutGW0L3Rh9C40LIg0LLRgdGWINC80L7QttC70LjQstGWINC60YPRgNGB0LgiCiAgICB9CiAgfQp9oIIFozCCBZ8wggVHoAMCAQICFF6YTVJvgvOPBAAAAOFJ/gDAiyYFMA0GCyqGJAIBAQEBAwEBMIG+MSkwJwYD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',
//            'signed_content_encoding' => 'base64',
//        ];
//
//        $employeeRequest = EmployeeRequestApi::getEmployees('f13ab4b7-1167-4215-9fb3-2116b775ddb1');
////        $employeeRequest = EmployeeRequestApi::getEmployeeRolesById();
//        dd($employeeRequest);
        return view('livewire.employee.employee-form');
    }


}
