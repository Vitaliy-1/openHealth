<?php

declare(strict_types=1);

namespace App\Livewire\Encounter;

use App\Classes\eHealth\Api\PatientApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Models\LegalEntity;
use App\Repositories\MedicalEvents\Repository;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Throwable;

class EncounterEdit extends EncounterComponent
{
    #[Locked]
    public int $encounterId;

    public function mount(LegalEntity $legalEntity, int $patientId, int $encounterId): void
    {
        $this->initializeComponent($patientId);
        $this->encounterId = $encounterId;

        $encounter = Repository::encounter()->get($this->encounterId);

        if (!$encounter) {
            abort(404);
        }

        $this->form->encounter = $encounter;

        $this->form->episode = $this->getEpisode();

        $this->form->conditions = Repository::condition()->get($this->encounterId);
        $this->form->conditions = Repository::condition()->formatForView(
            $this->form->conditions,
            $this->form->encounter['diagnoses']
        );

        $this->form->immunizations = Repository::immunization()->get($this->encounterId);
        $this->form->immunizations = Repository::immunization()->formatForView($this->form->immunizations);

        $this->form->diagnosticReports = Repository::diagnosticReport()->get($this->encounterId);
        $this->form->diagnosticReports = Repository::diagnosticReport()->formatForView($this->form->diagnosticReports);

        $this->form->observations = Repository::observation()->get($this->encounterId);
        $this->form->observations = Repository::observation()->formatForView($this->form->observations);

        $this->form->procedures = Repository::procedure()->get($this->encounterId);
        $this->form->procedures = Repository::procedure()->formatForView($this->form->procedures);

        $this->form->clinicalImpressions = Repository::clinicalImpression()->get($this->encounterId);

        $this->setDefaultDate();
    }

    /**
     * Validate and save data.
     *
     * @return void
     * @throws Throwable
     */
    public function save(): void
    {
        $formattedEncounter = Repository::encounter()->formatPeriod($this->form->encounter);

        // Validate formatted data
        try {
            $this->form->validateForm('encounter', $formattedEncounter);
            $this->form->validateForm('episode', $this->form->episode);
            $this->form->validateForm('conditions', $this->form->conditions);
            $this->form->validateForm('immunizations', $this->form->immunizations);
        } catch (ValidationException $e) {
            $this->dispatch('flashMessage', [
                'message' => $e->validator->errors()->first(),
                'type' => 'error'
            ]);

            return;
        }

        $createdEncounterId = Repository::encounter()->store(
            $formattedEncounter,
            $this->patientId
        );
        Repository::condition()->store($this->form->conditions, $createdEncounterId);
    }

    /**
     * Retrieve the episode from the database, if not found, retrieve it from the API, save it to the database, and set it to the form.
     *
     * @return array
     */
    private function getEpisode(): array
    {
        $episode = Repository::episode()->get($this->encounterId);

        if ($episode) {
            return $episode;
        }

        try {
            $episodeData = PatientApi::getEpisodeById(
                $this->patientUuid,
                $this->form->encounter['episode']['identifier']['value']
            );

            Repository::episode()->store($this->convertArrayKeysToCamelCase($episodeData), $this->encounterId);

            return Repository::episode()->get($this->encounterId);
        } catch (ApiException|Throwable) {
            $this->flashGeneralError();

            return [];
        }
    }

    /**
     * Set default encounter period date.
     *
     * @return void
     */
    private function setDefaultDate(): void
    {
        $this->form->encounter['period'] = [
            'date' => CarbonImmutable::parse($this->form->encounter['period']['start'])->format('Y-m-d'),
            'start' => CarbonImmutable::parse($this->form->encounter['period']['start'])->format('H:i'),
            'end' => CarbonImmutable::parse($this->form->encounter['period']['end'])->format('H:i')
        ];
    }
}
