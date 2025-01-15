<?php

namespace App\Livewire\Division;

use App\Classes\eHealth\Api\DivisionApi;
use App\Helpers\JsonHelper;
use App\Livewire\Division\Api\DivisionRequestApi;
use App\Models\Division;
use App\Models\LegalEntity;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class DivisionIndex extends Component
{

    use WithPagination;

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
        foreach ($responses as $response) {
            $division = Division::firstOrNew(['uuid' => $response['id']]);
            $division->fill($response);
            $division->setAttribute('uuid', $response['id']);
            $division->setAttribute('legal_entity_uuid', $response['legal_entity_id']);
            $division->setAttribute('external_id', $response['external_id']);
            $division->setAttribute('status', $response['status']);

            if (!empty($response['location'])) {
                $division->setAttribute('location', json_encode($response['location']));
            }

            $this->legalEntity->division()->save($division);
        }
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
