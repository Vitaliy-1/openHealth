<?php

declare(strict_types=1);

namespace App\Http\Middleware\Jobs;

use Closure;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
// use Spatie\Permission\PermissionRegistrar;

/**
 * Job middleware to ensure user with active session exists before processing
 *
 * This middleware will:
 * 1. Check if job has user property and legal entity
 * 2. If user not found, try to find user with active session for the legal entity
 * 3. If still no user found, retry job up to maxAttempts times
 * 4. If max attempts reached, fail the job
 * 5. If user found, set it on job and continue processing
 */
class EnsureUserHasActiveSession
{
    protected const array JOBS_POLICY_MAP = [
        'DivisionSync' => ['policy' => 'viewAny', 'model' => '\App\Models\Division'],
        'HealthcareServiceSync' => ['policy' => 'viewAny', 'model' => '\App\Models\HealthcareService'],
    ];

    /**
     * Maximum number of attempts to find user with active session
     *
     * @var int
     */
    protected int $maxAttempts;

    /**
     * Delay in seconds between retry attempts
     *
     * @var int
     */
    protected int $retryDelay;

    public function __construct(int $maxAttempts = 3, int $retryDelay = 3)
    {
        $this->maxAttempts = $maxAttempts;
        $this->retryDelay = $retryDelay;
    }

    /**
     * Process the queued job.
     *
     * @param mixed $job
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($job, Closure $next)
    {
        // Check if the job has the required properties
        if (!property_exists($job, 'user') || !property_exists($job, 'legalEntity')) {
            return $next($job);
        }

        // If user is already set and valid, continue
        if ($job->user instanceof User) {
            return $next($job);
        }

        // Try to get user with active session
        $user = $this->getUserWithActiveSession($job);

        if (!$user) {
            // Check if we've exceeded max attempts
            if ($job->attempts() >= $this->maxAttempts) {
                $message = "Max attempts ({$this->maxAttempts}) reached, no suitable user with active session found for legal entity {$job->legalEntity->id}";

                echo $message . PHP_EOL;

                Log::warning('Job failed due to missing user session', [
                    'legal_entity_id' => $job->legalEntity->id,
                    'attempts' => $job->attempts(),
                    'max_attempts' => $this->maxAttempts,
                    'job_class' => get_class($job)
                ]);

                $job->fail($message);

                return;
            }

            echo "No suitable user with active session found for legal entity {$job->legalEntity->id}, retrying in {$this->retryDelay} seconds... (attempt {$job->attempts()}/{$this->maxAttempts})" . PHP_EOL;

            $job->release($this->retryDelay);

            return;
        }

        // Set the user and continue
        $job->user = $user;

        $job->user->unsetRelation('roles')->unsetRelation('permissions');

        echo "Found user {$user->id} with active session for legal entity {$job->legalEntity->id}" . PHP_EOL;

        return $next($job);
    }

    /**
     * Get user with active session for the legal entity
     *
     * This method performs a complex database query to find users that meet two specific criteria:
     * 1. User must be an employee of the specified legal entity
     * 2. User must have an active session (record exists in sessions table)
     *
     * The query uses Eloquent's whereHas() and whereExists() methods for performance optimization:
     * - whereHas('employees'): Uses LEFT JOIN to check user-employee relationship through employees table
     * - whereExists(): Uses EXISTS subquery to check for session records without joining session data
     *
     * @param mixed $job The job instance containing legalEntity property
     * @return User|null
     */
    protected function getUserWithActiveSession($job): ?User
    {
        // Execute complex query to find users matching both criteria
        $users = User::whereHas('employees', function ($query) use ($job) {
                // FIRST CRITERIA: User must be employee of this legal entity
                // This creates a LEFT JOIN with employees table and filters by legal_entity_id
                // SQL equivalent: LEFT JOIN employees ON users.id = employees.user_id WHERE employees.legal_entity_id = ?
                $query->where('legal_entity_id', $job->legalEntity->id);
            })
            ->whereExists(function ($query) {
                // SECOND CRITERIA: User must have active session
                // This creates an EXISTS subquery to check sessions table
                // Using SELECT 1 for performance - we only need to verify existence, not retrieve data
                // SQL equivalent: EXISTS (SELECT 1 FROM sessions WHERE sessions.user_id = users.id)
                $query->select(DB::raw(1))
                    ->from('sessions')
                    // whereColumn creates correlation between main query and subquery
                    // This ensures we check sessions for the specific user from outer query
                    ->whereColumn('sessions.user_id', 'users.id');
            })
            ->get();

        echo "Found " . $users->count() . " users with active sessions for legal entity {$job->legalEntity->id}" . PHP_EOL;

        // User that can be able to do synchronization
        $userThatCanBeAble = null;

        setPermissionsTeamId($job->legalEntity->id);

        // Return first user from collection that can be able to do synchronization, or null if none found
        foreach ($users as $user) {
            // app(PermissionRegistrar::class)->forgetCachedPermissions();

            if (! $user->relationLoaded('roles')) {
                $user->loadMissing('roles', 'permissions');
            }

            if ($this->checkJobPolicy($job, $user)) {
                $userThatCanBeAble = $user;

                break;
            }
        }

        return $userThatCanBeAble;
    }

    /**
     * Check if user has the required policy permissions for the specific job type
     *
     *
     * @param mixed $job The job instance (must have getBatchName() method or BATCH_NAME constant)
     * @param User $user The user to check permissions for
     *
     * @return bool True if user has required permissions, false otherwise
     *
     * @see static::JOBS_POLICY_MAP Array mapping job names to required policies and models
     */
    protected function checkJobPolicy($job, $user): bool
    {
        // Get BATCH_NAME using method (more flexible approach)
        $batchName = method_exists($job, 'getBatchName') ? $job->getBatchName() : $job::BATCH_NAME;

        if (empty($batchName) || !array_key_exists($batchName, static::JOBS_POLICY_MAP)) {
            return false;
        }

        ['policy' => $policy, 'model' => $model] = static::JOBS_POLICY_MAP[$batchName] ?? [null, null];

        if ($policy === null || $model === null) {
            return false;
        }

        return $user->can($policy, $model);
    }
}
