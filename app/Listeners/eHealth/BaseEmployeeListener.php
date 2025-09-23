<?php

declare(strict_types=1);

namespace App\Listeners\eHealth;

use App\Classes\eHealth\Api\Employee as EmployeeApi;
use App\Enums\Employee\RevisionStatus;
use App\Enums\Status;
use App\Events\EHealthUserLogin;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeRequest;
use App\Repositories\Repository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class BaseEmployeeListener
{
    /**
     * The main entry point for the listener.
     */
    public function handle(EHealthUserLogin $event): void
    {
        if (!$this->shouldProcess($event)) {
            return;
        }

        try {
            $ehealthEmployees = $this->fetchEmployeesFromApi($event);
            $this->afterFetchingEmployees($event, $ehealthEmployees);
        } catch (Throwable $e) {
            Log::error('Failed to fetch employee list from E-Health.', [
                'listener' => static::class,
                'user_id' => $event->user->id,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        if (empty($ehealthEmployees)) {
            return;
        }

        // Get a simple list of UUIDs that are currently in E-Health
        $ehealthEmployeeUuids = collect($ehealthEmployees)->pluck('uuid')->all();

        // Get a list of UUIDs that already exist in our local database for this user
        $localEmployeeUuids = $event->user->employees()
            ->whereIn('uuid', $ehealthEmployeeUuids)
            ->pluck('uuid')
            ->all();

        // Find the difference - these are the employees we need to create
        $newEmployeeUuids = array_diff($ehealthEmployeeUuids, $localEmployeeUuids);

        if (empty($newEmployeeUuids)) {
            return; // Nothing to create
        }

        $newEhealthEmployees = collect($ehealthEmployees)->whereIn('uuid', $newEmployeeUuids);

        // Find the corresponding local requests for the new employees
        $pendingRequestsPairs = $this->getPendingRequestsForNewEmployees($event, $newEhealthEmployees);

        foreach ($pendingRequestsPairs as $pair) {
            $employeeRequest = $pair['request'];
            $approvedData = $pair['api_data'];

            try {
                if ($approvedData) {
                    $this->createEmployeeFromRequest($employeeRequest, $approvedData);
                }
            } catch (Throwable $e) {
                Log::error('Failed to process a new employee from request.', [
                    'listener' => static::class,
                    'employee_request_id' => $employeeRequest->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }

    /**
     * Each concrete listener must implement its own condition to run.
     */
    abstract protected function shouldProcess(EHealthUserLogin $event): bool;

    /**
     * Each concrete listener must implement its own way of fetching data.
     */
    abstract protected function fetchEmployeesFromApi(EHealthUserLogin $event): array;

    protected function afterFetchingEmployees(EHealthUserLogin $event, array &$ehealthEmployees): void
    {
        // By default, do nothing.
    }

    /**
     * Find local EmployeeRequest records that match the new employees found in the API response.
     */
    protected function getPendingRequestsForNewEmployees(EHealthUserLogin $event, Collection $newEmployeesFromApi): Collection
    {
        $pendingRequests = Repository::employee()->findPendingRequestsForUser($event->user, $event->legalEntity);

        return $pendingRequests->map(function (EmployeeRequest $request) use ($newEmployeesFromApi) {
            $apiMatch = $newEmployeesFromApi->firstWhere(function ($apiEmployee) use ($request) {
                return $apiEmployee['position'] === $request->position
                    && $apiEmployee['employee_type'] === $request->employee_type;
            });

            if ($apiMatch) {
                return ['request' => $request, 'api_data' => $apiMatch];
            }

            return null;
        })->filter();
    }

    /**
     * The single source of truth for creating an employee, using Revision data as the primary source.
     *
     * @throws Throwable
     */
    protected function createEmployeeFromRequest(EmployeeRequest $employeeRequest, array $approvedData): void
    {
        // NEW: Get all prepared data from the mapper method
        $mappedData = EmployeeApi::mapCreate($employeeRequest, $approvedData);

        $employeeData = $mappedData['employee'];
        $partyData = $mappedData['party'];
        $doctorData = $mappedData['doctor'];
        $documentsData = $mappedData['documents'];
        $phonesData = $mappedData['phones'];

        DB::transaction(function () use ($employeeData, $partyData, $doctorData, $documentsData, $phonesData, $employeeRequest) {
            $employeeModel = Employee::updateOrCreate(
                ['uuid' => $employeeData['uuid']],
                $employeeData
            );

            Repository::employee()->updateDetails(
                $employeeModel,
                $partyData,
                $documentsData,
                $phonesData,
                $doctorData['educations'] ?? null,
                $doctorData['specialities'] ?? null,
                $doctorData['qualifications'] ?? null,
                $doctorData['scienceDegree'] ?? null
            );

            $this->assignRoleToUser($employeeModel);
            $this->finalizeEmployeeRequest($employeeRequest, $employeeModel);
        });
    }

    /**
     * Assigns the appropriate role to the user if they don't already have it.
     */
    protected function assignRoleToUser(Employee $employeeModel): void
    {
        $user = $employeeModel->user;
        $roleName = $employeeModel->employee_type;
        $legalEntityId = $employeeModel->legal_entity_id;

        if ($user && $roleName && $legalEntityId) {
            setPermissionsTeamId($legalEntityId);
            $user->unsetRelation('roles')->unsetRelation('permissions');

            if (!$user->hasRole($roleName)) {
                $user->assignRole($roleName);
            }
        }
    }

    /**
     * Updates the status of the EmployeeRequest and its revision.
     */
    protected function finalizeEmployeeRequest(EmployeeRequest $employeeRequest, Employee $employeeModel): void
    {
        // Use a direct update to bypass any potential model observers that might be blocking the save.
        // This is a more direct and robust way to set the final state of the request.
        EmployeeRequest::where('id', $employeeRequest->id)->update(
            [
                'status' => Status::APPROVED->value,
                'applied_at' => now(),
                'employee_id' => $employeeModel->id,
            ]
        );

        if ($employeeRequest->revision()->exists()) {
            $employeeRequest->revision->update(['status' => RevisionStatus::APPLIED->value]);
        }
    }
}
