<?php

namespace App\Livewire\Division;

use App\Classes\eHealth\Api\DivisionApi;
use App\Helpers\JsonHelper;
use App\Models\Division;
use Livewire\Attributes\Validate;
use Livewire\Component;

class DivisionForm extends Component
{



    #[Validate([
        'division.name' => 'required|min:6|max:255',
        'division.type' => 'required',
        'division.email' => 'required',
        'division.phones.number' => 'required|string',
        'division.phones.type' => 'required',
        'division.location.latitude' => 'required',
        'division.location.longitude' => 'required',

    ])]
    public  ?array   $division = [];
    public ?object $divisions ;

    public ?array $dictionaries;

    public ?array $working_hours = [
        'mon' => 'Понеділок',
        'tue' => 'Вівторок',
        'wed' => 'Середа',
        'thu' => 'Четвер',
        'fri' => 'П’ятниця',
        'sat' => 'Субота',
        'sun' => 'Неділя',
   ];

    public  ?array $tableHeaders = [];

    public bool $showModal = false;

    public ?array $addresses = [];
    public ?object $legalEntity;

    public string $mode = 'create';

    protected $listeners = [ 'updateFieldAddresses','validateKoatauu'];

    public function mount( )
    {

        $this->tableHeaders();

        $this->getLegalEntity();

        $this->getDivisions();



        $this->dictionaries = JsonHelper::searchValue('DICTIONARIES_PATH', [
            'PHONE_TYPE',
            'SETTLEMENT_TYPE',
            'DIVISION_TYPE',
        ]);

    }

    public function getLegalEntity()
    {
        $this->legalEntity = optional(auth()->user()->person->employee, function ($employee) {
           return $this->legalEntity = $employee->legalEntity;
        });
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function tableHeaders(): void
    {
       $this->tableHeaders  = [
           __('ID E-health '),
           __('Назва'),
           __('Тип'),
           __('Телефон'),
           __('Email'),
           __('Статус'),
           __('Дія'),
       ];
    }


    public function create()
    {
       $this->division = [];
       $this->addresses = [];

        $this->openModal();
    }

    public function store()
    {
        $this->resetErrorBag();
        $this->validate();
        $division = new Division();
        $division->fill($this->division);
        $this->legalEntity->division()->save($division);
        $this->closeModal();
        $this->getDivisions();
        $this->resetErrorBag();
    }

    public function edit($id)
    {
        $this->division = Division::find($id)->toArray();
        $this->addresses = $this->division['addresses'] ?? [];
        $this->setAddressesFields();
        $this->mode = 'edit';
        $this->openModal();
    }

    public function setAddressesFields()
    {
        $this->dispatch('setAddressesFields',$this->addresses);
    }

    public function update(){

        $this->resetErrorBag();
        $this->validate();
        $divisionId = $this->division['id'];
        $division = Division::findOrFail($divisionId);
        $division->update($this->division);
        $this->division = [];
        $this->closeModal();
        $this->getDivisions();
        $this->resetErrorBag();
    }

    public function getDivisionsApi(): array
    {
        if ( !empty($this->legalEntity->uuid))
          return  $this->divisions[] = (new DivisionApi())->getDivisions(
                ['legal_entity_id' => auth()->user()->person->employee->uuid]
        );
        return (new DivisionApi())->getDivisions() ?? [];
    }

    public function getDivisions(): object
    {
      return $this->divisions = Division::all();
    }

    public function updateFieldAddresses($data)
    {
        $this->division['addresses'][$data['field']] = $data['value'];

    }

    public function render()
    {
        return view('livewire.division.division-form');
    }




}
