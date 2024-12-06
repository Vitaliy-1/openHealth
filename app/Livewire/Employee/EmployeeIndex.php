<?php

namespace App\Livewire\Employee;

use App\Classes\eHealth\Api\PersonApi;
use App\Livewire\Employee\Forms\Api\EmployeeRequestApi;
use App\Models\Employee;
use App\Models\LegalEntity;
use App\Models\Relations\Document;
use App\Models\Relations\Party;
use App\Models\Relations\Phone;
use App\Models\User;
use App\Repositories\EmployeeRepository;
use App\Services\EmployeeService;
use App\Traits\FormTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
                return (new Employee())->forceFill($data);
            });
        }
        return collect();
    }

    public function getEmployees(): void
    {
      $this->employees = $this->status === 'APPROVED' && 'NEW' ?
          $this->legalEntity->employees()->with('party')->where('status', $this->status)->get()
      : $this->getEmployeesCache();

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
        dd($status);
        $this->status = $status;
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
            $request = EmployeeRequestApi::getEmployeeById($request['id']);
            $request['uuid'] = $request['id'];
            $request['legal_entity_uuid'] = $request['legal_entity']['id'];
            $this->employeeRepository->saveEmployeeData($request, $this->legalEntity);
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
