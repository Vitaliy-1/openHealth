<?php

namespace App\Livewire\Employee;

use App\Classes\Cipher\Api\CipherApi;
use App\Livewire\Employee\Forms\Api\EmployeeRequestApi;
use App\Livewire\Employee\Forms\EmployeeFormRequest;
use App\Models\Division;
use App\Models\Employee;
use App\Models\LegalEntity;
use App\Models\Person;
use App\Models\User;
use App\Traits\Cipher;
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
    use FormTrait,Cipher,WithFileUploads;

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
        'SPECIALITY_TYPE',
        'DIVISION_TYPE',
        'SPECIALITY_LEVEL',
        'GENDER',
        'QUALIFICATION_TYPE',
        'SCIENCE_DEGREE',
        'DOCUMENT_TYPE',
        'SPEC_QUALIFICATION_TYPE',
        'EMPLOYEE_TYPE',
        'POSITION',
        'EDUCATION_DEGREE',
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


    public ?object  $file = null;


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
        $this->setCertificateAuthority();
        $this->getEmployee();
        $this->getDictionary();
        $this->getEmployeeDictionaryRole();
        $this->getEmployeeDictionaryPosition();
    }

    public function getHealthcareServices($id)
    {
        $this->healthcareServices = Division::find($id)
            ->healthcareService()
            ->get();
    }

    public function setCertificateAuthority(): array|null
    {
        return $this->getCertificateAuthority = $this->getCertificateAuthority();
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
                            'documents' => $this->employee->documents?? [],
                            'educations' => $this->employee->educations?? [],
                            'specialities' => $this->employee->specialities?? [],
                            'qualifications' => $this->employee->qualifications?? [],
                            'science_degree' => $this->employee->science_degree?? [],
                        ]
                    );
                }
            }
        }

    }



    public function updatedFile(): void{
        $this->keyContainerUpload = $this->file;
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

    public function openModalModel($model)
    {
        $this->showModal = $model;
    }

    public function create($model)
    {

        $this->mode = 'create';
        $this->employee_request->{$model} = [];
        $this->openModal($model);
        $this->getEmployee();
        $this->dictionaryUnset();
    }


    public function signdeComplete($model){
        $this->getEmployee();
        $open = $this->employee_request->validateBeforeSendApi();
        if ($open['error'] ) {
            $this->dispatch('flashMessage', ['message' => $open['message'], 'type' => 'error']);
        }
        else{
            $this->openModal($model);
        }


    }
    public function updated($field)
    {
        if ($field === 'keyContainerUpload'){
          $this->getEmployee();
        }
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

        $this->dispatch('flashMessage', ['message' => __('Інформацію успішно оновлено'), 'type' => 'success']);

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
            $this->employee->employee_type = $this->employee_request->employee['employee_type'];
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
        unset($this->employee->educations,
            $this->employee->specialities,
            $this->employee->qualifications,
            $this->employee->science_degree,
            $this->employee->documents,
        );

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
            $this->employee_request->science_degree = $this->employee->doctor['science_degree'] ?? [];
            $this->employee_request->specialities = $this->employee->doctor['specialities'] ?? [];
            $this->employee_request->educations = $this->employee->doctor['educations'] ?? [];
            $this->employee_request->qualifications = $this->employee->doctor['qualifications'] ?? [];

        }


        $error = $this->employee_request->validateBeforeSendApi();


         if (!$error['error']) {
            $base64Data =  $this->sendEncryptedData($this->buildEmployeeRequest());

             if (isset($base64Data['errors'])) {
                 $this->dispatch('flashMessage', [
                     'message' => $base64Data['errors'],
                     'type'    => 'error'
                 ]);
                 return;
             }

            $data = [
                'signed_content' =>    $base64Data,
                'signed_content_encoding' => 'base64',
            ];
            $employeeRequest = EmployeeRequestApi::createEmployeeRequest($data);

            if (isset($this->request_id)) {
                $this->saveUser($employeeRequest);
                $this->saveEmployee($employeeRequest);
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

    /**
     * Save a new user with the provided data.
     *
     * @param array $data The data to create the user with.
     */

    public function saveUser(array $data)
    {
        $user =  User::create([
            'email' =>  $data['party']['email'],
            'password' => Hash::make(\Illuminate\Support\Str::random(8)),
        ]);
        $user->assignRole($data['employee_type']);
        $user->legalEntity()->associate($this->legalEntity);
        $user->save();
    }

    /**
     * Save an employee record based on the provided data.
     *
     * @param array $data The data to fill the employee record with.
     * @return Employee The saved employee record.
     */
    public function saveEmployee(array $data)
    {
        $employee = new Employee();
        $employee->fill($data);
        $employee->uuid = $data['id'];
        $employee->division_uuid = $data['division_id'] ?? null;
        $employee->legal_entity_uuid = $data['legal_entity_id'] ?? null;
        $employee->legal_entity_id = $this->legalEntity->getId();
        return $employee;
    }



    public function buildEmployeeRequest(): array
    {
        $employee_request = $this->employee_request->toArray();

        $data['employee_request'] = [
            'employee_type' => $employee_request['employee']['employee_type'] ?? '',
            'employee_id' => $this->employee->uuid ?? '',
            'legal_entity_id' => $this->legalEntity->uuid ?? '',
            'position' => $employee_request['employee']['position'] ?? '',
            'status' => 'NEW',
            'start_date' => isset($employee_request['employee']['start_date']) ? Carbon::parse( $employee_request['employee']['start_date'])->format('Y-m-d') : '',
            'party' => [
                'email' => $employee_request['employee']['email'] ?? '',
                'first_name' => $employee_request['employee']['first_name'] ?? '',
                'last_name' => $employee_request['employee']['last_name'] ?? '',
                'phones' => $employee_request['employee']['phones'] ?? '',
                'tax_id' => $employee_request['employee']['tax_id'] ?? '',
                'no_tax_id' => $employee_request['employee']['no_tax_id'] ?? false,
                'gender' => $employee_request['employee']['gender'] ?? '',
                'documents' => $employee_request['documents'] ?? '',
                'birth_date' => isset($employee_request['employee']['birth_date']) ? Carbon::parse( $employee_request['employee']['birth_date'])->format('Y-m-d') : '',
                'working_experience' => (int)$employee_request['employee']['working_experience'] ?? '',
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


    /*
     * Include functions after  getDictionary
     * @return array
     */
    public function getEmployeeDictionaryRole(): array {
        $validRoles = ['OWNER', 'ADMIN', 'DOCTOR', 'HR'];

        $filteredRoles = array_filter($this->dictionaries['EMPLOYEE_TYPE'], function($key) use ($validRoles) {
            return in_array($key, $validRoles);
        }, ARRAY_FILTER_USE_KEY);

        return $this->dictionaries['EMPLOYEE_TYPE'] = $filteredRoles;
    }


    public function getEmployeeDictionaryPosition(): array {

        $validPositions = ["P3", "P274", "P93", "P202", "P215", "P159", "P118", "P46", "P54", "P99", "P109", "P96", "P245", "P279", "P63", "P123", "P17", "P62", "P45", "P10", "P74", "P37", "P114", "P127", "P214", "P179", "P156", "P145", "P103", "P115", "P126", "P120", "P268", "P110", "P43", "P130", "P203", "P81", "P273", "P95", "P191", "P42", "P38", "P105", "P23", "P197", "P154", "P65", "P58", "P175", "P61", "P98", "P13", "P177", "P173", "P72", "P256", "P178", "P153", "P212", "P53", "P48", "P7", "P106", "P122", "P52", "P158", "P15", "P22", "P39", "P92", "P112", "P71", "P164", "P170", "P266", "P224", "P270", "P78", "P242", "P160", "P2", "P213", "P152", "P26", "P247", "P192", "P36", "P67", "P181", "P124", "P73", "P228", "P55", "P117", "P249", "P91", "P70", "P231", "P229", "P97", "P167", "P169", "P238", "P149", "P150", "P128", "P64", "P51", "P83", "P44", "P241", "P4", "P50", "P250", "P116", "P185", "P276", "P76", "P40", "P69", "P84", "P82", "P176", "P174", "P278", "P155", "P9", "P257", "P29", "P252", "P243", "P24", "P180", "P166", "P201", "P16", "P200", "P210", "P34", "P272", "P168", "P275", "P194", "P165", "P146", "P151", "P111", "P85", "P265", "P87", "P246", "P6", "P77", "P41", "P204", "P94", "P240", "P79", "P14", "P216", "P32", "P59", "P230", "P1", "P88", "P248", "P172", "P75", "P113", "P196", "P28", "P129", "P206", "P57", "P162", "P35", "P107", "P184", "P68", "P131", "P189", "P211", "P60", "P25", "P56", "P161", "P5", "P89", "P188", "P183", "P100", "P47", "P269", "P66", "P8", "P207", "P255", "P119", "P90", "P86", "P27", "P199", "P108", "P163", "P157", "P277", "P11"];
        $filterPosition = array_filter($this->dictionaries['POSITION'], function($key) use ($validPositions) {
            return in_array($key, $validPositions);
        }, ARRAY_FILTER_USE_KEY);
        return $this->dictionaries['POSITION'] = $filterPosition;

    }

    public function render()
    {


        return view('livewire.employee.employee-form');
    }


}
