<?php

namespace App\Livewire\Declaration;

use App\Models\Declaration;
use App\Models\Employee;
use Database\Seeders\DeclarationSeeder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class DeclarationIndex extends Component
{

    use WithPagination;

    public array $tableHeaders = [];

    /**
     * Employee filter
     * @var array|string[]
     */
    public array $employee_filter = [
        'full_name'     => '',
        'status'        => '',
        'employee_uuid' => '',
    ];

    /**
     * @var array|int[]
     */
    public ?array $request_declaration = [
        'page'      => 1,
        'page_size' => 10,
    ];

    /**
     * @var object|null
     */
    public ?object $employees = null;

    /**
     * @var array|string[]
     */

    public ?array $declarations_filter = [
        'number_declaration' => '',
        'first_name'         => '',
        'last_name'          => '',
        'second_name'        => '',
        'gender'             => '',
        'birthday_day'       => '',
        'phone'              => '',
    ];

    /**
     * @var object|null
     */
    public ?object $declaration_show = null;


    protected $listeners = ['searchUpdated'];

    public function mount()
    {
        $this->tableHeaders();
    }

    public function tableHeaders(): void
    {
        $this->tableHeaders = [
            __('ФІО'),
            __('Телефон'),
            __('Дата народження'),
            __('Номер декларації'),
            __('Статус'),
            __('Дата декларації'),
            __('Лікар'),
            __('Дії'),
        ];
    }


    //Livewire functions
    public function showDeclaration($declaration): void
    {
        $this->declaration_show = new Declaration($declaration);
    }

    public function closeDeclaration(): void
    {
        $this->declaration_show = null;
    }

    public function updated($field): void
    {

        if (in_array($field, $this->declarations_filter) ) {
            $this->resetPage();
        }
    }
    //TODO: Remove function after testing
    public function callSeeder(): void
    {
        Declaration::truncate();
        if (!Declaration::count()) {
            $seeder = new DeclarationSeeder();
            DB::transaction(function () use ($seeder) {
                $seeder->run(1000);
            });

        }

    }


    public function searchUpdated($searchData): void
    {
        $this->declarations_filter = $searchData;
    }


    public function getDeclarations(): ?object
    {
        if (strlen($this->employee_filter['full_name']) > 3) {
            $query = Employee::doctor();

            $this->filterByEmployeeName($query, $this->employee_filter['full_name']);
        }

        return $this->filterDeclarations();
    }

    private function filterByEmployeeName($query, string $employeeName): void
    {
        $nameParts = explode(' ', $employeeName);
        $fields = ['first_name', 'last_name', 'second_name'];
        $query->where(function ($query) use ($nameParts, $fields) {
            foreach ($nameParts as $index => $part) {
                if (isset($part) && strlen($part) > 3) {
                    $field = $fields[$index] ?? null;
                    if ($field) {
                        $query->where("party->$field", 'ILIKE', "%$part%");
                    }
                }
            }
        });

        $this->employees = $query->take(5)->get();

    }

    private function filterDeclarations($employee = null): ?object
    {

        if ($this->employee_filter['employee_uuid']) {
            $employee = Employee::where('uuid', $this->employee_filter['employee_uuid'])->with('declarations')->first();
        }

        //  Get the employee by UUID
        if ($employee) {
            $declarations = $employee->declarations(); // Return the employee's declarations

        } else {
            $declarations = Declaration::query(); // Return all declarations
        }

        if (!empty($this->declarations_filter['first_name']) && strlen($this->declarations_filter['first_name']) > 3) {
            $firstName = $this->declarations_filter['first_name'];
            $declarations->where('person->first_name', 'ILIKE', "%$firstName%");
        }

        if (!empty($this->declarations_filter['last_name']) && strlen($this->declarations_filter['last_name']) > 3) {
            $last_name = $this->declarations_filter['last_name'];
            $declarations->where('person->last_name', 'ILIKE', "%$last_name%");
        }

        if (!empty($this->declarations_filter['second_name']) && strlen($this->declarations_filter['second_name']) > 3) {
            $second_name = $this->declarations_filter['second_name'];
            $declarations->where('person->second_name', 'ILIKE', "$second_name%");
        }

        if (!empty($this->declarations_filter['birth_date']) && strlen($this->declarations_filter['birth_date']) > 3) {
            $birth_date = $this->declarations_filter['birth_date'];
            $declarations->where('person->birth_date', 'ILIKE', "%$birth_date%");
        }

        if (!empty($this->declarations_filter['phone']) && strlen($this->declarations_filter['phone']) >= 3) {
            $phone = trim($this->declarations_filter['phone']);
            $declarations->whereRaw("EXISTS (
                                SELECT 1
                                FROM jsonb_array_elements(person->'phones') AS phone
                                WHERE phone->>'number' ILIKE ?
                            )", ["%$phone%"]);
        }

        if (!empty($this->declarations_filter['declaration_number']) && strlen($this->declarations_filter['declaration_number']) > 3) {
            $declaration_number = $this->declarations_filter['declaration_number'];
            $declarations->where('declaration_number', 'ILIKE', "%$declaration_number%");
        }

        if ($declarations) {
            return $declarations;
        }

        return null; // Return null if the employee is not found or doesn't havE
    }


    public function render()
    {
        $declarations = $this->getDeclarations();

        if (!$declarations) {
            $declarations = [];
        }

        $perPage = config('pagination.per_page');
        $paginatedDeclarations = !$declarations ? [] : $declarations->paginate($perPage);

        return view('livewire.declaration.declaration-index', [
            'declarations' => $paginatedDeclarations,
        ]);
    }
}
