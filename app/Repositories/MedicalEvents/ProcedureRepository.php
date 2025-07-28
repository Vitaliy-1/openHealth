<?php

declare(strict_types=1);

namespace App\Repositories\MedicalEvents;

use App\Classes\eHealth\Api\PatientApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Models\MedicalEvents\Sql\Condition;
use App\Models\MedicalEvents\Sql\Observation;
use App\Models\MedicalEvents\Sql\Procedure;
use App\Models\MedicalEvents\Sql\ProcedureComplicationDetail;
use App\Models\MedicalEvents\Sql\ProcedureReasonReference;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use App\Core\Arr as CoreArr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ProcedureRepository extends BaseRepository
{
    protected string $employeeUuid;

    public function __construct(Model $model)
    {
        parent::__construct($model);

        $this->employeeUuid = Auth::user()?->getProcedureWriterEmployee()->uuid;
    }

    /**
     * Format data before request.
     *
     * @param  array  $procedure
     * @return array
     */
    public function formatEHealthRequest(array $procedure): array
    {
        if ($procedure['referralType'] === 'electronic' || $procedure['referralType'] === '') {
            unset($procedure['paperReferral']);
        }

        if ($procedure['referralType'] === 'paper' || $procedure['referralType'] === '') {
            unset($procedure['basedOn']);
        }

        // delete frontend properties
        unset($procedure['isReferralAvailable'], $procedure['referralType']);

        $procedure['id'] = Str::uuid()->toString();
        $procedure['status'] = 'completed';

        $procedure['recordedBy']['identifier']['value'] = $this->employeeUuid;

        $procedure['managingOrganization'] = [
            'identifier' => [
                'type' => [
                    'coding' => [['system' => 'eHealth/resources', 'code' => 'legal_entity']],
                    'text' => ''
                ],
                'value' => legalEntity()->uuid
            ],
        ];

        if (!empty($procedure['reasonReferences'])) {
            foreach ($procedure['reasonReferences'] as &$reasonReference) {
                $code = str_contains($reasonReference['code']['coding'][0]['system'], 'condition_codes')
                    ? 'condition'
                    : 'observation';

                $identifier = [
                    'type' => [
                        'coding' => [['system' => 'eHealth/resources', 'code' => $code]]
                    ],
                    'value' => $reasonReference['id']
                ];

                // Keep only the identifier key
                $reasonReference = ['identifier' => $identifier];
            }

            unset($reasonReference);
        }

        if ($procedure['outcome']['coding'][0]['code'] === '') {
            unset($procedure['outcome']);
        }

        if ($procedure['usedCodes'][0]['coding'][0]['code'] === '') {
            unset($procedure['outcome']);
        }

        $normalizedData = schemaService()
            ->setDataSchema($procedure, app(PatientApi::class))
            ->requestSchemaNormalize('schemaProcedurePackageRequest')
            ->camelCaseKeys()
            ->getNormalizedData();

        // schema service delete effectivePeriod, performer and reportOrigin, because of 'One Of', so manually add it
        if ($normalizedData['primarySource']) {
            $normalizedData['performer'] = $procedure['performer'];
            $normalizedData['performer']['identifier']['value'] = $this->employeeUuid;
        } else {
            $normalizedData['reportOrigin'] = $procedure['reportOrigin'];
        }

        $normalizedData['performedPeriod'] = [
            'start' => convertToISO8601(
                $procedure['performedPeriodStartDate'] . $procedure['performedPeriodStartTime']
            ),
            'end' => convertToISO8601(
                $procedure['performedPeriodEndDate'] . $procedure['performedPeriodEndTime']
            ),
        ];

        return $normalizedData;
    }

    /**
     * Store procedure in DB.
     *
     * @param  array  $data
     * @param  int|null  $createdEncounterId
     * @return int|null
     * @throws Throwable
     */
    public function store(array $data, ?int $createdEncounterId = null): ?int
    {
        try {
            return DB::transaction(function () use ($data, $createdEncounterId) {
                $procedureId = null;

                foreach ($data as $datum) {
                    if (isset($datum['basedOn'])) {
                        $basedOn = Repository::identifier()->store($datum['basedOn']['identifier']['value']);
                        Repository::codeableConcept()->attach($basedOn, $datum['basedOn']);
                    }

                    $code = Repository::identifier()->store($datum['code']['identifier']['value']);
                    Repository::codeableConcept()->attach($code, $datum['code']);

                    if ($createdEncounterId) {
                        $encounter = Repository::identifier()->store($datum['encounter']['identifier']['value']);
                        Repository::codeableConcept()->attach($encounter, $datum['encounter']);
                    }

                    $recordedBy = Repository::identifier()->store($datum['recordedBy']['identifier']['value']);
                    Repository::codeableConcept()->attach($recordedBy, $datum['recordedBy']);

                    if (isset($datum['performer'])) {
                        $performer = Repository::identifier()->store($datum['performer']['identifier']['value']);
                        Repository::codeableConcept()->attach($performer, $datum['performer']);
                    }

                    if (isset($datum['reportOrigin'])) {
                        $reportOrigin = Repository::codeableConcept()->store($datum['reportOrigin']);
                    }

                    if (isset($datum['division'])) {
                        $division = Repository::identifier()->store($datum['division']['identifier']['value']);
                        Repository::codeableConcept()->attach($division, $datum['division']);
                    }

                    $managingOrganization = Repository::identifier()->store(
                        $datum['managingOrganization']['identifier']['value']
                    );
                    Repository::codeableConcept()->attach($managingOrganization, $datum['managingOrganization']);

                    if (isset($datum['outcome'])) {
                        $outcome = Repository::codeableConcept()->store($datum['outcome']);
                    }

                    $category = Repository::codeableConcept()->store($datum['category']);

                    /** @var Procedure $procedure */
                    $procedure = $this->model::create([
                        'uuid' => $datum['uuid'] ?? $datum['id'],
                        'encounter_internal_id' => $createdEncounterId,
                        'status' => $datum['status'],
                        'based_on_id' => $basedOn->id ?? null,
                        'code_id' => $code->id,
                        'encounter_id' => $encounter->id ?? null,
                        'recorded_by_id' => $recordedBy->id,
                        'primary_source' => $datum['primarySource'],
                        'performer_id' => $performer->id ?? null,
                        'report_origin_id' => $reportOrigin->id ?? null,
                        'division_id' => $division->id ?? null,
                        'managing_organization_id' => $managingOrganization->id,
                        'outcome_id' => $outcome->id ?? null,
                        'note' => $datum['note'] ?? null,
                        'category_id' => $category->id
                    ]);

                    $procedure->performedPeriod()->create([
                        'start' => $datum['performedPeriod']['start'],
                        'end' => $datum['performedPeriod']['end']
                    ]);

                    if (isset($datum['reasonReferences'])) {
                        foreach ($datum['reasonReferences'] as $reasonReference) {
                            $identifier = Repository::identifier()->store(
                                $reasonReference['identifier']['value']
                            );
                            Repository::codeableConcept()->attach($identifier, $reasonReference);

                            ProcedureReasonReference::create([
                                'procedure_id' => $procedure->id,
                                'identifier_id' => $identifier->id ?? null
                            ]);
                        }
                    }

                    if (isset($datum['complicationDetails'])) {
                        foreach ($datum['complicationDetails'] as $complicationDetail) {
                            $identifier = Repository::identifier()->store(
                                $complicationDetail['identifier']['value']
                            );
                            Repository::codeableConcept()->attach($identifier, $complicationDetail);

                            ProcedureComplicationDetail::create([
                                'procedure_id' => $procedure->id,
                                'identifier_id' => $identifier->id ?? null
                            ]);
                        }
                    }

                    if (isset($datum['paperReferral'])) {
                        Repository::paperReferral()->store($datum['paperReferral'], $procedure);
                    }

                    $usedCodeIds = [];
                    foreach ($datum['usedCodes'] as $usedCodeData) {
                        $usedCode = Repository::codeableConcept()->store($usedCodeData);

                        $usedCodeIds[] = $usedCode->id;
                    }

                    $procedure->usedCodes()->attach($usedCodeIds);

                    $procedureId = $procedure->id;
                }

                // Return the ID when creating separately
                return $createdEncounterId === null ? $procedureId : null;
            });
        } catch (Exception $e) {
            Log::channel('db_errors')->error('Error saving procedure', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            throw $e;
        }
    }

    /**
     * Store reason references.
     *
     * @param  string  $patientUuid
     * @param  array  $reasonReference
     * @param  int  $encounterId
     * @return void
     */
    public function storeReasonReferences(string $patientUuid, array $reasonReference, int $encounterId): void
    {
        if ($reasonReference['identifier']['type']['coding'][0]['code'] === 'condition') {
            $this->storeCondition($reasonReference['identifier']['value'], $patientUuid, $encounterId);
        } else {
            $observation = Observation::whereUuid($reasonReference['identifier']['value'])->first();

            // Get from API and save in the DB.
            if (!$observation) {
                try {
                    $observationData = PatientApi::getObservationById(
                        $patientUuid,
                        $reasonReference['identifier']['value']
                    );
                    Repository::observation()->store([CoreArr::toCamelCase($observationData)], $encounterId);
                } catch (ApiException|Throwable $e) {
                    Log::channel('e_health_errors')->error('Failed to fetch or store observation', [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);
                }
            }
        }
    }

    /**
     * Store complication details.
     *
     * @param  string  $patientUuid
     * @param  array  $procedure
     * @param  int  $encounterId
     * @return void
     */
    public function storeComplicationDetails(string $patientUuid, array $procedure, int $encounterId): void
    {
        foreach ($procedure['complicationDetails'] as $complicationDetail) {
            $this->storeCondition($complicationDetail['identifier']['value'], $patientUuid, $encounterId);
        }
    }

    /**
     * Get data that is related to the encounter.
     *
     * @param  int  $encounterId
     * @return array|null
     */
    public function get(int $encounterId): ?array
    {
        $results = $this->model::with([
            'basedOn.type.coding',
            'code.type.coding',
            'encounter.type.coding',
            'recordedBy.type.coding',
            'performer',
            'reportOrigin.coding',
            'division.type.coding',
            'managingOrganization.type.coding',
            'reasonReferences',
            'outcome.coding',
            'complicationDetails',
            'category.coding',
            'paperReferral',
            'usedCodes.coding',
            'performedPeriod'
        ])
            ->where('encounter_internal_id', $encounterId)
            ->get()
            ->toArray();

        $results = $this->resolveReasonReferences($results);
        $results = $this->resolveComplicationDetails($results);

        // Hide array of relationship data, accessories are used
        return array_map(static fn (array $item) => Arr::except($item, ['performedPeriod']), $results);
    }

    /**
     * Get the episode for the clinical impression based on the provided UUID to display the selected supporting info.
     *
     * @param  string  $uuid
     * @return array|null
     */
    public function getForClinicalImpression(string $uuid): ?array
    {
        return Procedure::whereUuid($uuid)
            ->select(['id', 'code_id'])
            ->with('code.coding')
            ->first()
            ?->toArray();
    }

    /**
     * Formatting to show on the frontend.
     *
     * @param  array  $procedures
     * @return array
     */
    public function formatForView(array $procedures): array
    {
        return array_map(static function (array $procedure) {
            // Set value to checkbox isReferralAvailable
            if (empty($procedure['basedOn']) && empty($procedure['paperReferral'])) {
                $procedure['isReferralAvailable'] = false;
            } else {
                $procedure['isReferralAvailable'] = true;
            }

            // Set referral type if referral is available
            if ($procedure['isReferralAvailable']) {
                $procedure['referralType'] = !empty($procedure['basedOn']) ? 'electronic' : 'paper';
            }

            // Set default value to avoid error
            if (empty($procedure['reportOrigin'])) {
                $procedure['reportOrigin'] = [
                    'coding' => [
                        ['code' => '']
                    ]
                ];
            }

            return $procedure;
        }, $procedures);
    }

    /**
     * Store condition if it doesn't exist by data from the API.
     *
     * @param  string  $value
     * @param  string  $patientUuid
     * @param  int  $encounterId
     * @return void
     */
    protected function storeCondition(string $value, string $patientUuid, int $encounterId): void
    {
        $condition = Condition::whereUuid($value)->first();

        if (!$condition) {
            try {
                $conditionData = PatientApi::getConditionById($patientUuid, $value);
                Repository::condition()->store([CoreArr::toCamelCase($conditionData)], $encounterId);
            } catch (ApiException|Throwable $e) {
                Log::channel('e_health_errors')->error('Failed to fetch or store condition', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
        }
    }

    /**
     * Get related condition and observation from the DB.
     *
     * @param  array  $results
     * @return array
     */
    protected function resolveReasonReferences(array $results): array
    {
        return collect($results)->map(function ($result) {
            if (!empty($result['reasonReferences'])) {
                $result['reasonReferences'] = collect($result['reasonReferences'])
                    ->map(function ($reasonReference) {
                        if ($reasonReference['identifier']['type'][0]['coding'][0]['code'] === 'condition') {
                            $reasonReference = $this->getCondition($reasonReference);
                        } else {
                            $observation = Repository::observation()
                                ->getForProcedure($reasonReference['identifier']['value']);

                            if ($observation) {
                                $reasonReference['inserted_at'] = $observation['issued'];
                                $reasonReference['code']['coding'][0]['code'] = $observation['code']['coding'][0]['code'];
                            }
                        }

                        return $reasonReference;
                    })->toArray();
            }

            return $result;
        })->toArray();
    }

    /**
     * Get related condition and observation from the DB.
     *
     * @param  array  $results
     * @return array
     */
    protected function resolveComplicationDetails(array $results): array
    {
        return collect($results)->map(function ($result) {
            if (!empty($result['complicationDetails'])) {
                $result['complicationDetails'] = collect($result['complicationDetails'])
                    ->map(function ($complicationDetail) {
                        return $this->getCondition($complicationDetail);
                    })->toArray();
            }

            return $result;
        })->toArray();
    }

    /**
     * Get condition from DB and set inserted_at and code to response.
     *
     * @param  array  $data
     * @return array
     */
    protected function getCondition(array $data): array
    {
        $condition = Repository::condition()->getForProcedure($data['identifier']['value']);

        if ($condition) {
            $data['inserted_at'] = $condition['onsetDate'];
            $data['code']['coding'][0]['code'] = $condition['code']['coding'][0]['code'];
        }

        return $data;
    }
}
