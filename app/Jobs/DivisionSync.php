<?php

namespace App\Jobs;

use App\Classes\eHealth\EHealth;
use App\Classes\eHealth\EHealthResponse;
use App\Core\EHealthJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DivisionSync extends EHealthJob
{
    public const string BATCH_NAME = 'DivisionSync';

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $token, // must be encrypted
        protected int $page
    )
    {
        parent::__construct($this->token, $this->page);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
    }

    protected function sendRequest(): EHealthResponse
    {
        return EHealth::division()->getMany();
    }

    protected function getNextEntityJob(string $token): ?EHealthJob
    {
        // TODO: Implement getNextEntityJob() method.
    }
}
