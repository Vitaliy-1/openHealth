<?php

declare(strict_types=1);

namespace App\Livewire\Patient;

use App\Classes\eHealth\Api\PatientApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Livewire\Patient\Forms\Api\PatientRequestApi;
use App\Models\Person\Person;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PatientSummary extends Component
{
    /**
     * Info about the patient.
     * @var array
     */
    public array $patient;

    protected string|int $patientUuid;

    public function boot(): void
    {
        $this->patientUuid = $this->getPatientUuid();
    }

    public function render(): View
    {
        return view('livewire.patient.patient-summary');
    }

    /**
     * Get patient episodes.
     *
     * @return void
     */
    public function getEpisodes(): void
    {
        try {
            $buildGetShortEpisodes = PatientRequestApi::buildGetShortEpisodes();
            $shortEpisodes = PatientApi::getShortEpisodes($this->patientUuid, $buildGetShortEpisodes);

            $this->patient['episodes'] = $shortEpisodes;
        } catch (ApiException) {
            $this->dispatch('flashMessage', [
                'message' => __('Не вдалося отримати епізоди. Спробуйте пізніше.'),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Get patient diagnoses.
     *
     * @return void
     */
    public function getDiagnoses(): void
    {
        try {
            $buildGetActiveDiagnoses = PatientRequestApi::buildGetActiveDiagnoses();
            $activeDiagnoses = PatientApi::getActiveDiagnoses($this->patientUuid, $buildGetActiveDiagnoses);

            $this->patient['diagnoses'] = $activeDiagnoses;
        } catch (ApiException) {
            $this->dispatch('flashMessage', [
                'message' => __('Не вдалося отримати діагнози. Спробуйте пізніше.'),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Get patient observations.
     *
     * @return void
     */
    public function getObservations(): void
    {
        try {
            $buildGetObservations = PatientRequestApi::buildGetObservations();
            $observations = PatientApi::getObservations($this->patientUuid, $buildGetObservations);

            $this->patient['observations'] = $observations;
        } catch (ApiException) {
            $this->dispatch('flashMessage', [
                'message' => __('Не вдалося отримати обстеження. Спробуйте пізніше.'),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Get the patient UUID from the URL or find it by ID in the DB.
     *
     * @return string
     */
    protected function getPatientUuid(): string
    {
        return uuid_is_valid($this->patient['id'])
            ? $this->patient['id']
            : Person::find($this->patient['id'])->uuid;
    }
}
