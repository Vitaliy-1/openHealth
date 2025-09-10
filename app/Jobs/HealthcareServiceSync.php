<?php

namespace App\Jobs;

use App\Core\EHealthJob;
use App\Classes\eHealth\EHealth;
use App\Models\HealthcareService;
use GuzzleHttp\Promise\PromiseInterface;
use App\Classes\eHealth\EHealthResponse;
use Illuminate\Queue\Middleware\RateLimited;

class HealthcareServiceSync extends EHealthJob
{
    public const string BATCH_NAME = 'HealthcareServiceSync';

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        echo 'Starting HealthcareService Sync for user:' . $this->user->id . ', legalEntity:' . $this->legalEntity->id . ', page:' . $this->page . PHP_EOL;

        parent::handle();
    }

    protected function sendRequest(string $token): PromiseInterface|EHealthResponse
    {
        return EHealth::healthcareService()
                ->withToken($token)
                ->getMany(query: ['page' => $this->page]);
    }

    protected function processResponse(EHealthResponse $response): void
    {
        // Implement the logic to process the response and store/update data in the database
    }

    /**
     * Get additional middleware configurations for the job.
     *
     * @return array Returns an array of middleware configurations to be applied to the job
     */
    protected function getAdditionalMiddleware(): array
    {
        return [
            new RateLimited('ehealth-division-get')
        ];
    }
}
