<?php

declare(strict_types=1);

namespace App\Repositories\MedicalEvents;

use App\Classes\eHealth\Api\PatientApi;
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
    protected string $employeeUuid;

    public function __construct(Model $model)
    {
        parent::__construct($model);

        $this->encounterUuid = Str::uuid()->toString();
        $this->visitUuid = Str::uuid()->toString();
        $this->episodeUuid = Str::uuid()->toString();
        $this->employeeUuid = Auth::user()?->getEncounterWriterEmployee()->uuid;
    }

    /**
     * Create encounter in DB for person with related data.
     *
     * @param  array  $encounterData
     * @param  int  $personId
     * @return false|int
     * @throws Throwable
     */
    public function store(array $encounterData, int $personId): false|int
    {
        return DB::transaction(function () use ($encounterData, $personId) {
            try {
                $visit = Repository::identifier()->store($encounterData['visit']['identifier']['value']);
                Repository::codeableConcept()->attach($visit, $encounterData['visit']);

                $episode = Repository::identifier()->store($encounterData['episode']['identifier']['value']);
                Repository::codeableConcept()->attach($episode, $encounterData['episode']);

                $class = Repository::coding()->store($encounterData['class']);

                $type = Repository::codeableConcept()->store($encounterData['type']);

                if (isset($encounterData['priority']['coding'][0]['code'])) {
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
     * @return array|null
     */
    public function get(int $encounterId): ?array
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
     * @param  bool  $isEpisodeNew
     * @return array
     */
    public function formatEncounterRequest(array $encounter, array $conditions, bool $isEpisodeNew): array
    {
        $encounter['id'] = $this->encounterUuid;
        $encounter['visit']['identifier']['value'] = $this->visitUuid;

        if ($isEpisodeNew) {
            $encounter['episode']['identifier']['value'] = $this->episodeUuid;
        }

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
            ->extractFirst()
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

        return schemaService()
            ->setDataSchema($episode, app(PatientApi::class))
            ->requestSchemaNormalize('schemaEpisodeRequest')
            ->camelCaseKeys()
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
                unset($condition['query']);
                // set ID same as diagnose
                $condition['id'] = $this->diagnoseUuids[$index];

                $condition['context']['identifier']['type']['coding'][0] = [
                    'system' => 'eHealth/resources',
                    'code' => 'encounter'
                ];
                $condition['context']['identifier']['value'] = $this->encounterUuid;

                // Remove coding with empty code
                $condition['code']['coding'] = array_values(array_filter(
                    $condition['code']['coding'],
                    static fn (array $coding) => !empty($coding['code']) && trim($coding['code']) !== ''
                ));

                // unset if code not provided
                if ($condition['severity']['coding'][0]['code'] === '') {
                    unset($condition['severity']);
                }

                if ($condition['primarySource']) {
                    $condition['asserter']['identifier']['value'] = $this->employeeUuid;

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

                if (empty($condition['evidences'][0]['codes'])) {
                    unset($condition['evidences']);
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
            ->extractFirst()
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

                $immunization['performer']['identifier']['value'] = $this->employeeUuid;
            } else {
                unset($immunization['performer']);
            }

            if ($immunization['notGiven']) {
                unset($immunization['explanation']['reasons']);
            } else {
                unset($immunization['explanation']['reasonsNotGiven']);
            }

            if ($immunization['route']['coding'][0]['code'] === '') {
                unset($immunization['route']);
            }

            if ($immunization['site']['coding'][0]['code'] === '') {
                unset($immunization['site']);
            }

            if (is_null($immunization['doseQuantity']['value'])) {
                unset($immunization['doseQuantity']);
            }

            $immunization['date'] = convertToISO8601($immunization['date'] . $immunization['time']);
            unset($immunization['time']);

            if ($immunization['expirationDate']) {
                $immunization['expirationDate'] = convertToISO8601($immunization['expirationDate'] . now()->format('H:i'));
            }

            // remove key where value is null
            return array_filter($immunization, static fn (mixed $value) => !is_null($value));
        }, $immunizations);

        return schemaService()
            ->setDataSchema(['immunizations' => $immunizationForm], app(PatientApi::class))
            ->requestSchemaNormalize()
            ->camelCaseKeys()
            ->getNormalizedData();
    }

    /**
     * Format observations data before request.
     *
     * @param  array  $observations
     * @return array
     */
    public function formatObservationsRequest(array $observations): array
    {
        $observationForm = array_map(function (array $observation) {
            unset($observation['codingSystem']);

            $observation['id'] = Str::uuid()->toString();
            $observation['status'] = 'valid';

            if (isset($observation['dictionaryName'])) {
                unset($observation['dictionaryName']);
            }

            $observation['effectiveDateTime'] = convertToISO8601($observation['effectiveDate'] . $observation['effectiveTime']);
            unset($observation['effectiveDate'], $observation['effectiveTime']);

            if (empty($observation['effectiveDateTime'])) {
                unset($observation['effectiveDateTime']);
            }

            $observation['issued'] = convertToISO8601($observation['issuedDate'] . $observation['issuedTime']);
            unset($observation['issuedDate'], $observation['issuedTime']);

            $observation['context']['identifier']['type']['coding'][0] = [
                'system' => 'eHealth/resources',
                'code' => 'encounter'
            ];
            $observation['context']['identifier']['value'] = $this->encounterUuid;

            if ($observation['primarySource']) {
                unset($observation['reportOrigin']);

                $observation['performer']['identifier']['value'] = $this->employeeUuid;
            } else {
                unset($observation['performer']);
            }

            if ($observation['valueQuantity']['value'] === '') {
                unset($observation['valueQuantity']);
            }

            // format to codeable concept type
            if (isset($observation['valueCodeableConcept'])) {
                $observation['valueCodeableConcept'] = [
                    'coding' => [
                        [
                            'system' => 'eHealth/' . $observation['code']['coding'][0]['code'],
                            'code' => $observation['valueCodeableConcept'],
                        ]
                    ],
                    'text' => ''
                ];
            }

            // combine date&time
            if (isset($observation['valueDate'], $observation['valueTime'])) {
                $observation['valueDateTime'] = convertToISO8601($observation['valueDate'] . $observation['valueTime']);
                unset($observation['valueDate'], $observation['valueTime']);
            }

            if (empty($observation['bodySite']['coding'][0]['code'])) {
                unset($observation['bodySite']);
            }

            if (empty($observation['interpretation']['coding'][0]['code'])) {
                unset($observation['interpretation']);
            }

            if (empty($observation['method']['coding'][0]['code'])) {
                unset($observation['method']);
            }

            if ($observation['components'][0]['valueCodeableConcept']['coding'][0]['code'] === '') {
                unset($observation['components']);
            }

            if (isset($observation['components'][0]['interpretation']['coding'][0]['code']) && $observation['components'][0]['interpretation']['coding'][0]['code'] === '') {
                unset($observation['components']);
            }

            return $observation;
        }, $observations);

        return schemaService()
            ->setDataSchema(['observations' => $observationForm], app(PatientApi::class))
            ->requestSchemaNormalize()
            ->camelCaseKeys()
            ->getNormalizedData();
    }

    /**
     * Format diagnostic reports data before request.
     *
     * @param  array  $diagnosticReports
     * @return array
     */
    public function formatDiagnosticReportsRequest(array $diagnosticReports): array
    {
        $diagnosticReportForm = array_map(function (array $diagnosticReport) {
            // delete frontend properties
            unset($diagnosticReport['isReferralAvailable'], $diagnosticReport['referralType'], $diagnosticReport['query']);

            $diagnosticReport['id'] = Str::uuid()->toString();
            $diagnosticReport['status'] = 'final';

            if ($diagnosticReport['primarySource']) {
                unset($diagnosticReport['reportOrigin']);

                $diagnosticReport['performer']['identifier']['value'] = $this->employeeUuid;
            } else {
                unset($diagnosticReport['performer']);
            }

            if (empty($diagnosticReport['conclusionCode']['coding'][0]['code'])) {
                unset($diagnosticReport['conclusionCode']);
            }

            $diagnosticReport['recordedBy']['identifier']['value'] = $this->employeeUuid;

            $diagnosticReport['issued'] = convertToISO8601($diagnosticReport['issuedDate'] . $diagnosticReport['issuedTime']);
            unset($diagnosticReport['issuedDate'], $diagnosticReport['issuedTime']);

            $diagnosticReport['effectivePeriod']['start'] = convertToISO8601($diagnosticReport['effectivePeriodStartDate'] . $diagnosticReport['effectivePeriodStartTime']);
            unset($diagnosticReport['effectivePeriodStartDate'], $diagnosticReport['effectivePeriodStartTime']);

            $diagnosticReport['effectivePeriod']['end'] = convertToISO8601($diagnosticReport['effectivePeriodEndDate'] . $diagnosticReport['effectivePeriodEndTime']);
            unset($diagnosticReport['effectivePeriodEndDate'], $diagnosticReport['effectivePeriodEndTime']);

            if (empty($diagnosticReport['resultsInterpreter']['text'])) {
                unset($diagnosticReport['resultsInterpreter']);
            }

            return $diagnosticReport;
        }, $diagnosticReports);

        return schemaService()
            ->setDataSchema(['diagnostic_reports' => $diagnosticReportForm], app(PatientApi::class))
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
        $encounterForm['period'] = [
            'start' => convertToISO8601($encounterForm['period']['date'] . $encounterForm['period']['start']),
            'end' => convertToISO8601($encounterForm['period']['date'] . $encounterForm['period']['end'])
        ];
        unset($encounterForm['period']['date']);

        return $encounterForm;
    }
}
