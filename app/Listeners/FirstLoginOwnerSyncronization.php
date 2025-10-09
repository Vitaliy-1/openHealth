<?php

namespace App\Listeners;

use Throwable;
use App\Models\User;
use App\Jobs\EmployeeSync;
use App\Enums\JobStatus;
use App\Jobs\DivisionSync;
use App\Events\EHealthUserLogin;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use App\Notifications\SyncNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

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
     * Handle the event.
     */
    public function handle(EHealthUserLogin $event): void
    {
        // This need to be user with roles and permissions loaded
        setPermissionsTeamId($event->legalEntity->id);

        if (!$event->isFirstLogin || !$event->user->hasRole('OWNER')) {
            return;
        }

        // TODO: remove it after testing
        echo 'First login synchronization started. ' . 'legalEntity:' . $event->legalEntity->id. PHP_EOL;

        $employeeJob = new EmployeeSync(
            legalEntity: $event->legalEntity,
            isFirstLogin: true
        );

        $initialJob = new DivisionSync(
            legalEntity: $event->legalEntity,
            nextEntity: $employeeJob,
            isFirstLogin: true
        );

        Bus::batch([$initialJob])
            ->name('FirstLoginSync')
            ->withOption('legal_entity_id', $event->legalEntity->id)
            ->withOption('token', $event->token) // Here token is encrypted
            ->withOption('user', $event->user)
            ->onQueue('sync')
            ->dispatch();

        $event->legalEntity->setEntityStatus(JobStatus::PROCESSING);

        Notification::send(User::all(), new SyncNotification('legal_entity', 'started'));
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
