<?php

declare(strict_types=1);

namespace App\Core;

use App\Classes\eHealth\EHealthResponse;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Bus;

abstract class EHealthJob implements ShouldQueue
{
    use Queueable;
    use Batchable;

    /**
     * This should be associated with the specific queue
     * @var string|null
     */
    public $queue = 'sync';

    public function __construct(
        protected string $token, // must be encrypted
        protected int $page = 1,
        protected ?bool $nextEntity = false
    )
    {

    }

    public function handle(): void
    {
        try {
            $response = $this->sendRequest();
        } catch (\Exception $e) {
            $this->batch()->cancel();
        }

        if (!$response->isNotLast()) {
            $this->batch()->add(new static($this->token, $this->page));
        }

        if (!is_null($this->nextEntity)) {
            Bus::batch([$this->getNextEntityJob($this->token)])->name(static::BATCH_NAME)->dispatch();
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(): void
    {
        $this->batch()->cancel();
    }

    abstract protected function sendRequest(): EHealthResponse;

    abstract protected function getNextEntityJob(string $token): ?EHealthJob;
}
