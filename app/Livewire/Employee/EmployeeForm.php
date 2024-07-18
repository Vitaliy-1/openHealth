<?php

namespace App\Livewire\Employee;

use App\Classes\Cipher\Api\CipherApi;
use App\Livewire\Employee\Forms\Api\EmployeeRequestApi;
use App\Livewire\Employee\Forms\EmployeeFormRequest;
use App\Models\Division;
use App\Models\Employee;
use App\Models\LegalEntity;
use App\Models\Person;
use App\Traits\FormTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class EmployeeForm extends Component
{
    use FormTrait,WithFileUploads;

    const CACHE_PREFIX = 'register_employee_form';

    public EmployeeFormRequest $employee_request;

    public  ? array $getCertificateAuthority;

    protected string $employeeCacheKey;

    public Employee $employee;

    public object $employees;

    public LegalEntity $legalEntity;

    public string $mode = 'create';

    #[Validate('required|string|max:255')]
    public string $knedp = '';

    #[Validate('required|max:1024')] // 1MB Max
    public   $keyContainerUpload;

    #[Validate('required|string|max:255')]
    public string $password = '';


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
        $this->getCertificateAuthority = (new CipherApi())->getCertificateAuthority();
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
            $base64Data =  (new CipherApi())->sendSession(
                json_encode($this->buildEmployeeRequest()),
                $this->password,
                $this->convertFileToBase64(),
                $this->knedp
            );
//        dd($base64Data);
            $data = [
//                'signed_content' =>    $base64Data,
                'signed_content' => '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',
                'signed_content_encoding' => 'base64',
            ];

            $employeeRequest = EmployeeRequestApi::createEmployeeRequest($data);

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

    public function convertFileToBase64(): ?string
    {
        if ($this->keyContainerUpload && $this->keyContainerUpload->exists()) {
            $fileExtension = $this->keyContainerUpload->getClientOriginalExtension();
            $filePath = $this->keyContainerUpload->storeAs('uploads/kep', 'kep.'.$fileExtension, 'public');
            if ($filePath) {
                $fileContents = file_get_contents(storage_path('app/public/' . $filePath));
                if ($fileContents !== false) {
                    $base64Content = base64_encode($fileContents);
                    Storage::disk('public')->delete($filePath);
                    return $base64Content;
                }
            }
        }

        return null;
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



    public function buildEmployeeRequest(): array
    {
       $employee_request = $this->employee_request->toArray();
        $data['employee_request'] = [
            'employee_type' => $employee_request['employee']['employee_type'] ?? '',
            'legal_entity_id' => Auth::user()->legalEntity->uuid ?? '',
            'position' => $employee_request['employee']['position'] ?? '',
            'start_date' => isset($employee_request['employee']['start_date']) ? Carbon::parse( $employee_request['employee']['start_date'])->format('Y-m-d') : '',
            'party' => [
                'email' => $employee_request['employee']['email'] ?? '',
                'first_name' => $employee_request['employee']['first_name'] ?? '',
                'last_name' => $employee_request['employee']['last_name'] ?? '',
                'phone' => $employee_request['employee']['phone'] ?? '',
                'tax_id' => $employee_request['employee']['tax_id'] ?? '',
                'no_tax_id' => $employee_request['employee']['no_tax_id'] ?? false,
                'gender' => $employee_request['employee']['gender'] ?? '',
                'documents' => $employee_request['employee']['documents'][0] ?? '',
                'birth_date' => isset($employee_request['employee']['birth_date']) ? Carbon::parse( $employee_request['employee']['birth_date'])->format('Y-m-d') : '',
                'working_experience' => $employee_request['employee']['working_experience'] ?? '',
                'about_myself' => $employee_request['employee']['about_myself'] ?? '',
            ],
            'doctor' => [
                'educations' => $employee_request['educations'] ?? [],
                'specialities' => $employee_request['specialities'] ?? [],
                'qualifications' => $employee_request['qualifications'] ?? [],
                'science_degree' => $employee_request['science_degree'] ?? [],
            ],
        ];

        return removeEmptyKeys($data);
    }


    public function render()
    {


        return view('livewire.employee.employee-form');
    }


}
