<?php

declare(strict_types=1);

namespace App\Livewire\Encounter;

use App\Classes\eHealth\Api\PatientApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Models\Employee\Employee;
use App\Repositories\MedicalEvents\Repository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Str;
use Throwable;

class EncounterCreate extends EncounterComponent
{
    public function mount(int $patientId): void
    {
        $this->patientId = $patientId;

        $this->setEmployeePartyData();

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
        $encounterRepository = Repository::encounter();

        $formattedEncounter = $encounterRepository->formatEncounterRequest($this->form->encounter, $this->form->conditions);
        $formattedEpisode = $encounterRepository->formatEpisodeRequest($this->form->episode, $this->form->encounter['period']);
        $formattedConditions = $encounterRepository->formatConditionsRequest($this->form->conditions);

        if (!empty($this->form->immunizations)) {
            $formattedImmunizations = $encounterRepository->formatImmunizationsRequest($this->form->immunizations);
        }

        // Validate formatted data
        try {
            $this->form->validateForm('encounter', $formattedEncounter);
            $this->form->validateForm('episode', $formattedEpisode);
            foreach ($formattedConditions['conditions'] as $index => $formattedCondition) {
                $this->form->validateForm("conditions.$index", $formattedCondition);
            }

            if (isset($formattedImmunizations)) {
                foreach ($formattedImmunizations['immunizations'] as $index => $formattedImmunization) {
                    $this->form->validateForm("immunizations.$index", $formattedImmunization);
                }
            }
        } catch (ValidationException $e) {
            $this->dispatch('flashMessage', [
                'message' => $e->validator->errors()->first(),
                'type' => 'error'
            ]);

            return;
        }

        $createdEncounterId = Repository::encounter()->store(
            $formattedEncounter['encounter'],
            $formattedEpisode['episode'],
            $this->patientId
        );

        Repository::condition()->store($formattedConditions['conditions'], $createdEncounterId);

        if (isset($formattedImmunizations)) {
            Repository::immunization()->store($formattedImmunizations['immunizations'], $createdEncounterId);
        }

        $encounter = PatientApi::getShortEncounterBySearchParams($this->patientUuid);
        $job = PatientApi::getJobsDetailsById('67e64af98c67240046bb4b2f');
    }

    /**
     * Submit encrypted data about person encounter.
     *
     * @return void
     * @throws ApiException|ValidationException
     */
    public function signPerson(): void
    {
        try {
            $this->form->rulesForModelValidate(['encounter', 'episode', 'conditions']);
        } catch (ValidationException $e) {
            $this->dispatch('flashMessage', [
                'message' => $e->validator->errors()->first(),
                'type' => 'error'
            ]);

            throw $e;
        }

        $this->createEpisode();

        // Note: No update operations are allowed. All IDs, submitted as PK, should be unique for eHealth.
        // TODO: додати перевірку на унікальність uuid, трішки потім. uuid має бути унікальний для пацієнта а не унікальним в цілому?
        $preRequestEncounter = Repository::encounter()->formatEncounterRequest($this->form->encounter, $this->form->conditions);
        $preRequestCondition = Repository::encounter()->formatConditionsRequest($this->form->conditions);

        $base64EncryptedData = $this->sendEncryptedData(
            array_merge(
                $preRequestEncounter,
                $preRequestCondition
            ),
            Auth::user()->tax_id
        );

        $prepareSubmitEncounter = [
            'visit' => (object)[
                'id' => Str::uuid()->toString(),
                'period' => (object)[
                    'start' => $preRequestEncounter['encounter']['period']['start'],
                    'end' => $preRequestEncounter['encounter']['period']['end']
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
     * @return void
     */
    private function createEpisode(): void
    {
        try {
            PatientApi::createEpisode($this->patientUuid, Repository::encounter()->formatEpisodeRequest($this->form->episode, $this->form->encounter['period']));
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
