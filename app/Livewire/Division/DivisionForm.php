<?php

namespace App\Livewire\Division;

use App\Classes\eHealth\Api\DivisionApi;
use App\Helpers\JsonHelper;
use App\Livewire\Division\Api\DivisionRequestApi;
use App\Livewire\Division\Forms\DivisionFormRequest;
use App\Models\Division;
use App\Models\LegalEntity;
use Livewire\Attributes\Validate;
use Livewire\Component;

class DivisionForm extends Component
{
    // #[Validate([
    //     'division.name' => 'required|min:6|max:255',
    //     'division.type' => 'required',
    //     'division.email' => 'required',
    //     'division.phones.number' => 'required|string',
    //     'division.phones.type' => 'required',
    //     'division.addresses' => 'required',
    // ])]

    // public ?array $division = [];

    public DivisionFormRequest $formService;


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
        } else {
            $this->initWorkingHours();
        }

        $this->getLegalEntity();

        $this->dictionaries = [
            'PHONE_TYPE' => dictionary()->getDictionary('PHONE_TYPE', true)['values'],
            'SETTLEMENT_TYPE' => dictionary()->getDictionary('SETTLEMENT_TYPE', true)['values'],
            'DIVISION_TYPE' => dictionary()->getDictionary('DIVISION_TYPE', true)['values']
        ];
    }


    protected function initWorkingHours()
    {
        $arr = [];

        foreach ($this->working_hours as $day => $name) {
            $arr[$day] = [];
        }

        $this->formService->setDivisionParam('working_hours', $arr);
    }

    public function getLegalEntity()
    {
        $this->legalEntity = auth()->user()->legalEntity;
    }

    public function getDivision($id)
    {
        $this->formService->setDivision(Division::find($id)->toArray());
        $this->formService->setDivisionParam('phones', $this->formService->getDivisionParam('phones')[0]);
        $this->formService->setDivisionParam('addresses', $this->formService->getDivisionParam('addresses')[0]);

        if ($this->formService->isDivisionParamExistAndNull('working_hours')) {
            // $this->formService->unsetDivisionParam('working_hours');
            $this->initWorkingHours();
        }
    }

    public function fetchDataFromAddressesComponent():void
    {
        $this->dispatch('fetchAddressData');
    }

    public function addressDataFetched($addressData): void
    {
        // dd('in addressDataFetched');
        $this->formService->setDivisionParam('addresses', $addressData);
    }

    public function validateDivision(): bool
    {
        // $this->resetErrorBag();

        // $this->validate();
        // dd($this->formService->getDivision());
        $error = $this->formService->doValidation($this->mode);

        if ($error) {
            $this->dispatch('flashMessage', ['message' => $error, 'type' => 'error']);

            return false;
        } else {
            return true;
        }
    }

    public function create()
    {
        $this->mode = 'create';
    }

    public function store()
    {
        $this->fetchDataFromAddressesComponent();
        $this->dispatch('address-data-fetched');

        if ($this->validateDivision()) {
            $this->updateOrCreate(new Division());
        }

        // $this->resetErrorBag();
    }

    public function edit(Division $division)
    {
        dd('edit');

        $this->mode = 'edit';
        $this->formService->setDivision($division->toArray());

        // $this->setAddressesFields();
    }

    // public function setAddressesFields():void
    // {
    //     $this->dispatch('setAddressesFields',$this->formService->getDivisionParam('addresses') ?? []);
    // }

    /**
     * Checks if the residence address in the legal entity form is an array and not empty.
     * If it is, increment the current step and put the legal entity in the cache.
     */
    public function checkAndProceedToNextStep(): void
    {
        dd('IN DIVISION checkAndProceedToNextStep');
    }

    public function update():void
    {
        $breakpoint = true;
        $this->fetchDataFromAddressesComponent();

        // $this->dispatch('address-data-fetched');

        if ($this->validateDivision()) {
            $division = Division::find($this->formService->getDivisionParam('id'));

            $this->updateOrCreate($division);
        }
    }

    public function updateOrCreate(Division $division)
    {
        // dd($this->formService->division);
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
        // dd($this->formService->getDivision());
        return DivisionRequestApi::updateDivisionRequest(
            $this->formService->getDivisionParam('uuid'),
            removeEmptyKeys($this->formService->getDivision())
        );
    }

    private function createDivision(): array
    {
        // dd($this->formService->getDivision());
        $division = removeEmptyKeys($this->formService->getDivision());

        return DivisionRequestApi::createDivisionRequest($division);
    }

    private function saveDivision(Division $division, array $response): void
    {
        $division->fill($response);
        $division->setAttribute('legal_entity_id', $this->legalEntity->id); // TODO: Delete after testing
        $division->setAttribute('uuid', $response['id']);
        $division->setAttribute('legal_entity_uuid', $response['legal_entity_id']);
        $division->setAttribute('external_id', $response['external_id']);
        $division->setAttribute('status', $response['status']);
        // dd($division);
        $this->legalEntity->division()->save($division);
    }

    public function notWorking($day, $showWork)
    {
        // dd($day, $showWork);
        if ($showWork) {
            $working_hours = $this->formService->getDivisionParam('working_hours');

            if (isset($working_hours[$day])) {
                unset($working_hours[$day]);
            }

            $this->formService->setDivisionParam('working_hours', $working_hours);
        }
    }

    public function render()
    {
        $currentDivision = [];
        $_division = $this->formService->getDivision();

        if (!empty($_division)) {
            $currentDivision['name'] = !empty($_division['name'])
                ? $_division['name']
                : '';
            $currentDivision['type'] = !empty($_division['type'])
                ? dictionary()->getDictionary('DIVISION_TYPE', true)['values'][$_division['type']]
                : '';
        }

        return view('livewire.division.division-form-create', compact('currentDivision'));
    }
}
