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
        'patientsFilter.birthDate' => 'required'
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

    public bool $showAdditionalParams = false;

    /**
     * Check if the search person's request found someone.
     *
     * @var bool
     */
    public bool $searchPerformed = false;

    public array $tableHeaders = [];

    public array $patients = [
//        0 => [
//            "birth_country" => "Україна",
//            "birth_date" => "2005-07-02",
//            "birth_settlement" => "Київ",
//            "first_name" => "Михайло",
//            "gender" => "MALE",
//            "id" => "f3c55699-fc11-4934-a54b-03c100785807",
//            "last_name" => "Михавко",
//            "second_name" => "Михайлович",
//            'phones' => [
//                0 => [
//                    'type' => 'MOBILE',
//                    'number' => '+380503410870'
//                ]
//            ]
//        ]
    ];

    public ?string $selectedPatientId = null;

    public function render(): View
    {
        return view('livewire.patient._parts._search_confidant_person');
    }

    public function mount(): void
    {
        $this->tableHeaders = [
            __('ПІБ'),
            __('forms.phones'),
            __('Д.Н.'),
            __('forms.RNOCPP') . '(' . __('forms.ipn') . ')',
            __('forms.action')
        ];
    }

    /**
     * Search for person with provided filters.
     *
     * @return void
     * @throws ApiException
     */
    public function searchForPerson(): void
    {
        $this->validate();

        $buildSearchRequest = PatientRequestApi::buildSearchForPerson($this->patientsFilter);

        $this->patients = PersonApi::searchForPersonByParams($buildSearchRequest);
        $this->searchPerformed = true;
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
        $this->dispatch('patient-selected');
    }

    /**
     * Remove selected confidant person.
     *
     * @return void
     */
    public function removeConfidantPerson(): void
    {
        $this->patients = [];
        $this->selectedPatientId = null;

        $this->dispatch('confidant-person-removed');
    }
}
