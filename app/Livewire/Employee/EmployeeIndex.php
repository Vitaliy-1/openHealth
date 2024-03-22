<?php

namespace App\Livewire\Employee;

use App\Livewire\Employee\Forms\Api\EmployeeRequestApi;
use App\Models\Employee;
use App\Traits\FormTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EmployeeIndex extends Component
{

    use FormTrait;
    const CACHE_PREFIX = 'register_employee_form';

    public object $employees;
    public array $tableHeaders = [];

    protected string $employeeCacheKey;
    public int $storeId = 0;
    public \Illuminate\Support\Collection $employeesCache;
    public string $dismiss_text;

    public int $dismissed_id;
    /**
     * @var false
     */


    public function boot(Employee $employee): void
    {
        $this->employeeCacheKey = self::CACHE_PREFIX . '-'. Auth::user()->legalEntity->uuid;
    }


    public function mount()
    {
        $this->tableHeaders();
        $this->getLastStoreId();
        $this->getEmployees();
//        $this->employees =auth()->user()->legalEntity-;
    }

    public function getLastStoreId()
    {
        if (Cache::has($this->employeeCacheKey) && !empty(Cache::get($this->employeeCacheKey)) && is_array(Cache::get($this->employeeCacheKey))) {
            $this->storeId = array_key_last(Cache::get($this->employeeCacheKey));
        }

        $this->storeId ++;
    }

    public function getEmployeesCache(): void
    {
        if (Cache::has($this->employeeCacheKey)) {
            $this->employeesCache = collect(Cache::get($this->employeeCacheKey))->map(function ($data) {
                return (new Employee())->forceFill($data);
            });
        }
    }

    public function getEmployees($status = ''): void
    {
        $this->employees = DB::table('legal_entities')
            ->join('users', 'legal_entities.id', '=', 'users.legal_entity_id')
            ->join('employees', 'legal_entities.id', '=', 'employees.legal_entity_id')
            ->join('persons', 'employees.person_id', '=', 'persons.id')
            ->where('users.id', Auth::id())
            ->select(
                'employees.id as id',
                'employees.uuid',
                'employees.start_date',
                'employees.end_date',
                'employees.status',
                DB::raw("CONCAT(persons.first_name, ' ', persons.last_name, ' ', persons.second_name) AS full_name"),
                'persons.email',
                'persons.phones',

                'employees.position',
                'employees.employee_type',
            )
            ->get();

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
    public function create()
    {

        return redirect()->route('employee.form', ['store_id' => $this->storeId]);

    }

    public string $selectedOption = 'is_active';

    public function sortEmployees():void
    {
        if ($this->selectedOption === 'is_active') {
            $this->getEmployees();
            $this->employeesCache = collect();
        } elseif ($this->selectedOption === 'is_inactive') {
            $this->employeesCache = collect();
            $this->employees = collect();

        } elseif ($this->selectedOption === 'is_cache') {
            $this->getEmployeesCache();
            $this->employees = collect();
        }
    }

    public function dismissed(Employee $employee){
        $dismissed = EmployeeRequestApi::dismissedEmployeeRequest($employee->uuid);

        if (!empty($dismissed)){
            $employee->update([
                'status' => 'DISMISSED',
                'end_date' => Carbon::now()->format('Y-m-d'),
            ]);
        }

        $this->closeModal();
        $this->getEmployees();

    }

    public function showModalDismissed($id){
        $employee = Employee::find($id);
        if ($employee->employee_type === 'DOCTOR') {
            $this->dismiss_text = __('forms.dismissed_text_doctor');
        }
        else{
            $this->dismiss_text = __('forms.dismissed_textr');

        }
        $this->dismissed_id = $employee->id;

        $this->openModal();
    }


    public function render()
    {
        return view('livewire.employee.employee-index');
    }
}
