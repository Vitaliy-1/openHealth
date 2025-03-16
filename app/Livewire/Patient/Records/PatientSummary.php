<?php

declare(strict_types=1);

namespace App\Livewire\Patient\Records;

use App\Classes\eHealth\Api\PatientApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Livewire\Patient\Forms\Api\PatientRequestApi;
use Illuminate\Contracts\View\View;

class PatientSummary extends BasePatientComponent
{
    public array $episodes;
    public array $diagnoses;
    public array $observations;

    public function render(): View
    {
        return view('livewire.patient.records.patient-summary');
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
            $shortEpisodes = PatientApi::getShortEpisodes($this->uuid, $buildGetShortEpisodes);

            $this->episodes = $shortEpisodes;
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
            $activeDiagnoses = PatientApi::getActiveDiagnoses($this->uuid, $buildGetActiveDiagnoses);

            $this->diagnoses = $activeDiagnoses;
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
            $observations = PatientApi::getObservations($this->uuid, $buildGetObservations);

            $this->observations = $observations;
        } catch (ApiException) {
            $this->dispatch('flashMessage', [
                'message' => __('Не вдалося отримати обстеження. Спробуйте пізніше.'),
                'type' => 'error'
            ]);
        }
    }
}
