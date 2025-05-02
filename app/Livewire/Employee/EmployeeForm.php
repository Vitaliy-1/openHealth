<?php

namespace App\Livewire\Employee;

use App\Classes\eHealth\Api\EmployeeApi;
use App\Livewire\Employee\Forms\Api\EmployeeRequestApi;
use App\Livewire\Employee\Forms\EmployeeForm as Form;
use App\Models\Division;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeRequest;
use App\Models\LegalEntity;
use App\Classes\Cipher\Traits\Cipher;
use App\Repositories\EmployeeRepository;
use App\Traits\FormTrait;
use App\Traits\InteractsWithCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class EmployeeForm extends Component
{
    use FormTrait;
    use Cipher;
    use WithFileUploads;
    use InteractsWithCache;

    const CACHE_PREFIX = 'register_employee_form';

    public Form $employeeRequest;

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

    public ?array $dictionaryNames = [
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
        'EMPLOYEE_TYPE',
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

    /**
     * @var mixed|string
     */
    public mixed $singleProperty;


    public function boot(EmployeeRepository $employeeRepository): void
    {
        $this->employeeRepository = $employeeRepository;
        $this->employeeCacheKey = self::CACHE_PREFIX.'-'.Auth::user()->legalEntity->uuid;
    }

    public function mount(Request $request, $id = '')
    {
        $this->getLegalEntity();
        if ($request->has('storeId')) {
            $this->requestId = $request->input('storeId');
        }
        if (!empty($id)) {
            $this->employeeId = $id;
        }
        $this->setCertificateAuthority();
        $this->getEmployee();
        $this->getDictionaries();
    }

    public function getHealthcareServices($id)
    {
        $this->healthcareServices = Division::find($id)
            ->healthcareService()
            ->get();
    }

    public function getDictionaries(): void
    {
        $this->getDictionary();
        $this->getEmployeeDictionaryRole();
        $this->getEmployeeDictionaryPosition();
    }

    public function setCertificateAuthority(): array|null
    {
        return $this->getCertificateAuthority = $this->getCertificateAuthority();
    }

    public function dictionaryUnset(): void
    {
        if (isset($this->employeeRequest->documents) && !empty($this->employeeRequest->documents)) {
            foreach ($this->employeeRequest->documents as $document) {
                unset($this->dictionaries['DOCUMENT_TYPE'][$document['type']]);
            }
        }
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


    public function getLegalEntity(): void
    {
        $this->legalEntity = auth()->user()->legalEntity;
    }

    public function getDivisions(): void
    {
        $this->divisions = $this->legalEntity->division()
            ->where('status', 'ACTIVE')
            ->get();
    }

    public function openModalModel($model, $singleProperty = ''): void
    {
        $this->showModal = $model;
        $this->singleProperty = $singleProperty;
    }

    public function create($model, $singleProperty = '')
    {
        $this->mode = 'create';
        if (!empty($singleProperty)) {
            $this->singleProperty = $singleProperty;
        }
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

    public function store($model, $modelSingle = []): void
    {
        $rules = $model;
        if (!empty($modelSingle)) {
            $rules = $modelSingle;
        }
        $this->employeeRequest->rulesForModelValidate($rules);
        $this->resetErrorBag();
        if (!empty($modelSingle)) {
            $this->employeeRequest->{$model} = $this->employeeRequest->{$modelSingle};
            unset($this->employeeRequest->{$modelSingle});
        }
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
            ['party', 'scienceDegree']
        );
    }

    public function edit($model, $keyProperty = '', $singleProperty = ''): void
    {
        $this->keyProperty = $keyProperty;
        $this->mode = 'edit';
        $this->singleProperty = $singleProperty;
        if (isset($this->requestId)) {
            $this->editCacheEmployee($model, $keyProperty, $singleProperty);
        }
        $this->openModal($model);
    }

    public function editCacheEmployee(string $model, string $keyProperty = '', $singleProperty = '')
    {
        $cacheData = $this->getCache($this->employeeCacheKey);
        if ($keyProperty !== '') {
            $this->employeeRequest->{$singleProperty ?: $model} = $cacheData[$this->requestId][$model][$keyProperty];
        } else {
            $this->employeeRequest->{$model} = $cacheData[$this->requestId][$model];
        }
    }

    public function update($model, $keyProperty, $modelSingle = '')
    {
        $rules = $model;
        if (!empty($modelSingle)) {
            $rules = $modelSingle;
        }

        $this->employeeRequest->rulesForModelValidate($rules);
        $this->resetErrorBag();
        if (isset($this->requestId)) {
            $this->updateCacheEmployee($model, $keyProperty, $modelSingle);
        }
        unset($this->employeeRequest->{$modelSingle});
        $this->closeModalModel($model);
    }

    public function updateCacheEmployee($model, $keyProperty, $singleProperty = '')
    {
        if (!empty($modelSingle)) {
            $this->employeeRequest->{$model} = $this->employeeRequest->{$modelSingle};
            unset($this->employeeRequest->{$modelSingle});
        }

        if ($this->hasCache($this->employeeCacheKey)) {
            $cacheData = $this->getCache($this->employeeCacheKey);
            if (isset($cacheData[$this->requestId][$model][$keyProperty])) {
                $cacheData[$this->requestId][$model][$keyProperty] = $this->employeeRequest->{$singleProperty ?: $model};
            }
            $this->putCache($this->employeeCacheKey, $cacheData);
        }
    }

    public function remove($model, $keyProperty = ''): void
    {
        $cacheData = $this->getCache($this->employeeCacheKey);
        if (isset($cacheData[$this->requestId][$model][$keyProperty])) {
            unset($cacheData[$this->requestId][$model][$keyProperty]);
        }

        $this->putCache($this->employeeCacheKey, $cacheData);
        $this->getEmployee();
    }

    public function closeModalModel($model = null): void
    {
        if (!empty($model)) {
            $this->employeeRequest->{$model} = [];
        }
        $this->closeModal();
        $this->getEmployee();
    }

    public function preRequestData(): array
    {
        if (!in_array($this->employeeRequest->party['employeeType'],config('ehealth.doctors_type',[]))) {
            unset($this->employeeRequest->specialities);
            unset($this->employeeRequest->qualifications);
            unset($this->employeeRequest->educations);
            unset($this->employeeRequest->scienceDegree);
        }
        return schemaService()
            ->setDataSchema(['employee_request' => $this->employeeRequest->toArray()],app(EmployeeApi::class))
            ->mapFields(['position', 'employeeType', 'startDate'], 'party', 'employee_request')
            ->mapFields([
                'doctor' => ['specialities', 'qualifications', 'educations', 'scienceDegree'],
                'party'  => ['documents']
            ],'','employee_request')
            ->requestSchemaNormalize()
            ->getNormalizedData();
    }

    public function sendApiRequest()
    {

        $base64Data = $this->sendEncryptedData(
            removeEmptyKeys($this->preRequestData()),
            \auth()->user()->tax_id
        );
        if (isset($base64Data['errors'])) {
            $this->dispatchErrorMessage($base64Data['errors']);
            return;
        }

        $employeeRequest = EmployeeRequestApi::createEmployeeRequest([
            'signed_content'          => $base64Data,
            'signed_content_encoding' => 'base64',
        ]);

        $this->apiResponse($employeeRequest);

        //TODO: add flash message

        $this->dispatch('flashMessage', [
            'message' => __('api.api_request_sent'),
            'type'    => 'success',
        ]);

        return redirect()->route('employee.index');

    }

    //Response from api
    public function apiResponse($response):void
    {
        $employeeResponse = schemaService()->setDataSchema($response, app(EmployeeApi::class))
            ->responseSchemaNormalize()
            ->replaceIdsKeysToUuid(['id', 'legalEntityId', 'divisionId', 'partyId'])
            ->getNormalizedData();
        app(EmployeeRepository::class)->saveEmployeeData($employeeResponse, auth()->user()->legalEntity,
            new EmployeeRequest());
    }

    private function dispatchErrorMessage(string $message, array $errors = []): void
    {
        $this->dispatch('flashMessage', [
            'message' => $message,
            'type'    => 'error',
            'errors'  => $errors
        ]);
    }

    public function getEmployeeDictionaryRole(): void
    {
        $this->dictionaries['EMPLOYEE_TYPE'] = $this->getDictionariesFields(config('ehealth.legal_entity_type.'.auth()->user()->legalEntity->type.'.roles'),
            'EMPLOYEE_TYPE');
    }

    public function updatedEmployeeRequestPartyEmployeeType(): void{
        $this->getEmployeeDictionaryPosition();
        if (!in_array( $this->employeeRequest->party['employeeType'],config('ehealth.doctors_type',[]))) {
            $this->employeeRequest->educations = [];
            $this->employeeRequest->qualifications = [];
            $this->employeeRequest->scienceDegree = [];
            $this->employeeRequest->specialities = [];
        }
        $this->employeeRequest->party['position'] = '';
    }

    public function getEmployeeDictionaryPosition(): void
    {
        $employeeType = $this->employeeRequest->party['employeeType'] ?? null;

        if (!empty($employeeType)) {
            $keys = config("ehealth.employee_type.{$employeeType}.position", []);
            if (!empty($keys)) {
              $this->dictionaries['POSITION_EMPLOYEE_TYPE'] =  $this->getDictionariesFields($keys, 'POSITION');
            }
        }
        else{
            $this->dictionaries['POSITION_EMPLOYEE_TYPE'] = [];
        }
    }

    public function render()
    {
        return view('livewire.employee.employee-create');
    }


    public function testEmployee(){
        return array(
            "division_id" => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
            "legal_entity_id" => "d290f1ee-6c54-4b01-90e6-d701748f0851",
            "position" => "P8",
            "start_date" => "2017-03-02T10:45:16.000Z",
            "end_date" => "2018-03-02T10:45:16.000Z",
            "status" => "NEW",
            "employee_type" => "DOCTOR",
            "party" => array(
                "id" => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                "first_name" => "Петро",
                "last_name" => "Іванов",
                "second_name" => "Миколайович",
                "birth_date" => "1991-08-19T00:00:00.000Z",
                "gender" => "MALE",
                "no_tax_id" => false,
                "tax_id" => "3213213213",
                "email" => "emai133l@example.com",
                "documents" => array(
                    array(
                        "type" => "PASSPORT",
                        "number" => "АА120518",
                        "issued_by" => "Рокитнянським РВ ГУ МВС Київської області",
                        "issued_at" => "2017-02-28"
                    )
                ),
                "phones" => array(
                    array(
                        "type" => "MOBILE",
                        "number" => "+380503410870"
                    )
                ),
                "working_experience" => 10,
                "about_myself" => "Закінчив всі можливі курси"
            ),
            "doctor" => array(
                "educations" => array(
                    array(
                        "country" => "UA",
                        "city" => "Київ",
                        "institution_name" => "Академія Богомольця",
                        "issued_date" => "2017-02-28",
                        "diploma_number" => "DD123543",
                        "degree" => "MASTER",
                        "speciality" => "Педіатр"
                    )
                ),
                "qualifications" => array(
                    array(
                        "type" => "SPECIALIZATION",
                        "institution_name" => "Академія Богомольця",
                        "speciality" => "Педіатр",
                        "issued_date" => "2017-02-28",
                        "certificate_number" => "2017-02-28",
                        "valid_to" => "2017-02-28",
                        "additional_info" => "додаткова інофрмація"
                    )
                ),
                "specialities" => array(
                    array(
                        "speciality" => "THERAPIST",
                        "speciality_officio" => true,
                        "level" => "FIRST",
                        "qualification_type" => "AWARDING",
                        "attestation_name" => "Академія Богомольця",
                        "attestation_date" => "2017-02-28",
                        "valid_to_date" => "2020-02-28",
                        "certificate_number" => "AB/21331"
                    )
                ),
                "science_degree" => array(
                    "country" => "UA",
                    "city" => "Київ",
                    "degree" => "SCIENCE_DEGREE",
                    "institution_name" => "Академія Богомольця",
                    "diploma_number" => "DD123543",
                    "speciality" => "Педіатр",
                    "issued_date" => "2017-02-28"
                )
            ),
            "id" => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
            "inserted_at" => "2017-05-05T14:09:59.232112",
            "updated_at" => "2017-05-05T14:09:59.232112"
        );
    }


}
