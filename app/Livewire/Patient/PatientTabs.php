<?php

declare(strict_types=1);

namespace App\Livewire\Patient;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class PatientTabs extends Component
{
    /**
     * ID of the current patient.
     * @var string|int
     */
    #[Locked]
    public string|int $patientId;

    /**
     * Name of the tab that is active now.
     * @var string
     */
    public string $activeTab;

    /**
     * Tabs for navigation with title and component names.
     * @var array|array[]
     */
    public array $navTabs = [
        'patient-data' => [
            'title' => 'Дані пацієнта',
            'component' => 'patient.patient-data'
        ],
        'summary' => [
            'title' => 'Зведені дані',
            'component' => 'patient.patient-summary'
        ],
        'episodes' => [
            'title' => 'Епізоди',
            'component' => 'patient.patient-episodes'
        ]
    ];

    /**
     * Basic info about a patient from a search.
     * @var array
     */
    public array $patient;

    public function mount(string|int $id, string $tab = 'patient-data'): void
    {
        $this->patientId = $id;
        $this->activeTab = $tab;
        $this->patient = session('temp_patient_data_' . $id);
    }

    public function render(): View
    {
        return view('livewire.patient.patient-tabs');
    }

    /**
     * Switches the active tab and redirects to the appropriate tab view.
     *
     * @param  string  $tab  The key of the tab to switch to.
     * @return void
     */
    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;

        $this->redirectRoute('patient.tabs', ['id' => $this->patientId, 'tab' => $tab]);
    }
}
