<?php

declare(strict_types=1);

namespace App\Livewire\Encounter;

use App\Classes\eHealth\Api\PatientApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Livewire\Encounter\Forms\Api\EncounterRequestApi;
use App\Repositories\MedicalEvents\Repository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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
        $formattedData = $this->prepareFormattedData();

        $this->validateFormatted($formattedData);
        $this->storeValidatedData($formattedData);
    }

    /**
     * Submit encrypted data about person encounter.
     *
     * @return void
     * @throws ApiException|Throwable
     */
    public function signPerson(): void
    {
        $formattedData = $this->prepareFormattedData();

        $this->validateFormatted($formattedData);
        $this->storeValidatedData($formattedData);

        if ($this->episodeType === 'new') {
            $this->createEpisode($formattedData['episode']);
        }

        $base64EncryptedData = $this->sendEncryptedData(
            $this->convertArrayKeysToSnakeCase($formattedData),
            $this->authUser->tax_id
        );

        $signedSubmitEncounter = EncounterRequestApi::buildSubmitEncounterPackage($formattedData, $base64EncryptedData);
        PatientApi::submitEncounter($this->patientUuid, $signedSubmitEncounter);
    }

    /**
     * Set required employee party data.
     *
     * @return void
     */
    protected function setEmployeePartyData(): void
    {
        $employeeUuid = $this->authEmployee->uuid;

        $this->form->encounter['performer']['identifier']['value'] = $employeeUuid;
        $this->form->episode['careManager']['identifier']['value'] = $employeeUuid;
    }

    /**
     * Set default encounter period date.
     *
     * @return void
     */
    private function setDefaultDate(): void
    {
        $now = CarbonImmutable::now();

        $this->form->encounter['period'] = [
            'start' => $now->format('H:i'),
            'end' => $now->addMinutes(15)->format('H:i')
        ];
    }

    /**
     * Prepare formatted data.
     *
     * @return array
     */
    protected function prepareFormattedData(): array
    {
        $encounterRepository = Repository::encounter();

        $data = [
            'encounter' => $encounterRepository->formatEncounterRequest(
                $this->form->encounter,
                $this->form->conditions,
                $this->episodeType === 'new'
            ),
            'episode' => $this->episodeType === 'new'
                ? $encounterRepository->formatEpisodeRequest($this->form->episode, $this->form->encounter['period'])
                : [],
            'conditions' => $encounterRepository->formatConditionsRequest($this->form->conditions),
            'immunizations' => !empty($this->form->immunizations)
                ? $encounterRepository->formatImmunizationsRequest($this->form->immunizations)
                : [],
            'diagnosticReports' => !empty($this->form->diagnosticReports)
                ? $encounterRepository->formatDiagnosticReportsRequest($this->form->diagnosticReports)
                : [],
            'observations' => !empty($this->form->observations)
                ? $encounterRepository->formatObservationsRequest($this->form->observations)
                : [],
        ];

        // Remove empty
        return array_filter($data);
    }

    /**
     * Validate formatted data.
     *
     * @param  array  $formattedData
     * @return void
     */
    protected function validateFormatted(array $formattedData): void
    {
        try {
            $this->form->validateForm('encounter', $formattedData['encounter']);

            if (isset($formattedData['episode'])) {
                $this->form->validateForm('episode', $formattedData['episode']);
            }

            foreach ($formattedData['conditions'] as $formattedCondition) {
                $this->form->validateForm('conditions', $formattedCondition);
            }

            if (isset($formattedData['immunizations'])) {
                foreach ($formattedData['immunizations'] as $formattedImmunization) {
                    $this->form->validateForm('immunizations', $formattedImmunization);
                }
            }

            if (isset($formattedData['diagnosticReports'])) {
                foreach ($formattedData['diagnosticReports'] as $formattedDiagnosticReport) {
                    $this->form->validateForm('diagnosticReports', $formattedDiagnosticReport);
                }
            }

            if (isset($formattedData['observations'])) {
                foreach ($formattedData['observations'] as $formattedObservation) {
                    $this->form->validateForm('observations', $formattedObservation);
                }
            }
        } catch (ValidationException $e) {
            $this->dispatch('flashMessage', [
                'message' => $e->validator->errors()->first(),
                'type' => 'error'
            ]);

            return;
        }
    }

    /**
     * Store validated formatted data into DB.
     *
     * @param  array  $formattedData
     * @return void
     * @throws Throwable
     */
    protected function storeValidatedData(array $formattedData): void
    {
        DB::transaction(function () use ($formattedData) {
            $createdEncounterId = Repository::encounter()->store($formattedData['encounter'], $this->patientId);

            Repository::episode()->store($formattedData['episode'], $createdEncounterId);

            Repository::condition()->store($formattedData['conditions'], $createdEncounterId);

            if (isset($formattedData)) {
                Repository::immunization()->store($formattedData['immunizations'], $createdEncounterId);
            }

            if (isset($formattedData)) {
                Repository::observation()->store($formattedData['observations'], $createdEncounterId);
            }
        });
    }

    /**
     * Create episode for patient.
     *
     * @param  array  $formattedEpisode
     * @return void
     */
    protected function createEpisode(array $formattedEpisode): void
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
}
