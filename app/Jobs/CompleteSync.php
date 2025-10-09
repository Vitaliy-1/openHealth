<?php

namespace App\Jobs;

use App\Core\EHealthJob;
use App\Notifications\SyncNotification;
use GuzzleHttp\Promise\PromiseInterface;
use App\Classes\eHealth\EHealthResponse;
use App\Notifications\CustomMessage;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

/**
 * This job is responsible for finalizing a full synchronization operation between different data sources
 *
 * @package App\Jobs
 */
class CompleteSync extends EHealthJob
{
    public const string BATCH_NAME = 'CompleteSync';

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        echo 'Starting CompleteSync for legalEntity : ' . $this->legalEntity->id . PHP_EOL;

        parent::handle();

        if ($this->isFirstLogin) {
            Notification::send(User::all(), new SyncNotification('legal_entity', 'completed'));
        } else {
            //notify users about completion of sync of other entities (used for manual syncs)
            Notification::send(User::all(), new SyncNotification(null, 'completed'));

        }
    }

    // Get data from EHealth API (here it mostly dummy method)
    protected function sendRequest(string $token): PromiseInterface|EHealthResponse|null
    {
        return null;
    }

    // Store or update data in the database (here it mostly dummy method)
    protected function processResponse(?EHealthResponse $response): void
    {
    }

    /**
     * Get additional middleware configurations for the job.
     *
     * @return array Returns an array of middleware configurations to be applied to the job
     */
    protected function getAdditionalMiddleware(): array
    {
        return [];
    }
}
