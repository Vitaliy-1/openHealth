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


    public ?object $legalEntity;

     public string $mode = 'create';

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


    protected $listeners = ['addressDataFetched'];

    public function mount($id = '')
    {
        if ( !empty($id)) {
            $this->getDivision($id);
            $this->mode = 'edit';
        }
        $this->getLegalEntity();
        $this->dictionaries = JsonHelper::searchValue('DICTIONARIES_PATH', [
            'PHONE_TYPE',
            'SETTLEMENT_TYPE',
            'DIVISION_TYPE',
        ]);
    }

    public function getLegalEntity()
    {
        $this->legalEntity = auth()->user()->legalEntity;
    }

    public function getDivision($id)
    {
        $this->division = Division::find($id)->toArray();
        $this->division['phones'] = $this->division['phones'][0];
        $this->division['addresses'] = $this->division['addresses'][0];
    }

    public function fetchDataFromAddressesComponent():void
    {
        $this->dispatch('fetchAddressData');
    }



    public function addressDataFetched($addressData): void
    {
        $this->division['addresses'] = $addressData;

    }

    public function validateDivision(): void
    {
        $this->resetErrorBag();
        $this->validate();

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
        $this->resetErrorBag();
    }

    public function edit(Division $division)
    {
        $this->mode = 'edit';
        $this->division = $division->toArray();

        $this->setAddressesFields();
    }

    public function setAddressesFields():void
    {
        $this->dispatch('setAddressesFields',$this->division['addresses'] ?? []);
    }

    public function update():void
    {

        $this->fetchDataFromAddressesComponent();
        $this->dispatch('address-data-fetched');
        $this->validateDivision();
        $division = Division::find($this->division['id']);

        $this->updateOrCreate($division);


    }

    public function updateOrCreate(Division $division)
    {
        $response = $this->mode === 'edit'
            ? $this->updateDivision()
            : $this->createDivision();

        if ($response) {
            $this->saveDivision($division, $response);

            return redirect()->route('division.index');
        }

        $this->dispatch('flashMessage', ['message' => __('Інформацію не оновлено'), 'type' => 'error']);
    }

    private function updateDivision(): array
    {
        return DivisionRequestApi::updateDivisionRequest($this->division['uuid'],removeEmptyKeys($this->division));
    }

    private function createDivision(): array
    {
        $division = removeEmptyKeys($this->division);
        return DivisionRequestApi::createDivisionRequest($division);
    }



    private function saveDivision(Division $division, array $response): void
    {

        $division->fill($response);
        $division->setAttribute('uuid', $response['id']);
        $division->setAttribute('legal_entity_uuid', $response['legal_entity_id']);
        $division->setAttribute('external_id', $response['external_id']);
        $division->setAttribute('status', $response['status']);
        $this->legalEntity->division()->save($division);
    }

    public function notWorking($day)
    {
        $this->division['working_hours'][$day][] = [];
    }




    public function render()
    {

    return view('livewire.division.division-form-create');

    }

}
