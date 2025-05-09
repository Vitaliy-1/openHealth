<?php

declare(strict_types=1);

namespace App\Repositories\MedicalEvents;

use App\Models\MedicalEvents\Sql\ConditionEvidence;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ConditionRepository extends BaseRepository
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
                    $reportOrigin = null;
                    $asserter = null;
                    $severity = null;

                    if (isset($datum['asserter'])) {
                        $asserter = Repository::identifier()->store($datum['asserter']['identifier']['value']);
                        Repository::codeableConcept()->attach($asserter, $datum['asserter']);
                    }

                    $context = Repository::identifier()->store($datum['context']['identifier']['value']);
                    Repository::codeableConcept()->attach($context, $datum['context']);

                    if (isset($datum['reportOrigin'])) {
                        $reportOrigin = Repository::codeableConcept()->store($datum['reportOrigin']);
                    }

                    $code = Repository::codeableConcept()->store($datum['code']);

                    if (isset($datum['severity'])) {
                        $severity = Repository::codeableConcept()->store($datum['severity']);
                    }

                    $condition = $this->model::create([
                        'uuid' => $datum['id'],
                        'encounter_id' => $encounterId,
                        'primary_source' => $datum['primarySource'],
                        'asserter_id' => $asserter?->id,
                        'report_origin_id' => $reportOrigin?->id,
                        'context_id' => $context->id,
                        'code_id' => $code->id,
                        'clinical_status' => $datum['clinicalStatus'],
                        'verification_status' => $datum['verificationStatus'],
                        'severity_id' => $severity?->id,
                        'onset_date' => $datum['onsetDate'],
                        'asserted_date' => $datum['assertedDate'] ?? null
                    ]);

                    if (!empty($datum['evidences'])) {
                        foreach ($datum['evidences']['codes'] as $evidence) {
                            $codes = Repository::codeableConcept()->store($evidence);

                            ConditionEvidence::create([
                                'condition_id' => $condition->id,
                                'codes_id' => $codes->id,
                            ]);
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
            'asserter',
            'reportOrigin.coding',
            'context.type.coding',
            'code.coding',
            'severity.coding'
        ])
            ->where('encounter_id', $encounterId)
            ->get()
            ->toArray();
    }
}
