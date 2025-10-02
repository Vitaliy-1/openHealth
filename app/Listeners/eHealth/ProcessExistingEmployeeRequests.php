<?php

declare(strict_types=1);

namespace App\Listeners\eHealth;

use App\Classes\eHealth\EHealth;
use App\Enums\Employee\RequestStatus;
use App\Enums\Status;
use App\Events\EHealthUserLogin;
use Illuminate\Http\Client\ConnectionException;

class ProcessExistingEmployeeRequests extends BaseEmployeeListener
{
    /**
     * This listener should only process if the user's party is synced
     * AND if there are any actual employee requests in a syncable status.
     */
    protected function shouldProcess(EHealthUserLogin $event): bool
    {
        if (!isset($event->user->party->uuid)) {
            return false;
        }

        return $event->user->employeeRequests()
            ->whereIn('status', RequestStatus::getStatusesForSync())
            ->exists();

    }

    /**
     * Fetches employee data using the party's UUID.
     *
     * @throws ConnectionException
     */
    protected function fetchEmployeesFromApi(EHealthUserLogin $event): array
    {
        $apiFilters = [
            'legal_entity_id' => $event->legalEntity->uuid,
            'status' => Status::APPROVED->value,
            'party_id' => $event->user->party->uuid
        ];

        return EHealth::employee()->getMany($apiFilters)->validate();
    }
}
