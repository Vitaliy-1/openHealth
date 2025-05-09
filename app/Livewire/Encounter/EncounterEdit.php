<?php

declare(strict_types=1);

namespace App\Livewire\Encounter;

use App\Repositories\MedicalEvents\Repository;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Throwable;

class EncounterEdit extends EncounterComponent
{
    #[Locked]
    public int $encounterId;

    public function mount(int $patientId, int $encounterId): void
    {
        $this->patientId = $patientId;
        $this->encounterId = $encounterId;

        $encounter = $this->form->encounter = Repository::encounter()->get($this->encounterId);
        if (!$encounter) {
            abort(404);
        }
        $this->form->episode = Repository::episode()->get($this->encounterId);

        $this->form->conditions = Repository::condition()->get($this->encounterId);
        $this->form->conditions = Repository::encounter()->formatConditions($this->form->conditions, $this->form->encounter['diagnoses']);

        $this->form->immunizations = Repository::immunization()->get($this->encounterId);
        $this->form->immunizations = Repository::immunization()->formatForView($this->form->immunizations);

        $this->setDefaultDate();
        $this->setPatientData();
        $this->getDictionary();

        $this->adjustEpisodeTypes();
        $this->adjustEncounterClasses();
        $this->adjustEncounterTypes();

        $this->getDivisionData();
        $this->setCertificateAuthority();
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
            $this->form->episode,
            $this->patientId
        );
        Repository::condition()->store($this->form->conditions, $createdEncounterId);
    }

    /**
     * Set default encounter period date.
     *
     * @return void
     */
    private function setDefaultDate(): void
    {
        $this->form->encounter['period']['date'] = CarbonImmutable::parse($this->form->encounter['period']['start'])->format('Y-m-d');
        $this->form->encounter['period']['start'] = CarbonImmutable::parse($this->form->encounter['period']['start'])->format('H:i');
        $this->form->encounter['period']['end'] = CarbonImmutable::parse($this->form->encounter['period']['end'])->format('H:i');
    }
}
