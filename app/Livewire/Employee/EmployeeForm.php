<?php

namespace App\Livewire\Employee;

use App\Classes\Cipher\Traits\Cipher;
use App\Classes\eHealth\Api\EmployeeApi;
use App\Livewire\Employee\Forms\Api\EmployeeRequestApi;
use App\Livewire\Employee\Forms\EmployeeForm as Form;
use App\Models\Division;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeRequest;
use App\Models\LegalEntity;
use App\Repositories\EmployeeRepository;
use App\Traits\FormTrait;
use App\Traits\InteractsWithCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class EmployeeForm extends Component
{
    use FormTrait, Cipher, WithFileUploads, InteractsWithCache;

    const CACHE_PREFIX = 'register_employee_form';

    public Form $employeeRequest;
    public Employee $employee;
    public LegalEntity $legalEntity;
    public string $mode = 'create';
    public array $success = ['message' => '', 'status' => false];
    public ?array $error = ['message' => '', 'status' => false];
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

    public string $employeeCacheKey;
    public string $requestId;
    public string $employeeId;
    public mixed $keyProperty;
    public mixed $singleProperty;
    public ?object $file = null;

    public function boot(EmployeeRepository $employeeRepository): void
    {
        $this->employeeRepository = $employeeRepository;
        $this->employeeCacheKey = self::CACHE_PREFIX . '-' . Auth::user()->legalEntity->uuid;
    }

    public function mount(Request $request, $id = ''): void
    {
        $this->legalEntity = Auth::user()->legalEntity;
        $this->requestId = $request->input('storeId', '');
        $this->employeeId = $id;

        $this->setCertificateAuthority();
        $this->loadEmployeeData();
        $this->getDictionaries();
    }

    protected function loadEmployeeData(): void
    {
        if (!empty($this->employeeId)) {
            $this->employeeRequest->fill(Employee::showEmployee($this->employeeId));
        } elseif ($this->hasCache($this->employeeCacheKey) && $this->requestId) {
            $cached = $this->getCache($this->employeeCacheKey);
            $this->employeeRequest->fill($cached[$this->requestId] ?? []);
        }
    }

    public function getHealthcareServices($id): void
    {
        $this->healthcareServices = Division::find($id)?->healthcareService?->get();
    }

    public function setCertificateAuthority(): void
    {
        $this->getCertificateAuthority = $this->getCertificateAuthority();
    }

    public function updatedFile(): void
    {
        $this->keyContainerUpload = $this->file;
    }

    public function create(string $model, string $singleProperty = ''): void
    {
        $this->mode = 'create';
        $this->singleProperty = $singleProperty;
        $this->employeeRequest->{$model} = [];
        $this->openModal($model);
        $this->dictionaryUnset();
    }

    public function store(string $model, array $modelSingle = []): void
    {
        $rules = $modelSingle ?: $model;
        $this->employeeRequest->rulesForModelValidate($rules);
        $this->resetErrorBag();

        if (!empty($modelSingle)) {
            $this->employeeRequest->{$model} = $this->employeeRequest->{$modelSingle};
            unset($this->employeeRequest->{$modelSingle});
        }

        $this->storeCacheEmployee($model);
        $this->closeModalModel();
        $this->dispatch('flashMessage', ['message' => __('Інформацію успішно оновлено'), 'type' => 'success']);
    }

    protected function storeCacheEmployee(string $model): void
    {
        $this->storeCacheData(
            $this->employeeCacheKey,
            $model,
            'employeeRequest',
            ['party', 'scienceDegree']
        );
    }

    public function sendApiRequest(): ?\Illuminate\Http\RedirectResponse
    {
        $base64Data = $this->sendEncryptedData(removeEmptyKeys($this->preRequestData()), auth()->user()->tax_id);

        if (isset($base64Data['errors'])) {
            return $this->dispatchErrorMessage($base64Data['errors']);
        }

        $employeeRequest = EmployeeRequestApi::createEmployeeRequest([
            'signed_content' => $base64Data,
            'signed_content_encoding' => 'base64',
        ]);

        $this->apiResponse($employeeRequest);

        $this->dispatch('flashMessage', [
            'message' => __('api.api_request_sent'),
            'type' => 'success',
        ]);

        return redirect()->route('employee.index');
    }

    protected function apiResponse($response): void
    {
        $data = schemaService()
            ->setDataSchema($response, app(EmployeeApi::class))
            ->responseSchemaNormalize()
            ->replaceIdsKeysToUuid(['id', 'legalEntityId', 'divisionId', 'partyId'])
            ->getNormalizedData();

        app(EmployeeRepository::class)->saveEmployeeData(
            $data,
            auth()->user()->legalEntity,
            new EmployeeRequest()
        );
    }

    protected function dispatchErrorMessage(string $message, array $errors = []): void
    {
        $this->dispatch('flashMessage', [
            'message' => $message,
            'type' => 'error',
            'errors' => $errors,
        ]);
    }

    public function render()
    {
        return view('livewire.employee.employee-create');
    }
}
