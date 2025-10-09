<?php

namespace App\Listeners;

use Throwable;
use App\Models\User;
use App\Enums\JobStatus;
use App\Events\EHealthUserLogin;
use Illuminate\Support\Facades\Log;
use App\Notifications\SyncNotification;
use App\Traits\BatchLegalEntityQueries;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;


class OnRegularLoginSyncronization implements ShouldQueue
{
    use InteractsWithQueue,
        BatchLegalEntityQueries;

    /**
     * This listener will be placed on the 'sync' queue
     *
     * @var string|null
     */
    public $queue = 'sync';

    /**
     * Handle the event.
     */
    public function handle(EHealthUserLogin $event): void
    {
        // Skip if this is the first login
        if ($event->isFirstLogin) {
            return;
        }

        echo 'Regular login synchronization checking for ' . 'legalEntity:' . $event->legalEntity->id . PHP_EOL;

        // Find all failed batches for this legal entity and retry them
        $failedBatches = $this->findFailedBatchesByLegalEntity($event->legalEntity->id, 'ASC');

        if ($failedBatches->isEmpty()) {
            echo 'No failed batches found for legal entity: ' . $event->legalEntity->id . PHP_EOL;

            return;
        }

        foreach ($failedBatches as $batch) {
            echo 'Found related batch: ' . $batch->name . ' id: ' . $batch->id . PHP_EOL;

            // Extract pending jobs from the batches
            $pendingJobs = $this->extractPendingJobsFromBatches($batch);

            if (empty($pendingJobs)) {
                echo 'No pending jobs found in batch: ' . $batch->name . ' id: ' . $batch->id . PHP_EOL;

                continue;
            }

            $this->restartFailedBatch($event->user, $batch, $event->token, $event->legalEntity, $pendingJobs);
        }

        $event->legalEntity->setEntityStatus(JobStatus::PROCESSING);

        Notification::send(User::all(), new SyncNotification('legal_entity', 'resumed'));
    }

    /**
     * Handle a job failure.
     *
     * @param EHealthUserLogin $event
     * @param Throwable $exception
     * @return void
     */
    public function failed(EHealthUserLogin $event, Throwable $exception): void
    {
        $errorMessage = "FirstLoginOwnerSyncronization failed for legal entity ID: {$event->legalEntity->id}";
        $errorDetails = "Error: {$exception->getMessage()}";

        // Log the error
        Log::error($errorMessage, [
            'legal_entity_id' => $event->legalEntity->id,
            'error_message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'listener' => self::class,
        ]);

        // Output to console
        echo $errorMessage . PHP_EOL;
        echo $errorDetails . PHP_EOL;
        echo "Stack trace: " . $exception->getTraceAsString() . PHP_EOL;
    }

}
