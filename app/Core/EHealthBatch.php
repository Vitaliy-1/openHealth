<?php

declare(strict_types=1);

namespace App\Core;

use Illuminate\Bus\Batch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;

/**
 * Helper class for creating batches with legal_entity_id tracking
 */
class EHealthBatch
{
    /**
     * Create a new batch with legal_entity_id tracking
     *
     * @param array $jobs
     * @param string $name
     * @param int|string $legalEntityId
     * @param string $queue
     *
     * @return Batch
     */
    public static function createWithLegalEntity(
        array $jobs,
        string $name,
        int $legalEntityId,
        string $queue = 'sync'
    ): Batch {
        // Create standard batch
        $batch = Bus::batch($jobs)
            ->name($name)
            ->onQueue($queue)
            ->dispatch();

        // Update job_batches with legal_entity_id
        DB::table('job_batches')
            ->where('id', $batch->id)
            ->update(['legal_entity_id' => $legalEntityId]);

        return $batch;
    }

    /**
     * Find batches by legal entity ID
     *
     * @param int $legalEntityId
     *
     * @return Collection
     */
    public static function findByLegalEntity(int $legalEntityId): Collection
    {
        return DB::table('job_batches')
            ->where('legal_entity_id', $legalEntityId)
            ->get();
    }

    /**
     * Find failed batches by legal entity ID
     *
     * @param int $legalEntityId
     *
     * @return Collection
     */
    public static function findFailedByLegalEntity(int $legalEntityId, string $orderBy = 'desc'): Collection
    {
        return DB::table('job_batches')
            ->where('legal_entity_id', $legalEntityId)
            ->where('failed_jobs', '>', 0)
            ->orderBy('cancelled_at', $orderBy)
            ->get();
    }
}
