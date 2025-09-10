<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;
use Exception;
use App\Models\User;
use App\Core\EHealthBatch;
use App\Models\LegalEntity;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use App\Classes\eHealth\EHealthResponse;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Exceptions\EHealth\EHealthResponseException;
use App\Http\Middleware\Jobs\EnsureUserHasActiveSession;

abstract class EHealthJob implements ShouldQueue
{
    use Queueable,
        Batchable,
        InteractsWithQueue;

    public const string BATCH_NAME = 'syncJobs';

    /** @var int Rate limit delay in seconds (50 requests per minute = 1 request every 1.2s, using 2s for safety) */
    protected const int RATE_LIMIT_DELAY = 2;

    // protected const array ERR_WITH_RETRY = [500, 502, 503, 408];

    protected const array ERR_IMMEDIATE_FAIL = [400, 401, 403, 404, 429];

    /**
     * Get the batch name for job
     *
     * @return string
     */
    public function getBatchName(): string
    {
        return static::BATCH_NAME;
    }

    /**
     * Authentication token for EHealth API requests
     *
     * @var string
     */
    protected string $token = '';

    /**
     * User associated with the job for session and policy checks
     *
     * @var User|null
     */
    public ?User $user;

    /**
     * Amount of times to attempt the job if it fails.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public int $timeout = 60;

    /**
     * Amount of time (in seconds) to wait before retrying the job if it fails.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [3, 10, 30];
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware(): array
    {
        return array_merge([
            new EnsureUserHasActiveSession(maxAttempts: $this->tries, retryDelay: 3),
        ], $this->getAdditionalMiddleware());
    }

    public function __construct(
        public LegalEntity $legalEntity,
        protected int $page = 1,
        protected bool $isFirstLogin = false,
        protected ?EHealthJob $nextEntity = null
    ) {
        $this->onQueue('sync');

        // User will be set by middleware
        $this->user = null;
    }

    public function handle(): void
    {
        echo "Processing Job: " . static::BATCH_NAME . " Page: " . $this->page . PHP_EOL;

        $token = $this->getToken();

        if (empty($token)) {
            $this->fail( static::BATCH_NAME . " Page: " . $this->page . " - Missing Token");

            return;
        }

        try {
            $response = $this->sendRequest($token);
        } catch (EHealthResponseException $err) {
            $errCode = $err->getCode();
            echo 'Job Exception (failing job): ' . $err->getMessage() . PHP_EOL;

            if (in_array($errCode, static::ERR_IMMEDIATE_FAIL)) {
                $this->fail($err);

                return;
            }

            echo 'Job will be retried according to backoff settings' . PHP_EOL;

            throw $err; // This will trigger Laravel's retry mechanism
        } catch (Exception $err) {
            // This is an unexpected exception, fail the job
            echo 'Job Exception (failing job): ' . $err->getMessage() . PHP_EOL;

            $this->fail($err);

            return;
        }

        try {
            $this->processResponse($response);
        } catch (Exception $err) {
            echo 'Job Exception (failing job): ' . $err->getMessage() . PHP_EOL;
            // Let the job fail and be cancelled in case of exception
            $this->fail($err);

            return;
        }

        if ($response->isNotLast()) {
            $this->batch()
                ?->add(new static($this->legalEntity, page: $this->page + 1, isFirstLogin: $this->isFirstLogin, nextEntity: $this->nextEntity)
                ->delay(now()->addSeconds(self::RATE_LIMIT_DELAY)));

            return;
        }

        echo "Job COMPLETED: " . static::BATCH_NAME . PHP_EOL;

        $nextJob = $this->getNextEntityJob();

        if ($nextJob !== null) {
            // $nextJob->delay(now()->addSeconds(self::RATE_LIMIT_DELAY));
            echo "Scheduling next job: " . $nextJob::BATCH_NAME . PHP_EOL;

            EHealthBatch::createWithLegalEntity([$nextJob], $nextJob::BATCH_NAME, $this->legalEntity->id, 'sync');
        }
    }

    // Handle job failure
    public function failed(?Throwable $exception): void
    {
        Log::channel('e_health_errors')->error('Sync job failed: ', [
            'EXCEPTION' => $exception::class,
            'message' => $exception->getMessage(),
            'attempts' => $this->attempts(),
            'bastch_id' => $this->batch()?->id,
            'batch_name' => static::BATCH_NAME
        ]);

        echo "Job FAILED: " . static::BATCH_NAME . " Page: " . $this->page . PHP_EOL;

        $this->batch()->cancel();
    }

    /**
     * Get authentication token with deferred initialization
     *
     * @return string
     */
    protected function getToken(): string
    {
        if (empty($this->token)) {
            $this->token = $this->getCurrentToken();
        }

        return $this->token;
    }

    protected function getCurrentToken(): string
    {
        if (!$this->user) {
            return '';
        }

        $session = \DB::table('sessions')->where('user_id', $this->user->id)->first();

        if (!$session) {
            return '';
        }

        $payload = unserialize(base64_decode($session->payload));
        $token = $payload['auth_token'] ?? '';

        // TODO: Remove echoing & logging of token in production
        \Log::info('Session token : ', ['token' => $token]);
        echo "Session token: " . $token. '...' . PHP_EOL;

        return $token;
    }

    // Get next entity job if needed
    protected function getNextEntityJob(): ?EHealthJob
    {
        return $this->nextEntity;
    }

    // Get data from EHealth API
    abstract protected function sendRequest(string $token): PromiseInterface|EHealthResponse;

    // Store or update data in the database
    abstract protected function processResponse(EHealthResponse $response): void;

    /**
     * Get additional middleware specific to the job implementation.
     *
     * This method must be implemented by child classes to define job-specific middleware
     * that will be executed AFTER the base EnsureUserHasActiveSession middleware.
     *
     * Common middleware examples:
     * - RateLimited('rate-limiter-name') for API rate limiting
     * - Custom validation middleware
     * - Logging middleware
     *
     * @return array Array of middleware instances
     *
     * @example
     * protected function getAdditionalMiddleware(): array
     * {
     *     return [
     *         new RateLimited('ehealth-api-calls'),
     *         new CustomLoggingMiddleware(),
     *     ];
     * }
     */
    abstract protected function getAdditionalMiddleware(): array;
}
