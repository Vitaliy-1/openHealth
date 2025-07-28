<?php

declare(strict_types=1);

namespace App\Repositories\MedicalEvents;

use App\Classes\eHealth\Api\PatientApi;
use App\Models\MedicalEvents\Sql\DiagnosticReport;
use App\Models\MedicalEvents\Sql\DiagnosticReportPerformer;
use App\Models\MedicalEvents\Sql\DiagnosticReportResultsInterpreter;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class DiagnosticReportRepository extends BaseRepository
{
    protected string $employeeUuid;

    public function __construct(Model $model)
    {
        parent::__construct($model);

        $this->employeeUuid = Auth::user()?->getDiagnosticReportWriterEmployee()->uuid;
    }

    /**
     * Format data before request.
     *
     * @param  array  $diagnosticReport
     * @return array
     */
    public function formatEHealthRequest(array $diagnosticReport): array
    {
        // delete frontend properties
        unset($diagnosticReport['isReferralAvailable'], $diagnosticReport['referralType']);

        $diagnosticReport['id'] = Str::uuid()->toString();
        $diagnosticReport['status'] = 'final';

        if ($diagnosticReport['primarySource']) {
            unset($diagnosticReport['reportOrigin']);

            $diagnosticReport['performer']['reference']['identifier']['value'] = $this->employeeUuid;
        } else {
            unset($diagnosticReport['performer']);
        }

        if (empty($diagnosticReport['conclusionCode']['coding'][0]['code'])) {
            unset($diagnosticReport['conclusionCode']);
        }

        $diagnosticReport['recordedBy']['identifier']['value'] = $this->employeeUuid;

        $diagnosticReport['issued'] = convertToISO8601(
            $diagnosticReport['issuedDate'] . $diagnosticReport['issuedTime']
        );
        unset($diagnosticReport['issuedDate'], $diagnosticReport['issuedTime']);

        $diagnosticReport['managingOrganization'] = [
            'identifier' => [
                'type' => [
                    'coding' => [['system' => 'eHealth/resources', 'code' => 'legal_entity']],
                    'text' => ''
                ],
                'value' => legalEntity()->uuid
            ],
        ];

        if (empty($diagnosticReport['division']['identifier']['value'])) {
            unset($diagnosticReport['division']);
        }

        if (empty($diagnosticReport['resultsInterpreter']['text'])) {
            unset($diagnosticReport['resultsInterpreter']);
        }

        $normalizedData = schemaService()
            ->setDataSchema(['diagnostic_report' => $diagnosticReport], app(PatientApi::class))
            ->requestSchemaNormalize('schemaDiagnosticReportPackageRequest')
            ->camelCaseKeys()
            ->getNormalizedData();

        // schema service delete effectivePeriod, so manually add it
        $normalizedData['diagnosticReport']['effectivePeriod'] = [
            'start' => convertToISO8601(
                $diagnosticReport['effectivePeriodStartDate'] . $diagnosticReport['effectivePeriodStartTime']
            ),
            'end' => convertToISO8601(
                $diagnosticReport['effectivePeriodEndDate'] . $diagnosticReport['effectivePeriodEndTime']
            ),
        ];
        unset($diagnosticReport['effectivePeriodStartDate'], $diagnosticReport['effectivePeriodStartTime'], $diagnosticReport['effectivePeriodEndDate'], $diagnosticReport['effectivePeriodEndTime']);

        return $normalizedData;
    }

    /**
     * Store condition in DB.
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
                $diagnosticReportId = null;

                foreach ($data as $datum) {
                    $code = Repository::identifier()->store($datum['code']['identifier']['value']);
                    Repository::codeableConcept()->attach($code, $datum['code']);

                    if (isset($datum['conclusionCode'])) {
                        $conclusionCode = Repository::codeableConcept()->store($datum['conclusionCode']);
                    }

                    $recordedBy = Repository::identifier()->store($datum['recordedBy']['identifier']['value']);
                    Repository::codeableConcept()->attach($recordedBy, $datum['recordedBy']);

                    if ($createdEncounterId) {
                        $encounter = Repository::identifier()->store($datum['encounter']['identifier']['value']);
                        Repository::codeableConcept()->attach($encounter, $datum['encounter']);
                    }

                    $managingOrganization = Repository::identifier()->store(
                        $datum['managingOrganization']['identifier']['value']
                    );
                    Repository::codeableConcept()->attach($managingOrganization, $datum['managingOrganization']);

                    $division = Repository::identifier()->store($datum['division']['identifier']['value']);
                    Repository::codeableConcept()->attach($division, $datum['division']);

                    if (isset($datum['reportOrigin'])) {
                        $reportOrigin = Repository::codeableConcept()->store($datum['reportOrigin']);
                    }

                    /** @var DiagnosticReport $diagnosticReport */
                    $diagnosticReport = $this->model::create([
                        'uuid' => $datum['uuid'] ?? $datum['id'],
                        'encounter_internal_id' => $createdEncounterId,
                        'status' => $datum['status'],
                        'code_id' => $code->id,
                        'issued' => $datum['issued'],
                        'conclusion' => $datum['conclusion'] ?? null,
                        'conclusion_code_id' => $conclusionCode->id ?? null,
                        'recorded_by_id' => $recordedBy->id,
                        'encounter_id' => $encounter->id ?? null,
                        'primary_source' => $datum['primarySource'],
                        'managing_organization_id' => $managingOrganization->id,
                        'division_id' => $division->id,
                        'report_origin_id' => $reportOrigin->id ?? null
                    ]);

                    if (isset($datum['paperReferral'])) {
                        Repository::paperReferral()->store($datum['paperReferral'], $diagnosticReport);
                    }

                    $categoryIds = [];
                    foreach ($datum['category'] as $categoryData) {
                        $category = Repository::codeableConcept()->store($categoryData);

                        $categoryIds[] = $category->id;
                    }

                    $diagnosticReport->category()->attach($categoryIds);

                    $diagnosticReport->effectivePeriod()->create([
                        'start' => $datum['effectivePeriod']['start'],
                        'end' => $datum['effectivePeriod']['end']
                    ]);

                    if (isset($datum['performer'])) {
                        if (isset($datum['performer']['reference'])) {
                            $reference = Repository::identifier()->store(
                                $datum['performer']['reference']['identifier']['value']
                            );
                            Repository::codeableConcept()->attach($reference, $datum['performer']['reference']);
                        }

                        DiagnosticReportPerformer::create([
                            'diagnostic_report_id' => $diagnosticReport->id,
                            'reference_id' => $reference->id ?? null,
                            'text' => $datum['performer']['text'] ?? null
                        ]);
                    }

                    if (isset($datum['resultsInterpreter'])) {
                        if (isset($datum['resultsInterpreter']['reference'])) {
                            $reference = Repository::identifier()->store(
                                $datum['resultsInterpreter']['reference']['identifier']['value']
                            );
                            Repository::codeableConcept()->attach(
                                $reference,
                                $datum['resultsInterpreter']['reference']
                            );
                        }

                        DiagnosticReportResultsInterpreter::create([
                            'diagnostic_report_id' => $diagnosticReport->id,
                            'reference_id' => $reference->id ?? null,
                            'text' => $datum['resultsInterpreter']['text'] ?? null
                        ]);
                    }

                    $diagnosticReportId = $diagnosticReport->id;
                }

                // Return the ID when creating separately
                return $createdEncounterId === null ? $diagnosticReportId : null;
            });
        } catch (Exception $e) {
            Log::channel('db_errors')->error('Error saving diagnostic report', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            throw $e;
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
            'paperReferral',
            'code.type.coding',
            'category.coding',
            'conclusionCode.coding',
            'recordedBy.type.coding',
            'encounter.type.coding',
            'managingOrganization.type.coding',
            'division.type.coding',
            'performer.reference',
            'reportOrigin.coding',
            'resultsInterpreter.reference'
        ])
            ->where('encounter_internal_id', $encounterId)
            ->get()
            ->toArray();

        // Hide array of relationship data, accessories are used
        return array_map(static fn (array $item) => Arr::except($item, ['effectivePeriod']), $results);
    }

    /**
     * Get the diagnostic report for the clinical impression based on the provided UUID to display the selected supporting info.
     *
     * @param  string  $uuid
     * @return array|null
     */
    public function getForClinicalImpression(string $uuid): ?array
    {
        return DiagnosticReport::whereUuid($uuid)
            ->select(['id', 'issued', 'code_id'])
            ->with(['code.coding'])
            ->first()
            ?->toArray();
    }

    /**
     * Formatting to show on the frontend.
     *
     * @param  array  $diagnosticReports
     * @return array
     */
    public function formatForView(array $diagnosticReports): array
    {
        return array_map(static function (array $diagnosticReport) {
            // Set value to checkbox isReferralAvailable
            if (empty($diagnosticReport['basedOn']) && empty($diagnosticReport['paperReferral'])) {
                $diagnosticReport['isReferralAvailable'] = false;
            } else {
                $diagnosticReport['isReferralAvailable'] = true;
            }

            // Set referral type if referral is available
            if ($diagnosticReport['isReferralAvailable']) {
                $diagnosticReport['referralType'] = !empty($diagnosticReport['basedOn']) ? 'electronic' : 'paper';
            }

            // Set default value to avoid error
            if (empty($diagnosticReport['reportOrigin'])) {
                $diagnosticReport['reportOrigin'] = [
                    'coding' => [
                        ['code' => '']
                    ]
                ];
            }

            return $diagnosticReport;
        }, $diagnosticReports);
    }
}
