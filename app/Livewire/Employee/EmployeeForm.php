<?php

namespace App\Livewire\Employee;

use App\Classes\eHealth\Api\EmployeeApi;
use App\Livewire\Employee\Forms\Api\EmployeeRequestApi;
use App\Livewire\Employee\Forms\EmployeeFormRequest;
use App\Models\Division;
use App\Models\Employee;
use App\Models\LegalEntity;
use App\Models\User;
use App\Classes\Cipher\Traits\Cipher;
use App\Repositories\EmployeeRepository;
use App\Traits\FormTrait;
use App\Traits\InteractsWithCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;

class EmployeeForm extends Component
{
    use FormTrait;
    use Cipher;
    use WithFileUploads;
    use InteractsWithCache;

    const CACHE_PREFIX = 'register_employee_form';

    public EmployeeFormRequest $employeeRequest;

    protected string $employeeCacheKey;

    public Employee $employee;

    public object $employees;

    public LegalEntity $legalEntity;

    public string $mode = 'create';

    public array $success = [
        'message' => '',
        'status'  => false,
    ];

    public ?array $error = [
        'message' => '',
        'status'  => false,
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
        'EMPLOYEE_TYPE'
    ];

    public ?object $divisions;
    public ?object $healthcareServices;

    protected ?EmployeeRepository $employeeRepository;

    public array $tableHeaders;
    public string $requestId;
    public string $employeeId;
    /**
     * @var mixed|string
     */
    public mixed $keyProperty;


    public ?object $file = null;


    public function boot(EmployeeRepository $employeeRepository): void
    {
        $this->employeeRepository = $employeeRepository;
        $this->employeeCacheKey = self::CACHE_PREFIX.'-'.Auth::user()->legalEntity->uuid;
    }

    public function mount(Request $request, $id = '')
    {
        $this->getLegalEntity();
//        $this->getDivisions();
        if ($request->has('storeId')) {
            $this->requestId = $request->input('storeId');
        }
        if (!empty($id)) {
            $this->employeeId = $id;
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
            foreach ($this->employee['documents'] as $document) {
                unset($dictionaries['DOCUMENT_TYPE'][$document['type']]);
            }
        }
        return $this->dictionaries = $dictionaries;
    }

    public function getEmployee(): void
    {
        if (isset($this->employeeId)) {
            $employeeData = Employee::showEmployee($this->employeeId);
            $this->employeeRequest->fill($employeeData);
        }
        if ($this->hasCache($this->employeeCacheKey) && isset($this->requestId)) {
            $employeeData = $this->getCache($this->employeeCacheKey);
            if (isset($employeeData[$this->requestId])) {
                $this->employeeRequest->fill($employeeData[$this->requestId]);
            }
        }

    }

    public function updatedFile(): void
    {
        $this->keyContainerUpload = $this->file;
    }

