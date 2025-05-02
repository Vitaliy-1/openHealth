<?php

declare(strict_types=1);

namespace App\Repositories\MedicalEvents;

use App\Classes\eHealth\Api\PatientApi;
use App\Models\Employee\Employee;
use App\Models\MedicalEvents\Sql\EncounterDiagnose;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class EncounterRepository extends BaseRepository
{
    protected string $encounterUuid;
    protected array $diagnoseUuids;
    protected string $visitUuid;
    protected string $episodeUuid;

    public function __construct(Model $model)
    {
        parent::__construct($model);

        $this->encounterUuid = Str::uuid()->toString();
        $this->visitUuid = Str::uuid()->toString();
        $this->episodeUuid = Str::uuid()->toString();
    }

    /**
     * Create encounter in DB for person with related data.
     *
     * @param  array  $encounterData
     * @param  array  $episodeData
     * @param  int  $personId
     * @return false|int
     * @throws Throwable
     */
    public function store(array $encounterData, array $episodeData, int $personId): false|int
    {
        return DB::transaction(function () use ($encounterData, $episodeData, $personId) {
            $repository = new Repository();

            try {
                $visit = $repository::identifier()->store($encounterData['visit']['identifier']['value']);

                $episode = $repository::identifier()->store($encounterData['episode']['identifier']['value']);

                $class = $repository::coding()->store($encounterData['class']);

                $type = $repository::codeableConcept()->store($encounterData['type']);

                if (isset($encounterData['priority'])) {
                    $priority = $repository::codeableConcept()->store($encounterData['priority']);
                }

                $performer = $repository::identifier()->store($encounterData['performer']['identifier']['value']);

                $division = $repository::identifier()->store($encounterData['division']['identifier']['value']);

                $encounter = $this->model::create([
                    'person_id' => $personId,
                    'uuid' => $encounterData['uuid'] ?? $encounterData['id'],
                    'status' => $encounterData['status'],
                    'visit_id' => $visit->id,
                    'episode_id' => $episode->id,
                    'class_id' => $class->id,
                    'type_id' => $type->id,
                    'priority_id' => $priority->id ?? null,
                    'performer_id' => $performer->id,
                    'division_id' => $division->id
                ]);

                $encounter->period()->create([
                    'start' => $encounterData['period']['start'],
                    'end' => $encounterData['period']['end']
                ]);

                $repository::codeableConcept()->attach($visit, $encounterData['visit']);

                $repository::codeableConcept()->attach($episode, $encounterData['episode']);

                $repository::episode()->store($episodeData, $encounter->id);

                $repository::codeableConcept()->attach($performer, $encounterData['performer']);

                $reasonIds = [];

                foreach ($encounterData['reasons'] as $reasonData) {
                    $reason = $repository::codeableConcept()->store($reasonData);

                    $reasonIds[] = $reason->id;
                }

                $encounter->reasons()->attach($reasonIds);

                foreach ($encounterData['diagnoses'] as $diagnoseData) {
                    $condition = $repository::identifier()->store($diagnoseData['condition']['identifier']['value']);
                    $repository::codeableConcept()->attach($condition, $diagnoseData['condition']);

                    $role = $repository::codeableConcept()->store($diagnoseData['role']);

                    EncounterDiagnose::create([
                        'encounter_id' => $encounter->id,
                        'condition_id' => $condition->id,
                        'role_id' => $role->id,
                        'rank' => $diagnoseData['rank'] ?? null
                    ]);
                }

                $actionIds = [];

                foreach ($encounterData['actions'] as $actionData) {
                    $action = $repository::codeableConcept()->store($actionData);

                    $actionIds[] = $action->id;
                }

                $encounter->actions()->attach($actionIds);

                $repository::codeableConcept()->attach($division, $encounterData['division']);

                return $encounter->id;
            } catch (Exception $e) {
                Log::channel('db_errors')->error('Error saving encounter', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);

                throw $e;
            }
        });
    }

    /**
     * Get encounter data that is related to the patient.
     *
     * @param  int  $patientId
     * @return array
     */
    public function get(int $patientId): array
    {
        return $this->model::with([
            'period',
            'visit',
            'episode',
            'class',
            'type.coding',
            'priority.coding',
            'performer',
            'reasons.coding',
            'diagnoses',
            'actions.coding',
            'division'
        ])
            ->where('person_id', $patientId)
            ->first()?->toArray();
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

        $encounterForm = $this->formatPeriod($encounter);

        return schemaService()
            ->setDataSchema(['encounter' => $encounterForm], app(PatientApi::class))
            ->requestSchemaNormalize()
            ->getNormalizedData();
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

        $normalizedData = schemaService()
            ->setDataSchema($episode, app(PatientApi::class))
            ->requestSchemaNormalize('schemaEpisodeRequest')
            ->getNormalizedData();

        return ['episode' => $normalizedData];
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
            ->getNormalizedData();
    }

    /**
     * Format immunizations data before request.
     *
     * @param  array  $immunizations
     * @return array
     */
    public function formatImmunizationsRequest(array $immunizations): array
    {
        $immunizationForm = array_map(function (array $immunization) {
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
    public function formatPeriod(array $encounterForm): array
    {
        $encounterForm['period']['start'] = convertToISO8601($encounterForm['period']['date'] . $encounterForm['period']['start']);
        $encounterForm['period']['end'] = convertToISO8601($encounterForm['period']['date'] . $encounterForm['period']['end']);
        unset($encounterForm['period']['date']);

        return $encounterForm;
    }
}
