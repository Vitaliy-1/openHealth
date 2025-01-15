<?php

namespace App\Livewire\Division;

use Livewire\Component;
use App\Models\Division;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Repositories\AddressRepository;
use App\Livewire\Division\Api\DivisionRequestApi;

class DivisionIndex extends Component
{
    use WithPagination;

    protected ?AddressRepository $addressRepository;

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

    public function boot(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    public function mount()
    {
        $this->tableHeaders();

        $this->getLegalEntity();

        $this->dictionaries = [
            'PHONE_TYPE' => dictionary()->getDictionary('PHONE_TYPE')->getValue('values')->toArray(),
            'SETTLEMENT_TYPE' => dictionary()->getDictionary('SETTLEMENT_TYPE', true)['values'],
            'DIVISION_TYPE' => dictionary()->getDictionary('DIVISION_TYPE', true)['values'],
        ];
    }

    #[On('refreshPage')]
    public function refreshPage()
    {
        $this->dispatch('$refresh');
    }

    public function getLegalEntity()
    {
        $this->legalEntity = auth()->user()->legalEntity;
    }

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

    public function syncDivisions()
    {
        $syncDivisions = DivisionRequestApi::syncDivisionRequest($this->legalEntity->uuid);

        $this->syncDivisionsSave($syncDivisions);

        $this->dispatch('refreshPage');
        $this->dispatch('flashMessage', ['message' => __('Інформацію успішно оновлено'), 'type' => 'success']);
    }

    public function syncDivisionsSave($responses)
    {
        DB::transaction(function () use ($responses) {
            foreach ($responses as $response) {
                $addressData = $response['addresses'];
                unset($response['addresses']);

                $response['phones'] = $response['phones'][0];

                $division = Division::firstOrNew(['uuid' => $response['id']]);
                $division->fill($response);
                $division->setAttribute('uuid', $response['id']);
                $division->setAttribute('legal_entity_uuid', $response['legal_entity_id']);
                $division->setAttribute('external_id', $response['external_id']);
                $division->setAttribute('status', $response['status']);

                $savedDivision = $this->legalEntity->division()->save($division);

                $this->addressRepository->addAddresses($savedDivision, $addressData);
            }
        });
    }

    public function activate(Division $division): void
    {
        DivisionRequestApi::activateDivisionRequest($division['uuid']);

        $division->setAttribute('status', 'ACTIVE');
        $division->save();

        $this->dispatch('refreshPage');
    }

    public function deactivate(Division $division): void
    {
        DivisionRequestApi::deactivateDivisionRequest($division['uuid']);

        $division->setAttribute('status', 'INACTIVE');
        $division->save();

        $this->dispatch('refreshPage');
    }

    public function render()
    {
        $perPage = config('pagination.per_page');
        $divisions = $this->legalEntity->division()->orderBy('uuid')->paginate($perPage);

        return view('livewire.division.division-form', compact('divisions'));
    }
}
