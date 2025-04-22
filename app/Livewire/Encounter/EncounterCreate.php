<?php

declare(strict_types=1);

namespace App\Livewire\Encounter;

use App\Classes\eHealth\Api\PatientApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Models\Employee\Employee;
use App\Repositories\MedicalEvents\Repository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Str;
use Throwable;

class EncounterCreate extends EncounterComponent
{
    public function mount(int $patientId): void
    {
        $this->initializeComponent($patientId);
        $this->setEmployeePartyData();
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
        $encounterRepository = Repository::encounter();

        $formattedEncounter = $encounterRepository->formatEncounterRequest($this->form->encounter, $this->form->conditions);
        $formattedEpisode = $encounterRepository->formatEpisodeRequest($this->form->episode, $this->form->encounter['period']);
        $formattedConditions = $encounterRepository->formatConditionsRequest($this->form->conditions);
        $formattedImmunizations = !empty($this->form->immunizations) ? $encounterRepository->formatImmunizationsRequest($this->form->immunizations) : null;
        $formattedObservations = !empty($this->form->observations) ? $encounterRepository->formatObservationsRequest($this->form->observations) : null;

        if (!empty($this->form->observations)) {
            $formattedObservations = $encounterRepository->formatObservationsRequest($this->form->observations);
        }

        // Validate formatted data
        try {
            $this->form->validateForm('encounter', $formattedEncounter);
            $this->form->validateForm('episode', $formattedEpisode);

            foreach ($formattedConditions['conditions'] as $formattedCondition) {
                $this->form->validateForm('conditions', ['conditions' => [$formattedCondition]]);
            }

            if (isset($formattedImmunizations)) {
                foreach ($formattedImmunizations['immunizations'] as $formattedImmunization) {
                    $this->form->validateForm('immunizations', ['immunizations' => [$formattedImmunization]]);
                }
            }

            if (isset($formattedObservations)) {
                foreach ($formattedObservations['observations'] as $formattedObservation) {
                    $this->form->validateForm('observations', ['observations' => [$formattedObservation]]);
                }
            }
        } catch (ValidationException $e) {
            $this->dispatch('flashMessage', [
                'message' => $e->validator->errors()->first(),
                'type' => 'error'
            ]);

            return;
        }

        DB::transaction(function () use ($formattedEncounter, $formattedEpisode, $formattedConditions, $formattedImmunizations, $formattedObservations) {
            $createdEncounterId = Repository::encounter()->store(
                $formattedEncounter['encounter'],
                $formattedEpisode['episode'],
                $this->patientId
            );

            Repository::condition()->store($formattedConditions['conditions'], $createdEncounterId);

            if (isset($formattedImmunizations)) {
                Repository::immunization()->store($formattedImmunizations['immunizations'], $createdEncounterId);
            }

            if (isset($formattedObservations)) {
                Repository::observation()->store($formattedObservations['observations'], $createdEncounterId);
            }
        });

        $encounter = PatientApi::getShortEncounterBySearchParams($this->patientUuid);
        $job = PatientApi::getJobsDetailsById('683408acf712c70046293a6a');
    }

    /**
     * Submit encrypted data about person encounter.
     *
     * @return void
     * @throws ApiException
     */
    public function signPerson(): void
    {
        // Note: No update operations are allowed. All IDs, submitted as PK, should be unique for eHealth.
        // TODO: додати перевірку на унікальність uuid, трішки потім. uuid має бути унікальний для пацієнта а не унікальним в цілому?
        $encounterRepository = Repository::encounter();

        $formattedEncounter = $encounterRepository->formatEncounterRequest($this->form->encounter, $this->form->conditions);
        $formattedEpisode = $encounterRepository->formatEpisodeRequest($this->form->episode, $this->form->encounter['period']);
        $formattedConditions = $encounterRepository->formatConditionsRequest($this->form->conditions);
        $formattedImmunizations = !empty($this->form->immunizations) ? $encounterRepository->formatImmunizationsRequest($this->form->immunizations) : [];
        $formattedObservations = !empty($this->form->observations) ? $encounterRepository->formatObservationsRequest($this->form->observations) : [];

        // Validate formatted data
        try {
            $this->form->validateForm('encounter', $formattedEncounter);
            $this->form->validateForm('episode', $formattedEpisode);

            foreach ($formattedConditions['conditions'] as $formattedCondition) {
                $this->form->validateForm('conditions', ['conditions' => [$formattedCondition]]);
            }

            if (!empty($formattedImmunizations)) {
                foreach ($formattedImmunizations['immunizations'] as $formattedImmunization) {
                    $this->form->validateForm('immunizations', ['immunizations' => [$formattedImmunization]]);
                }
            }

            if (!empty($formattedObservations)) {
                foreach ($formattedObservations['observations'] as $formattedObservation) {
                    $this->form->validateForm('observations', ['observations' => [$formattedObservation]]);
                }
            }
        } catch (ValidationException $e) {
            $this->dispatch('flashMessage', [
                'message' => $e->validator->errors()->first(),
                'type' => 'error'
            ]);

            return;
        }

        $this->createEpisode($formattedEpisode['episode']);

        $base64EncryptedData = $this->sendEncryptedData(
            array_merge(
                $formattedEncounter,
                $this->convertArrayKeysToSnakeCase($formattedConditions),
                $this->convertArrayKeysToSnakeCase($formattedImmunizations),
                $this->convertArrayKeysToSnakeCase($formattedObservations)
            ),
            Auth::user()->tax_id
        );

        $prepareSubmitEncounter = [
            'visit' => (object)[
                'id' => Str::uuid()->toString(),
                'period' => (object)[
                    'start' => $formattedEncounter['encounter']['period']['start'],
                    'end' => $formattedEncounter['encounter']['period']['end']
                ]
            ],
            'signed_data' => $base64EncryptedData
        ];

        $submitEncounter = PatientApi::submitEncounter($this->patientUuid, $prepareSubmitEncounter);
        dd($submitEncounter);
    }

    /**
     * Create episode for patient.
     *
     * @param  array  $formattedEpisode
     * @return void
     */
    private function createEpisode(array $formattedEpisode): void
    {
        try {
            PatientApi::createEpisode($this->patientUuid, $this->convertArrayKeysToSnakeCase($formattedEpisode));
        } catch (ApiException) {
            $this->dispatch('flashMessage', [
                'message' => __('Виникла помилка при створенні епізоду. Зверніться до адміністратора.'),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Set required employee party data.
     *
     * @return void
     */
    protected function setEmployeePartyData(): void
    {
        // TODO: потім взяти employee авторизованого
        $employee = Employee::find(1);

        $this->form->encounter['performer']['identifier']['value'] = $employee?->uuid;
        $this->form->episode['careManager']['identifier']['value'] = $employee?->uuid;
    }

    /**
     * Set default encounter period date.
     *
     * @return void
     */
    private function setDefaultDate(): void
    {
        $now = CarbonImmutable::now();
        $this->form->encounter['period']['start'] = $now->format('H:i');
        $this->form->encounter['period']['end'] = $now->addMinutes(15)->format('H:i');
    }
}
