<?php

declare(strict_types=1);

namespace App\Repositories\MedicalEvents;

use App\Classes\eHealth\Api\PatientApi;
use App\Models\Employee\Employee;
use App\Models\MedicalEvents\Sql\EncounterDiagnose;
use Carbon\CarbonImmutable;
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
            try {
                $visit = Repository::identifier()->store($encounterData['visit']['identifier']['value']);
                Repository::codeableConcept()->attach($visit, $encounterData['visit']);

                $episode = Repository::identifier()->store($encounterData['episode']['identifier']['value']);
                Repository::codeableConcept()->attach($episode, $encounterData['episode']);

                $class = Repository::coding()->store($encounterData['class']);

                $type = Repository::codeableConcept()->store($encounterData['type']);

                if (isset($encounterData['priority'])) {
                    $priority = Repository::codeableConcept()->store($encounterData['priority']);
                }

                $performer = Repository::identifier()->store($encounterData['performer']['identifier']['value']);
                Repository::codeableConcept()->attach($performer, $encounterData['performer']);

                $division = Repository::identifier()->store($encounterData['division']['identifier']['value']);
                Repository::codeableConcept()->attach($division, $encounterData['division']);

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

                Repository::episode()->store($episodeData, $encounter->id);

                $reasonIds = [];

                foreach ($encounterData['reasons'] as $reasonData) {
                    $reason = Repository::codeableConcept()->store($reasonData);

                    $reasonIds[] = $reason->id;
                }

                $encounter->reasons()->attach($reasonIds);

                foreach ($encounterData['diagnoses'] as $diagnoseData) {
                    $condition = Repository::identifier()->store($diagnoseData['condition']['identifier']['value']);
                    Repository::codeableConcept()->attach($condition, $diagnoseData['condition']);

                    $role = Repository::codeableConcept()->store($diagnoseData['role']);

                    EncounterDiagnose::create([
                        'encounter_id' => $encounter->id,
                        'condition_id' => $condition->id,
                        'role_id' => $role->id,
                        'rank' => $diagnoseData['rank'] ?? null
                    ]);
                }

                $actionIds = [];

                foreach ($encounterData['actions'] as $actionData) {
                    $action = Repository::codeableConcept()->store($actionData);

                    $actionIds[] = $action->id;
                }

                $encounter->actions()->attach($actionIds);

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
     * Get encounter data by encounter ID form URL.
     *
     * @param  int  $encounterId
     * @return array
     */
    public function get(int $encounterId): array
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
            ->where('id', $encounterId)
            ->first()
            ?->toArray();
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
            ->camelCaseKeys()
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
            ->camelCaseKeys()
            ->getNormalizedData();
    }

    /**
     * Formatting conditions for showing in frontend.
     *
     * @param  array  $conditions
     * @param  array  $diagnoses
     * @return array
     */
    public function formatConditions(array $conditions, array $diagnoses): array
    {
        return collect($conditions)
            ->map(function (array $condition, int $index) use ($diagnoses) {
                // add diagnoses array to conditions
                if (isset($diagnoses[$index])) {
                    $condition['diagnoses'] = $diagnoses[$index];
                }

                $originalOnsetDate = $condition['onsetDate'];
                $originalAssertedDate = $condition['assertedDate'];

                // set date
                $condition['onsetDate'] = CarbonImmutable::parse($originalOnsetDate)->format('Y-m-d');
                $condition['onsetTime'] = CarbonImmutable::parse($originalOnsetDate)->format('H:i');
                $condition['assertedDate'] = CarbonImmutable::parse($originalAssertedDate)->format('Y-m-d');
                $condition['assertedTime'] = CarbonImmutable::parse($originalAssertedDate)->format('H:i');

                return $condition;
            })
            ->toArray();
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

            if (is_null($immunization['doseQuantity']['value'])) {
                unset($immunization['doseQuantity']);
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
            ->camelCaseKeys()
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
