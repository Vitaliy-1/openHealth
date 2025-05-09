<?php

declare(strict_types=1);

namespace App\Repositories\MedicalEvents;

use App\Models\MedicalEvents\Sql\ImmunizationDoseQuantity;
use App\Models\MedicalEvents\Sql\ImmunizationExplanation;
use App\Models\MedicalEvents\Sql\ImmunizationVaccinationProtocol;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ImmunizationRepository extends BaseRepository
{
    /**
     * Store condition in DB.
     *
     * @param  array  $data
     * @param  int  $encounterId
     * @return void
     * @throws Throwable
     */
    public function store(array $data, int $encounterId): void
    {
        DB::transaction(function () use ($data, $encounterId) {
            try {
                foreach ($data as $datum) {
                    $vaccineCode = Repository::codeableConcept()->store($datum['vaccineCode']);

                    $context = Repository::identifier()->store($datum['context']['identifier']['value']);
                    Repository::codeableConcept()->attach($context, $datum['context']);

                    if (isset($datum['performer'])) {
                        $performer = Repository::identifier()->store($datum['performer']['identifier']['value']);
                        Repository::codeableConcept()->attach($performer, $datum['performer']);
                    }

                    if (isset($datum['reportOrigin'])) {
                        $reportOrigin = Repository::codeableConcept()->store($datum['reportOrigin']);
                    }

                    if (isset($datum['site'])) {
                        $site = Repository::codeableConcept()->store($datum['site']);
                    }

                    if (isset($datum['route'])) {
                        $route = Repository::codeableConcept()->store($datum['route']);
                    }

                    $immunization = $this->model::create([
                        'uuid' => $datum['uuid'] ?? $datum['id'],
                        'encounter_id' => $encounterId,
                        'status' => $datum['status'],
                        'not_given' => $datum['notGiven'],
                        'vaccine_code_id' => $vaccineCode->id,
                        'context_id' => $context->id,
                        'date' => $datum['date'] ?? null,
                        'primary_source' => $datum['primarySource'],
                        'performer_id' => $performer->id ?? null,
                        'report_origin_id' => $reportOrigin->id ?? null,
                        'manufacturer' => $datum['manufacturer'] ?? null,
                        'lot_number' => $datum['lotNumber'] ?? null,
                        'expiration_date' => $datum['expirationDate'] ?? null,
                        'site_id' => $site->id ?? null,
                        'route_id' => $route->id ?? null
                    ]);

                    if (isset($datum['doseQuantity'])) {
                        ImmunizationDoseQuantity::create([
                            'immunization_id' => $immunization->id,
                            'value' => $datum['doseQuantity']['value'],
                            'comparator' => $datum['doseQuantity']['comparator'] ?? null,
                            'unit' => $datum['doseQuantity']['unit'] ?? null,
                            'system' => $datum['doseQuantity']['system'] ?? null,
                            'code' => $datum['doseQuantity']['code'] ?? null
                        ]);
                    }

                    if (isset($datum['explanation']['reasons'])) {
                        foreach ($datum['explanation']['reasons'] as $reasonData) {
                            $reasons = Repository::codeableConcept()->store($reasonData);

                            ImmunizationExplanation::create([
                                'immunization_id' => $immunization->id,
                                'reasons_id' => $reasons->id,
                                'reasons_not_given_id' => null
                            ]);
                        }
                    }

                    if (isset($datum['explanation']['reasonsNotGiven'])) {
                        $reasonsNotGiven = Repository::codeableConcept()->store($datum['explanation']['reasonsNotGiven']);

                        ImmunizationExplanation::create([
                            'immunization_id' => $immunization->id,
                            'reasons_id' => null,
                            'reasons_not_given_id' => $reasonsNotGiven->id
                        ]);
                    }

                    if (!empty($datum['vaccinationProtocols'])) {
                        foreach ($datum['vaccinationProtocols'] as $vaccinationProtocolData) {
                            $authority = Repository::codeableConcept()->store($vaccinationProtocolData['authority']);

                            $immunizationVaccinationProtocol = ImmunizationVaccinationProtocol::create([
                                'immunization_id' => $immunization->id,
                                'dose_sequence' => $vaccinationProtocolData['doseSequence'] ?? null,
                                'description' => $vaccinationProtocolData['description'] ?? null,
                                'authority_id' => $authority->id ?? null,
                                'series' => $vaccinationProtocolData['series'] ?? null,
                                'series_doses' => $vaccinationProtocolData['seriesDoses'] ?? null
                            ]);

                            $targetDiseaseIds = [];
                            foreach ($vaccinationProtocolData['targetDiseases'] as $targetDiseaseData) {
                                $targetDisease = Repository::codeableConcept()->store($targetDiseaseData);

                                $targetDiseaseIds[] = $targetDisease->id;
                            }

                            $immunizationVaccinationProtocol->targetDiseases()->attach($targetDiseaseIds);
                        }
                    }
                }
            } catch (Exception $e) {
                Log::channel('db_errors')->error('Error saving condition', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);

                throw $e;
            }
        });
    }

    /**
     * Get condition data that is related to the encounter.
     *
     * @param  int  $encounterId
     * @return array
     */
    public function get(int $encounterId): array
    {
        return $this->model::with([
            'vaccineCode.coding',
            'context.type.coding',
            'performer.type.coding',
            'reportOrigin.coding',
            'site.coding',
            'route.coding',
            'doseQuantity',
            'vaccinationProtocols.authority.coding',
            'vaccinationProtocols.targetDiseases.coding'
        ])
            ->where('encounter_id', $encounterId)
            ->get()
            ->toArray();
    }

    /**
     * Formatting immunizations for showing in frontend.
     *
     * @param  array  $immunizations
     * @return array
     */
    public function formatForView(array $immunizations): array
    {
        return array_map(static function (array $immunization) {
            if (empty($immunization['explanation']['reasons'])) {
                $immunization['explanation']['reasons'] = [];
            }

            if (empty($immunization['explanation']['reasonsNotGiven'])) {
                $immunization['explanation']['reasonsNotGiven'] = [];
            }

            if (empty($immunization['reportOrigin'])) {
                $immunization['reportOrigin'] = [
                    'coding' => [
                        ['code' => '']
                    ]
                ];
            }
            if (is_null($immunization['doseQuantity'])) {
                $immunization['doseQuantity']['value'] = null;
                $immunization['doseQuantity']['code'] = null;
            }

            $immunization['time'] = CarbonImmutable::parse($immunization['date'])->format('H:i');
            $immunization['date'] = CarbonImmutable::parse($immunization['date'])->format('Y-m-d');

            return $immunization;
        }, $immunizations);
    }
}
