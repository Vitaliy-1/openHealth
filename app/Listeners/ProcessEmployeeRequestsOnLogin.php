<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Classes\eHealth\EHealth;
use App\Core\Arr;
use App\Enums\Employee\RevisionStatus;
use App\Enums\Status;
use App\Events\EHealthUserLogin;
use App\Models\Employee\Employee;
use App\Repositories\EmployeeRepository;
use App\Repositories\Repository;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

readonly class ProcessEmployeeRequestsOnLogin
{
    /**
     * @throws Throwable
     * @throws ConnectionException
     */
    public function handle(EHealthUserLogin $event): void
    {
        // If user doesn't have party, the employee wasn't added through our MIS
        if (!$event->user?->party?->uuid) {
            return;
        }

        $pendingRequests = Repository::employee()->findPendingRequestsForUser($event->user, $event->legalEntity);

        if ($pendingRequests->isEmpty()) {
            return;
        }

        try {
            $ehealthResponse = EHealth::employee()->getMany(
                [
                    'legal_entity_id' => $event->legalEntity->uuid,
                    'party_id' => $event->user->party->uuid,
                    'status' => Status::APPROVED->value,
                ]
            );

            $employeesFromApi = $ehealthResponse->getData();

            if (empty($employeesFromApi)) {
                return;
            }

        } catch (Throwable $e) {
            Log::error('Failed to fetch initial data for employee requests processing.', [
                'user_id' => $event->user->id,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return;
        }

        $approvedEmployeesByPosition = collect($employeesFromApi)->groupBy('position');

        foreach ($pendingRequests as $request) {
            if (!$approvedEmployeesByPosition->has($request->position)) {
                continue;
            }

            $approvedData = $approvedEmployeesByPosition->get($request->position)
                ->firstWhere(
                    fn($employeeFromApi) => $employeeFromApi['start_date'] === $request->start_date->format('Y-m-d') &&
                        $employeeFromApi['employee_type'] === $request->employee_type
                );

            if (!$approvedData) {
                continue;
            }

            DB::transaction(function() use ($approvedData, $request) {
                $detailsResponse = EHealth::employee()->getDetails($approvedData['id'], groupByEntities: true);
                [
                    'employee' => $employee,
                    'party' => $party,
                    'documents' => $documents,
                    'phones' => $phones,
                    'educations' => $educations,
                    'specialities' => $specialities,
                    'qualifications' => $qualifications,
                    'scienceDegrees' => $scienceDegrees,
                ] = $detailsResponse->validate();

                $employeeModel = Employee::firstOrNew(
                    ['uuid' => Arr::pull($employee, 'uuid')],
                    $employee
                );
                $employeeModel->save();
                Repository::employee()->updateDetails($employeeModel, $party, $documents, $phones, $educations, $specialities, $qualifications, $scienceDegrees);

                $request->status = Status::APPROVED->value;
                $request->applied_at = Carbon::parse($approvedData['updated_at'] ?? 'now');
                $request->employee()->associate($employeeModel);

                $request->save();

                if ($request->revision) {
                    $request->revision->update(
                        [
                            'status' => RevisionStatus::APPLIED->value,
                            'deleted_at' => now(),
                        ]
                    );
                }
            });
        }
    }
}
