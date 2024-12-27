<?php

namespace App\Livewire\Employee;

use App\Classes\eHealth\Api\EmployeeApi;
use App\Livewire\Employee\Forms\Api\EmployeeRequestApi;
use App\Models\Employee\BaseEmployee;
use App\Models\Employee\Employee;
use App\Models\LegalEntity;

use App\Models\Relations\Party;
use App\Models\Relations\Phone;
use App\Repositories\EmployeeRepository;
use App\Traits\FormTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class EmployeeIndex extends Component
{

    use FormTrait;

    const CACHE_PREFIX = 'register_employee_form';

    public object $employees;

    public array $tableHeaders = [];
    protected string $employeeCacheKey;

    public int $storeId = 0;
    public string $dismiss_text;
    public int $dismissed_id;

    private LegalEntity $legalEntity;

    public string $email = '';
    protected ?EmployeeRepository $employeeRepository; // nullable

    public array $dictionaries_field = [
        'POSITION',
    ];

    public string $status = 'APPROVED';


    public function boot(EmployeeRepository $employeeRepository): void
    {
        $this->employeeCacheKey = self::CACHE_PREFIX.'-'.Auth::user()->legalEntity->uuid;
        $this->employeeRepository = $employeeRepository;
        $this->legalEntity = Auth::user()->legalEntity;
    }

    public function mount()
    {
        $this->tableHeaders();
        $this->getLastStoreId();
        $this->getEmployees();
        $this->getDictionary();
    }

    public function getLastStoreId()
    {
        if (Cache::has($this->employeeCacheKey) && !empty(Cache::get($this->employeeCacheKey)) && is_array(Cache::get($this->employeeCacheKey))) {
            $this->storeId = array_key_last(Cache::get($this->employeeCacheKey));
        }
        $this->storeId++;
    }

    public function getEmployeesCache(): \Illuminate\Support\Collection
    {
        if (Cache::has($this->employeeCacheKey)) {
            return collect(Cache::get($this->employeeCacheKey))->map(function ($data) {
                $employee = (new BaseEmployee())->forceFill($data['party']);
                $employee->party = (new Party())->forceFill($data['party'] ?? []);
                $employee->party->phones = (new Phone())->forceFill($data['party']['phones'] ?? []);
                return $employee;
            });
        }
        return collect();
    }

    public function getEmployees(): void
    {
        if ($this->status === 'APPROVED') {
            $this->employees = $this->legalEntity->employees()->get();
        } elseif ($this->status === 'NEW') {
            $this->employees = $this->legalEntity->employeesRequest()->get();
        } else {
            $this->employees = $this->getEmployeesCache();

        }

    }

    public function tableHeaders(): void
    {
        $this->tableHeaders = [
            __('ID E-health '),
            __('ПІБ'),
            __('Телефон'),
            __('Email'),
            __('Посада'),
            __('Статус'),
            __('Дія'),
        ];
    }

    public function sortEmployees($status): void
    {
        $this->status = $status;
        $this->getEmployees();
    }

    public function dismissed(Employee $employee)
    {
        $dismissed = EmployeeRequestApi::dismissedEmployeeRequest($employee->uuid);

        if (!empty($dismissed)) {
            $employee->update([
                'status'   => 'DISMISSED',
                'end_date' => Carbon::now()->format('Y-m-d'),
            ]);
        }
        $this->closeModal();
        $this->getEmployees();
    }

    public function showModalDismissed($id)
    {
        $employee = Employee::find($id);
        if ($employee->employee_type === 'DOCTOR') {
            $this->dismiss_text = __('forms.dismissed_text_doctor');
        } else {
            $this->dismiss_text = __('forms.dismissed_textr');
        }
        $this->dismissed_id = $employee->id;

        $this->openModal();
    }

    //TODO: Створити багато співробітників в статусі не підтверджено, створювати таблицю EmployeeRequest? перевірити Rate Limit
    public function getEmployeeRequestsList()
    {
        return EmployeeRequestApi::getEmployeeRequestsList();
    }

    /**
     * Syncs employees by fetching data from the EmployeeRequestApi and saving it using the employeeSyncService.
     *
     */

    public function syncEmployees(): void
    {
        $requests = EmployeeRequestApi::getEmployees($this->legalEntity->uuid);

        foreach ($requests as $request) {
            $response = EmployeeRequestApi::getEmployeeById($request['id']);
            $employeeResponse = schemaService()->setDataSchema($response, app(EmployeeApi::class))
                ->responseSchemaNormalize()
                ->replaceIdsKeysToUuid(['id', 'legalEntityId', 'divisionId', 'partyId'])
                ->getNormalizedData();
            app(EmployeeRepository::class)
                ->saveEmployeeData($employeeResponse,
                auth()->user()->legalEntity,
                new Employee());
        }

        $this->dispatchErrorMessage(__('Співробітники успішно синхронізовано'));

        $this->getEmployees();
    }


    private function dispatchErrorMessage(string $message, string $type = 'success', array $errors = []): void
    {
        $this->dispatch('flashMessage', [
            'message' => $message,
            'type'    => $type,
            'errors'  => $errors
        ]);
    }


    public function render()
    {
        return view('livewire.employee.employee-index');
    }


}
