<?php

namespace App\Livewire\Division;

use App\Classes\eHealth\Api\DivisionApi;
use App\Helpers\JsonHelper;
use App\Livewire\Division\Api\DivisionRequestApi;
use App\Models\Division;
use App\Models\LegalEntity;
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
        'division.addresses' => 'required',

    ])]

    public ?array $division = [];
    public ?object $divisions;

    public ?object $legalEntity;

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

    public ?array $tableHeaders = [];

    public bool $showModal = false;

    public string $mode = 'default';

    protected $listeners = ['addressDataFetched'];

    public function mount()
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
        $this->legalEntity =auth()->user()->legalEntity;
    }

//    public function openModal()
//    {
//        $this->showModal = true;
//    }
//
//    public function closeModal()
//    {
//        $this->showModal = false;
//        $this->getDivisions();
//        $this->resetErrorBag();
//        $this->division = [];
//    }

    public function tableHeaders(): void
    {
        $this->tableHeaders = [
            __('ID E-health '),
            __('Назва'),
            __('Тип'),
            __('Телефон'),
            __('Email'),
            __('Статус'),
            __('Дія'),
        ];
    }

    public function fetchDataFromAddressesComponent()
    {
        $this->dispatch('fetchAddressData');
    }



    public function addressDataFetched($addressData): void
    {
        $this->division['addresses'] = $addressData;

    }

    public function validateDivision(): bool
    {
        $this->resetErrorBag();
        $this->validate();
        if (isset($this->division['addresses']) && empty($this->division['addresses'])){
            return false;
        }

        return true;
    }

    public function create()
    {
        $this->mode = 'create';
    }

    public function store()
    {
        $this->fetchDataFromAddressesComponent();
        $this->dispatch('address-data-fetched');
        $this->validateDivision();
        $this->updateOrCreate(new Division());
        $this->getDivisions();
        $this->resetErrorBag();
    }

    public function edit(Division $division)
    {
        $this->mode = 'edit';
        $this->division = $division->toArray();
        $this->setAddressesFields();
    }

    public function setAddressesFields()
    {
        $this->dispatch('setAddressesFields',$this->division['addresses'] ?? []);
    }

    public function update(Division $division)
    {

        $this->fetchDataFromAddressesComponent();

        $this->dispatch('address-data-fetched');

        $divisionId = $this->division['id'];
        $division = $division::find($divisionId);
        $this->updateOrCreate($division);

    }

    public function updateOrCreate(Division $division): void
    {
        $response = $this->mode === 'edit'
            ? $this->updateDivision()
            : $this->createDivision();

        if ($response) {
            $this->saveDivision($division, $response);
        }
    }

    private function updateDivision(): array
    {
        return DivisionRequestApi::updateDivisionRequest($this->division['uuid'], $this->division);
    }

    private function createDivision(): array
    {
        return DivisionRequestApi::createDivisionRequest($this->division);
    }

    public function activate(Division $division): void
    {
        DivisionRequestApi::activateDivisionRequest($division['uuid']);
        $division->setAttribute('status', 'ACTIVE');
        $division->save();
        $this->getDivisions();
    }

    public function deactivate(Division $division): void
    {
        DivisionRequestApi::deactivateDivisionRequest($division['uuid']);
        $division->setAttribute('status', 'DEACTIVATED');
        $division->save();
        $this->getDivisions();
    }

    private function saveDivision(Division $division, array $response): void
    {
        $division->fill($this->division);
        $division->setAttribute('uuid', $response['id']);
        $division->setAttribute('addresses', $this->addresses);
        $division->setAttribute('legal_entity_uuid', $response['legal_entity_id']);
        $division->setAttribute('external_id', $response['external_id']);
        $division->setAttribute('status', $response['status']);

        $this->legalEntity->division()->save($division);
    }

    public function notWorking($day)
    {
        $this->division['working_hours'][$day][] = [];
    }


    public function getDivisions(): object
    {
        return $this->divisions = $this->legalEntity->division()->get();
    }

    public function render()
    {
        if ($this->mode === 'create')
        {
            return view('livewire.division.division-form-create');

        }
        return view('livewire.division.division-form');
    }

}
