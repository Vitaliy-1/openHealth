<?php

declare(strict_types=1);

namespace App\Services;

use App\Classes\eHealth\Api\PatientApi;
use App\Models\Employee\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EncounterService
{
    protected string $encounterUuid;
    protected array $diagnoseUuids;
    protected string $visitUuid;
    protected string $episodeUuid;

    public function __construct()
    {
        $this->encounterUuid = Str::uuid()->toString();
        $this->visitUuid = Str::uuid()->toString();
        $this->episodeUuid = Str::uuid()->toString();
    }

    /**
     * Format encounter data before request.
     *
     * @param  array  $encounter
     * @param  array  $conditions
     * @return array
     */
    public function formatEncounterRequest(array $encounter, array $conditions): array
    {
        $encounter['id'] = $this->encounterUuid;
        $encounter['visit']['identifier']['value'] = $this->visitUuid;
        $encounter['episode']['identifier']['value'] = $this->episodeUuid;

        // add system if priority is provided or when it's required
        if ($encounter['class']['code'] === 'INPATIENT' || $encounter['class']['code']) {
            $encounter['priority']['coding'][0]['system'] = 'eHealth/encounter_priority';
        }

        $encounter['diagnoses'] = array_map(function (array $diagnose) {
            // Create a unique UUID for each diagnosis, and use them in condition
            $diagnoseUuid = Str::uuid()->toString();
            $diagnose['diagnoses']['condition']['identifier']['value'] = $diagnoseUuid;
            $this->diagnoseUuids[] = $diagnoseUuid;

            // delete rank if not provided
            if ($diagnose['diagnoses']['rank'] === '') {
                unset($diagnose['diagnoses']['rank']);
            }

            return $diagnose['diagnoses'];
        }, $conditions);

        if ($encounter['division']['identifier']['value']) {
            $encounter['division']['identifier']['type']['coding'][0] = [
                'system' => 'eHealth/resources',
                'code' => 'division'
            ];
        }

        $encounterForm = $this->formatEncounterPeriod($encounter);

        return schemaService()
            ->setDataSchema(['encounter' => $encounterForm], app(PatientApi::class))
            ->requestSchemaNormalize()
            ->getNormalizedData()['encounter'];
    }

    /**
     * Format episode data before request.
     *
     * @param  array  $episode
     * @param  array  $encounterPeriod
     * @return array
     */
    public function formatEpisodeRequest(array $episode, array $encounterPeriod): array
    {
        $episode['id'] = $this->episodeUuid;
        $episode['managingOrganization']['identifier']['value'] = Auth::user()->legalEntity->uuid;
        $episode['period']['start'] = convertToISO8601($encounterPeriod['date'] . $encounterPeriod['start']);

        return schemaService()
            ->setDataSchema($episode, app(PatientApi::class))
            ->requestSchemaNormalize('schemaEpisodeRequest')
            ->getNormalizedData();
    }

    /**
     * Format Conditions data before request.
     *
     * @param  array  $conditions
     * @return array
     */
    public function formatConditionsRequest(array $conditions): array
    {
        $conditionForm = array_map(
            function (array $condition, int $index) {
                // set ID same as diagnose
                $condition['id'] = $this->diagnoseUuids[$index];

                $condition['context']['identifier']['type']['coding'][0] = [
                    'system' => 'eHealth/resources',
                    'code' => 'encounter'
                ];
                $condition['context']['identifier']['value'] = $this->encounterUuid;

                // unset if code not provided
                if ($condition['severity']['coding'][0]['code'] === '') {
                    unset($condition['severity']);
                }

                if ($condition['primarySource']) {
                    // TODO: потім взяти employee авторизованого
                    $employee = Employee::find(1);
                    $condition['asserter']['identifier']['value'] = $employee?->uuid;

                    unset($condition['reportOrigin']);
                } else {
                    unset($condition['asserter']);
                }

                // convert dates
                if (isset($condition['onsetTime'])) {
                    $condition['onsetDate'] = convertToISO8601($condition['onsetDate'] . $condition['onsetTime']);
                    $condition['assertedDate'] = convertToISO8601($condition['assertedDate'] . $condition['assertedTime']);
                    unset($condition['onsetTime'], $condition['assertedTime'], $condition['diagnoses']);
                }

                return $condition;
            },
            $conditions,
            array_keys($conditions)
        );

        return schemaService()
            ->setDataSchema(['conditions' => $conditionForm], app(PatientApi::class))
            ->requestSchemaNormalize()
            ->getNormalizedData()['conditions'];
    }

    /**
     * Format immunizations data before request.
     *
     * @param  array  $immunizations
     * @return array
     */
    public function formatImmunizationsRequest(array $immunizations): array
    {
        $immunizationForm = array_map(function ($immunization) {
            $immunization['id'] = Str::uuid()->toString();

            $immunization['status'] = 'completed';

            $immunization['context']['identifier']['type']['coding'][0] = [
                'system' => 'eHealth/resources',
                'code' => 'encounter'
            ];
            $immunization['context']['identifier']['value'] = $this->encounterUuid;

            if ($immunization['primarySource']) {
                unset($immunization['reportOrigin']);

                // TODO: потім взяти employee авторизованого
                $employee = Employee::findOrFail(1);
                $immunization['performer']['identifier']['value'] = $employee->uuid;
            } else {
                unset($immunization['performer']);
            }

            if ($immunization['notGiven']) {
                unset($immunization['explanation']['reasons']);
            } else {
                unset($immunization['explanation']['reasonsNotGiven']);
            }

            $immunization['date'] = convertToISO8601($immunization['date'] . $immunization['time']);
            unset($immunization['time']);

            if ($immunization['expirationDate']) {
                $immunization['expirationDate'] = convertToISO8601($immunization['expirationDate'] . now()->format('H:i'));
            }

            return $immunization;
        }, $immunizations);

        return schemaService()
            ->setDataSchema(['immunizations' => $immunizationForm], app(PatientApi::class))
            ->requestSchemaNormalize()
            ->getNormalizedData();
    }

    /**
     * Format encounter period to ISO8601 format.
     *
     * @param  array  $encounterForm
     * @return array
     */
    public function formatEncounterPeriod(array $encounterForm): array
    {
        // TODO: зробити унікальнимЮ використати в епізоді
        $encounterForm['period']['start'] = convertToISO8601($encounterForm['period']['date'] . $encounterForm['period']['start']);
        $encounterForm['period']['end'] = convertToISO8601($encounterForm['period']['date'] . $encounterForm['period']['end']);
        unset($encounterForm['period']['date']);

        return $encounterForm;
    }
}
