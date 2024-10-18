<?php

namespace App\Livewire\Declaration;

use App\Classes\eHealth\Api\DeclarationApi;
use App\Livewire\Declaration\forms\DeclarationRequestApi;
use App\Models\Declaration;
use App\Models\Employee;
use Database\Seeders\DeclarationSeeder;
use Livewire\Component;

class DeclarationIndex extends Component
{

    public  array $tableHeaders  = [];

    public string $employee_name = '';


    public string $employee_uuid = '';

    public string $status_doctor = '';

    public string $legal_entity_id = '';

    public string $declaration_number = '';

    /**
     * @var array|int[]
     */
    public ?array $request_declaration = [
        'page' => 1,
        'page_size' => 10,
    ];

    public ?object $employees = null;

    public ?object $declarations = null;

    public function mount()
    {
        $this->tableHeaders();
    }

    public function tableHeaders(): void{
        $this->tableHeaders = [
            __('ФІО'),
            __('Номер декларації'),
            __('Лікар'),
        ];
    }

     public function updated($field): void
    {
        $this->getDeclarations();
    }



    public function getDeclarations(): void{

        if ($this->employee_name) {
            $query = Employee::doctor();
            $nameParts = explode(' ', $this->employee_name);
            $firstName = $nameParts[0] ?? null;
            $lastName = $nameParts[1] ?? null;
            $secondName = $nameParts[2] ?? null;
            $this->employees =   $query->where(function ($query) use ($firstName, $lastName, $secondName) {
                if (strlen($firstName) > 3) {
                    $query->whereRaw("party->>'first_name' ILIKE ?", ["%$firstName%"]);
                }
                if (strlen($lastName) > 3) {
                    $query->whereRaw("party->>'last_name' ILIKE ?", ["%$lastName%"]);
                }
                if (strlen($secondName) > 3) {
                    $query->whereRaw("party->>'second_name' ILIKE ?", ["%$secondName%"]);
                }
            })->get();

        }

        if (!empty($this->employee_uuid)) {
            $this->request_declaration['employee_id'] = $this->employee_uuid;
            $employee = Employee::where('uuid', $this->employee_uuid)->first();
            $this->declarations = $employee->declarations()->get();

        }

        if (!empty($this->status_doctor)) {
            $this->request_declaration['status'] = $this->status_doctor;
        }

        if (!empty($this->legal_entity_id)) {
            $this->request_declaration['legal_entity_id'] = $this->legal_entity_id;
        }

        if (!empty($this->declaration_number)) {
            $this->request_declaration['declaration_number'] = $this->declaration_number;
        }

//        $getListDeclaration = DeclarationRequestApi::getListDeclaration($this->request_declaration);


    }



    public function callSeeder(){

        if (!Declaration::count()) {
            (new DeclarationSeeder)->run();
        }
    }

    public function render()
    {
        return view('livewire.declaration.declaration-index');

    }
}