    /**
     * Set the table headers for the E-health table.
     */


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
        $this->employeeRequest->{$model} = [];
        $this->openModal($model);
        $this->getEmployee();
        $this->dictionaryUnset();
    }


    public function signedComplete($model)
    {
        $this->getEmployee();
        $open = $this->employeeRequest->validateBeforeSendApi();
        if ($open['error']) {
            $this->dispatch('flashMessage', ['message' => $open['message'], 'type' => 'error']);
        } else {
            $this->openModal($model);
        }
    }

    public function updated($field)
    {

        if ($field === 'keyContainerUpload') {
            $this->getEmployee();
        }
    }


    public function store($model) : void
    {
        
        $this->employeeRequest->rulesForModelValidate($model);

        $this->resetErrorBag();

        if (isset($this->requestId)) {
            $this->storeCacheEmployee($model);
        }
        $this->closeModalModel();
        $this->dispatch('flashMessage', ['message' => __('Інформацію успішно оновлено'), 'type' => 'success']);

        $this->getEmployee();
    }

    public function storeCacheEmployee(string $model): void
    {
        $this->storeCacheData(
            $this->employeeCacheKey,
            $model,
            'employeeRequest',
            ['party','scienceDegree']
        );

    }



    public function edit($model, $keyProperty = '')
    {

        $this->keyProperty = $keyProperty;
        $this->mode = 'edit';
        $this->openModal($model);

        if (isset($this->requestId)) {
            $this->editCacheEmployee($model, $keyProperty);
        }

    }


    public function editCacheEmployee( string $model,  string $keyProperty = '')
    {
        $cacheData = $this->getCache($this->employeeCacheKey);

        if ($keyProperty !== '') {
            $this->employeeRequest->{$model} = $cacheData[$this->requestId][$model][$keyProperty];
        } else {
            $this->employeeRequest->{$model} = $cacheData[$this->requestId][$model];

        }
    }

    public function editEmployee($model, $keyProperty = '')
    {
        if ($model == 'documents') {
            $this->employeeRequest->{$model} = $this->employee->party[$model][$keyProperty];
        } elseif ($model == 'science_degree') {
            $this->employeeRequest->{$model} = $this->employee->doctor[$model];
        } else {
            $this->employeeRequest->{$model} = $this->employee->doctor[$model][$keyProperty];
        }
    }

    public function update($model, $keyProperty)
    {
        $this->employeeRequest->rulesForModelValidate($model);
        $this->resetErrorBag();

        if (isset($this->requestId)) {
            $this->updateCacheEmployee($model, $keyProperty);
        }
        if (isset($this->employeeId)) {
            $this->updateEmployee($model, $keyProperty);
        }
        $this->closeModalModel($model);
    }

    public function updateCacheEmployee($model,$keyProperty)
    {
        if ($this->hasCache($this->employeeCacheKey)) {
            $cacheData = $this->getCache($this->employeeCacheKey);
            $cacheData[$this->requestId][$model][$keyProperty] = $this->employeeRequest->{$model};
            $this->putCache($this->employeeCacheKey, $cacheData);
        }
    }


    public function updateEmployee($model, $keyProperty)
    {
        if ($model === 'documents') {
            $party = $this->employee->party;
            $party[$model][$keyProperty] = $this->employeeRequest->{$model};
            $this->employee->party = $party;
        } else {
            $doctor = $this->employee->doctor;
            $doctor[$model][$keyProperty] = $this->employeeRequest->{$model};
            $this->employee->doctor = $doctor;
        }
        $this->employee->save();
    }

    public function closeModalModel($model = null): void
    {
        if (!empty($model)) {
            $this->employeeRequest->{$model} = [];
        }

        $this->closeModal();
        $this->getEmployee();
    }


    public function sendApiRequest()
    {
        $preRequest = $this->employeeRequest->toArray();
        $preRequest['doctor'] = [
            'specialities'   => $preRequest['specialities'],
            'qualifications' => $preRequest['qualifications'],
            'educations'     => $preRequest['educations'],
            'scienceDegree'  => $preRequest['scienceDegree']
        ];
        $employeeRequest = schemaService()->requestSchemaNormalize(
            ['employee_request' => $preRequest],
            app(EmployeeApi::class),
        );

        dd($employeeRequest);
        $base64Data = $this->sendEncryptedData($employeeRequest);
        if (isset($base64Data['errors'])) {
            $this->dispatch('flashMessage', [
                'message' => $base64Data['errors'],
                'type'    => 'error'
            ]);
            return;
        }
        $data = [
            'signed_content'          => $base64Data,
            'signed_content_encoding' => 'base64',
        ];

        $employeeRequest = EmployeeRequestApi::createEmployeeRequest($data);
        $this->employeeRepository->saveEmployeeData($employeeRequest, $this->legalEntity);
        if (isset($this->requestId)) {
            $this->forgetCacheIndex();
        }
        return redirect(route('employee.index'));
    }


    private function forgetCacheIndex()
    {
        $cacheData = $this->getCache($this->employeeCacheKey);
        if (isset($cacheData[$this->requestId])) {
            unset($cacheData[$this->requestId]);
            $this->putCache($this->employeeCacheKey, $cacheData);
        }
    }


    /**
     * Save a new user with the provided data.
     *
     * @param  array  $data  The data to create the user with.
     */

    public function saveUser(array $data)
    {
        $user = User::create([
            'email'    => $data['party']['email'],
            'password' => Hash::make(\Illuminate\Support\Str::random(8)),
        ]);
        $user->assignRole($data['employee_type']);
        $user->legalEntity()->associate($this->legalEntity);
        $user->save();
    }

    /*
     * Include functions after  getDictionary
     * @return array
     */
    public function getEmployeeDictionaryRole(): array
    {
        $validRoles = ['OWNER', 'ADMIN', 'DOCTOR', 'HR'];

        $filteredRoles = array_filter($this->dictionaries['EMPLOYEE_TYPE'], function ($key) use ($validRoles) {
            return in_array($key, $validRoles);
        }, ARRAY_FILTER_USE_KEY);

        return $this->dictionaries['EMPLOYEE_TYPE'] = $filteredRoles;
    }


    public function getEmployeeDictionaryPosition(): array
    {
        $validPositions = [
            "P3", "P274", "P93", "P202", "P215", "P159", "P118", "P46", "P54", "P99", "P109", "P96", "P245", "P279",
            "P63", "P123", "P17", "P62", "P45", "P10", "P74", "P37", "P114", "P127", "P214", "P179", "P156", "P145",
            "P103", "P115", "P126", "P120", "P268", "P110", "P43", "P130", "P203", "P81", "P273", "P95", "P191", "P42",
            "P38", "P105", "P23", "P197", "P154", "P65", "P58", "P175", "P61", "P98", "P13", "P177", "P173", "P72",
            "P256", "P178", "P153", "P212", "P53", "P48", "P7", "P106", "P122", "P52", "P158", "P15", "P22", "P39",
            "P92", "P112", "P71", "P164", "P170", "P266", "P224", "P270", "P78", "P242", "P160", "P2", "P213", "P152",
            "P26", "P247", "P192", "P36", "P67", "P181", "P124", "P73", "P228", "P55", "P117", "P249", "P91", "P70",
            "P231", "P229", "P97", "P167", "P169", "P238", "P149", "P150", "P128", "P64", "P51", "P83", "P44", "P241",
            "P4", "P50", "P250", "P116", "P185", "P276", "P76", "P40", "P69", "P84", "P82", "P176", "P174", "P278",
            "P155", "P9", "P257", "P29", "P252", "P243", "P24", "P180", "P166", "P201", "P16", "P200", "P210", "P34",
            "P272", "P168", "P275", "P194", "P165", "P146", "P151", "P111", "P85", "P265", "P87", "P246", "P6", "P77",
            "P41", "P204", "P94", "P240", "P79", "P14", "P216", "P32", "P59", "P230", "P1", "P88", "P248", "P172",
            "P75", "P113", "P196", "P28", "P129", "P206", "P57", "P162", "P35", "P107", "P184", "P68", "P131", "P189",
            "P211", "P60", "P25", "P56", "P161", "P5", "P89", "P188", "P183", "P100", "P47", "P269", "P66", "P8",
            "P207", "P255", "P119", "P90", "P86", "P27", "P199", "P108", "P163", "P157", "P277", "P11"
        ];


        $filterPosition = array_filter($this->dictionaries['POSITION'], function ($key) use ($validPositions) {
            return in_array($key, $validPositions);
        }, ARRAY_FILTER_USE_KEY);

        return $this->dictionaries['POSITION'] = $filterPosition;
    }

    public function render()
    {
         $this->getDictionary();

        return view('livewire.employee.employee-form');
    }


}
