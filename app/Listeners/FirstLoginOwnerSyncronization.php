<?php

namespace App\Listeners;

use Throwable;
use App\Core\EHealthBatch;
use App\Jobs\DivisionSync;
use App\Events\EhealthUserLoggedIn;
use App\Jobs\HealthcareServiceSync;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FirstLoginOwnerSyncronization implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * This listener will be placed on the 'sync' queue
     *
     * @var string|null
     */
    public $queue = 'sync';

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EhealthUserLoggedIn $event): void
    {
        if (!$event->isFirstLogin) {
            return;
        }

        echo 'First login synchronization started. ' . 'legalEntity:' . $event->legalEntity->id. PHP_EOL;

        $healthcareServiceJob = new HealthcareServiceSync($event->legalEntity, isFirstLogin: true);

        $initialJob = new DivisionSync($event->legalEntity, isFirstLogin: true, nextEntity: $healthcareServiceJob);

        EHealthBatch::createWithLegalEntity([$initialJob], 'FirstLoginSync', $event->legalEntity->id, 'sync');
    }

    /**
     * Handle a job failure.
     *
     * @param EhealthUserLoggedIn $event
     * @param Throwable $exception
     * @return void
     */
    public function failed(EhealthUserLoggedIn $event, Throwable $exception): void
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
