<?php

namespace App\Jobs;

use App\Core\EHealthJob;
use App\Models\Division;
use App\Classes\eHealth\EHealth;
use App\Jobs\HealthcareServiceSync;
use GuzzleHttp\Promise\PromiseInterface;
use App\Classes\eHealth\EHealthResponse;
use Illuminate\Queue\Middleware\RateLimited;

class DivisionSync extends EHealthJob
{
    public const string BATCH_NAME = 'DivisionSync';

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        echo 'Starting DivisionSync for user:' . $this->user->id . ', legalEntity:' . $this->legalEntity->id . ', page:' . $this->page . PHP_EOL;

        parent::handle();
    }

    protected function sendRequest(string $token): PromiseInterface|EHealthResponse
    {
        return EHealth::division()
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
