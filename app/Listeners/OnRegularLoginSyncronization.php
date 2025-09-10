<?php

namespace App\Listeners;

use App\Core\EHealthBatch;
use App\Events\EhealthUserLoggedIn;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnRegularLoginSyncronization implements ShouldQueue
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
        if ($event->isFirstLogin) {
            return;
        }

        echo 'Regular login synchronization checking for ' . ', legalEntity:' . $event->legalEntity->id . PHP_EOL;

        // Find all failed batches for this legal entity and retry them
        $failedBatches = EHealthBatch::findFailedByLegalEntity($event->legalEntity->id, 'ASC');

        foreach ($failedBatches as $batch) {
            echo 'Found related batch: ' . $batch->name . ' id: ' . $batch->id . PHP_EOL;

            Artisan::call('queue:retry-batch', ['id' => $batch->id]);
        }

    }
}
