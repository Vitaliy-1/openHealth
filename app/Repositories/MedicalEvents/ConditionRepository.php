<?php

declare(strict_types=1);

namespace App\Repositories\MedicalEvents;

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
            $repository = new Repository();

            try {
                foreach ($data as $datum) {
                    $reportOrigin = null;
                    $asserter = null;
                    $severity = null;

                    if (isset($datum['asserter'])) {
                        $asserter = $repository::identifier()->store($datum['asserter']['identifier']['value']);
                    }

                    $context = $repository::identifier()->store($datum['context']['identifier']['value']);

                    if (isset($datum['report_origin'])) {
                        $reportOrigin = $repository::codeableConcept()->store($datum['report_origin']);
                    }

                    $code = $repository::codeableConcept()->store($datum['code']);

                    if (isset($datum['severity'])) {
                        $severity = $repository::codeableConcept()->store($datum['severity']);
                    }

                    $this->model::create([
                        'uuid' => $datum['id'],
                        'encounter_id' => $encounterId,
                        'primary_source' => $datum['primary_source'],
                        'asserter_id' => $asserter?->id,
                        'report_origin_id' => $reportOrigin?->id,
                        'context_id' => $context->id,
                        'code_id' => $code->id,
                        'clinical_status' => $datum['clinical_status'],
                        'verification_status' => $datum['verification_status'],
                        'severity_id' => $severity?->id,
                        'onset_date' => $datum['onset_date'],
                        'asserted_date' => $datum['asserted_date'] ?? null
                    ]);

                    if (isset($datum['asserter'])) {
                        $repository::codeableConcept()->attach($asserter, $datum['asserter']);
                    }

                    $repository::codeableConcept()->attach($context, $datum['context']);
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
            'context',
            'code.coding',
            'severity.coding'
        ])
            ->where('encounter_id', $encounterId)
            ->get()->toArray();
    }
}
