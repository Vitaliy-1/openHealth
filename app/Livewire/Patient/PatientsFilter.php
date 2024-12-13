<?php

namespace App\Livewire\Patient;

use App\Classes\eHealth\Api\PersonApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Livewire\Patient\Forms\Api\PatientRequestApi;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PatientsFilter extends Component
{
    #[Validate([
        'patientsFilter.firstName' => 'required',
        'patientsFilter.lastName' => 'required',
        'patientsFilter.birthDate' => 'required',

    ])]
    public array $patientsFilter = [
        'firstName' => '',
        'lastName' => '',
        'secondName' => '',
        'birthDate' => '',
        'taxId' => '',
        'phoneNumber' => '',
        'birthCertificate' => ''
    ];

    public array $tableHeaders = [];

    public array $patients = [];

    public ?string $selectedPatientId = null;

    public function render(): View
    {
        return view('livewire.patient._parts._search_confidant_person');
    }

    public function mount(): void
    {
        $this->tableHeaders = [
            __("Ім'я"),
            __('Прізвище'),
            __('По батькові'),
            __('Дата народження'),
            __('Місце народження'),
        ];
    }

    /**
     * Do search for person in DB with provided filters.
     *
     * @return void
     * @throws ApiException
     */
    public function searchPerson(): void
    {
        $this->validate();

        $buildSingleSearch = PatientRequestApi::buildSearchForPerson($this->patientsFilter);
        $this->patients = PersonApi::searchForPersonByParams($buildSingleSearch);
    }

    /**
     * Choose confidant from provided list.
     *
     * @param  string  $id
     * @return void
     */
    public function chooseConfidantPerson(string $id): void
    {
        $this->selectedPatientId = $id;
        $this->dispatch('confidant-person-selected', $id);
        $this->dispatch('close-search-form');
    }
}
