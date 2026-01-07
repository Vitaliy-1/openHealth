<?php

declare(strict_types=1);

namespace App\Livewire\Employee;

use AllowDynamicProperties;
use App\Classes\eHealth\EHealth;
use App\Enums\JobStatus;
use App\Enums\Status;
use App\Exceptions\EHealth\EHealthResponseException;
use App\Jobs\EmployeeSync;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeRequest;
use App\Models\LegalEntity;
use App\Models\Relations\Party;
use App\Notifications\EmployeeSyncCompleted;
use App\Notifications\SyncNotification;
use App\Repositories\Repository;
use App\Traits\BatchLegalEntityQueries;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use JsonException;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

#[AllowDynamicProperties]
class EmployeeIndex extends EmployeeComponent
{
    use WithPagination;
    use BatchLegalEntityQueries;

    // --- Component State for Filters ---
    public string $search = '';
    public array $status = ['APPROVED', 'NEW', 'SIGNED'];
    public array $filter = [
        'phone' => '',
        'email' => '',
        'role' => '',
        'position' => '',
        'division_id' => '',
    ];

    // --- State for Modals ---
    public bool $showDeactivateModal = false;
    public ?int $employeeToDeactivateId = null;
    public ?string $employeeToDeactivateName = null;

    public ?int $employeeToDismissId = null;
    public ?string $employeeToDismissName = null;

    public bool $showDeleteModal = false;
    public ?int $requestToDeleteId = null;
    public ?string $deleteRequestName = null;

    public ?string $batchId = null;
    public string $dismissalMessageType = 'default';

    public int $refreshTrigger = 0;

    private LegalEntity $legalEntity;

    public function boot(): void
    {
        $this->legalEntity = legalEntity();
    }

    public function mount(LegalEntity $legalEntity): void
    {
        $this->legalEntity = $legalEntity;
        $this->loadDivisions($legalEntity);
        $this->loadDictionaries();
    }

    public function applyFilters(): void
    {
        $this->resetPage();
    }

    /**
     * Main computed property to fetch and filter parties.
     */
    #[Computed]
    public function parties(): LengthAwarePaginator
    {
        // 1. We get the basic query from the repository (all the complex SQL is hidden there)
        $query = Repository::employee()->getPartiesWithLatestActivityQuery($this->legalEntity->id);

        // 2. Apply dynamic filters (Search, Email, Phone, etc.)
        // This method (applyDatabaseFilters) remains in the component because it is responsible for UI filtering
        $this->applyDatabaseFilters($query);

        // 3. Return the paginated result
        return $query->paginate(10);
    }

    /**
     * Applies UI filters (Search, Email, Phone, Status) to the query builder.
     */
    private function applyDatabaseFilters(Builder $query): void
    {
        // 1. Filter: Ensure Party is linked to this Legal Entity via Employee or Request
        $query->where(function (Builder $q) {
            $q->whereHas('employees', function ($sub) {
                $sub->where('legal_entity_id', $this->legalEntity->id);
                $this->applyChildFilters($sub);
            })
                ->orWhereHas('employeeRequests', function ($sub) {
                    $sub->where('legal_entity_id', $this->legalEntity->id)
                        ->whereIn('status', [Status::NEW->value, Status::SIGNED->value]);
                    $this->applyChildFilters($sub);
                });
        });

        // 2. Filter: Search Text (Full Name, Case-Insensitive)
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            // PostgreSQL specific: ILIKE is case-insensitive
            $query->whereRaw("CONCAT(last_name, ' ', first_name, ' ', second_name) ILIKE ?", [$searchTerm]);
        }

        // 3. Filter: Email (via Users)
        if (!empty($this->filter['email'])) {
            // ILIKE for emails too
            $query->whereHas('users', fn ($q) => $q->where('email', 'ILIKE', '%' . $this->filter['email'] . '%'));
        }

        // 4. Filter: Phone
        if (!empty($this->filter['phone'])) {
            $query->whereHas('phones', fn ($q) => $q->where('number', 'like', '%' . $this->filter['phone'] . '%'));
        }

        // 5. Filter: Verification Status
        $showVerified = in_array('VERIFIED', $this->status, true);
        $showNotVerified = in_array('NOT_VERIFIED', $this->status, true);

        if ($showVerified && !$showNotVerified) {
            $query->where('verification_status', 'VERIFIED');
        } elseif (!$showVerified && $showNotVerified) {
            $query->where('verification_status', '!=', 'VERIFIED');
        }
    }

    /**
     * Helper to apply role, division, position, and status filters to relationship subqueries.
     */
    private function applyChildFilters(Builder $subQuery): void
    {
        // Status Filter
        if (!empty($this->status)) {
            // Map 'DISMISSED' -> 'STOPPED' for DB query
            $dbStatuses = array_map(fn ($s) => $s === 'DISMISSED' ? 'STOPPED' : $s, $this->status);

            // Remove non-DB statuses (like 'VERIFIED'/'NOT_VERIFIED' which apply to Party)
            $dbStatuses = array_diff($dbStatuses, ['VERIFIED', 'NOT_VERIFIED']);

            if (!empty($dbStatuses)) {
                $subQuery->whereIn('status', $dbStatuses);
            }
        }

        // Division Filter
        if (!empty($this->filter['division_id'])) {
            $subQuery->where('division_id', $this->filter['division_id']);
        }

        // Role Filter
        if (!empty($this->filter['role'])) {
            $subQuery->where('employee_type', $this->filter['role']);
        }

        // Position Filter
        if (!empty($this->filter['position'])) {
            $subQuery->where('position', $this->filter['position']);
        }
    }

    public function showModalDeactivate(int $id): void
    {
        $employee = Employee::with('party')->find($id);
        if (!$employee) {
            return;
        }

        $this->employeeToDeactivateName = $employee->party->fullName ?? __('employees.modals.deactivate.default_name');
        $this->employeeToDeactivateId = $id;

        if ($employee->employee_type === 'DOCTOR') {
            $this->dismissalMessageType = 'doctor';
        } else {
            $this->dismissalMessageType = 'default';
        }

        $this->showDeactivateModal = true;
    }

    public function closeModal(): void
    {
        $this->showDeactivateModal = false;
        $this->reset(['employeeToDeactivateId', 'employeeToDeactivateName', 'dismissalMessageType']);
    }

    public function resetFilters(): void
    {
        $this->reset(['filter', 'status', 'search']);
        $this->status = ['APPROVED', 'NEW'];
        $this->resetPage();
    }

    public function deactivate(): void
    {
        $employee = Employee::find($this->employeeToDeactivateId);
        if (!$employee) {
            $this->closeModal();

            return;
        }

        try {
            $endDate = Carbon::now('UTC')->format('Y-m-d');
            $response = EHealth::employee()->deactivate($employee->uuid, $endDate);

            if (!empty($response)) {
                $employee->update([
                    'status' => Status::STOPPED->value,
                    'end_date' => $endDate,
                ]);

                if ($user = $employee->user) {
                    $roleToRemove = $employee->employee_type;
                    if ($user->hasRole($roleToRemove)) {
                        $user->removeRole($roleToRemove);
                    }
                }

                $this->dispatch('flashMessage', ['message' => __('employees.dismissalSuccess'), 'type' => 'success']);
            } else {
                $this->dispatch(
                    'flashMessage',
                    ['message' => __('employees.dismissalEhealthError'), 'type' => 'error']
                );
            }
        } catch (\Exception $e) {
            $this->dispatch(
                'flashMessage',
                ['message' => __('employees.requestError', ['error' => $e->getMessage()]), 'type' => 'error']
            );
        }

        $this->closeModal();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Throwable
     * @throws NotFoundExceptionInterface
     */
    public function sync(): void
    {
        $user = Auth::user();
        $user->notify(new SyncNotification('employee', 'started'));

        $this->dispatch('flashMessage', [
            'message' => __('employees.sync.started'),
            'type' => 'success',
        ]);

        try {
            $response = EHealth::employee()->getMany(['legal_entity_id' => legalEntity()->uuid]);
        } catch (ConnectionException $e) {
            Log::error('Employee sync failed: No connection to E-Health.', ['error' => $e->getMessage()]);
            $this->dispatch(
                'flashMessage',
                ['message' => __('errors.ehealth.messages.no_connection'), 'type' => 'error']
            );

            return;
        } catch (EHealthResponseException $e) {
            Log::error(
                'Employee sync failed: E-Health API error.',
                ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]
            );
            $this->dispatch(
                'flashMessage',
                ['message' => __('employees.requestError', ['error' => $e->getMessage()]), 'type' => 'error']
            );

            return;
        } catch (\Exception $e) {
            Log::error(
                'Employee sync failed: An unexpected error occurred during initiation.',
                ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]
            );
            $this->dispatch('flashMessage', ['message' => __('employees.sync.error'), 'type' => 'error']);

            return;
        }

        $employees = $response->validate();
        foreach ($employees as &$item) {
            unset($item['party'], $item['doctor'], $item['med_admin'], $item['pharmacist'], $item['specialist'], $item['legal_entity'], $item['division']);

            $item['legal_entity_id'] = legalEntity()->id;
            $item['sync_status'] = JobStatus::PARTIAL->value;
        }
        unset($item);

        Employee::upsert($employees, uniqueBy: ['uuid']);

        $token = session()->get(config('ehealth.api.oauth.bearer_token'));

        if ($response->isNotLast()) {
            Bus::batch([
                new EmployeeSync(
                    legalEntity: $this->legalEntity,
                    page: 2,
                    nextEntity: null
                ),
            ])
                ->withOption('legal_entity_id', $this->legalEntity->id)
                ->withOption('token', Crypt::encryptString($token))
                ->withOption('user', $user)
                ->then(function (Batch $batch) use ($user) {
                    app(PermissionRegistrar::class)->forgetCachedPermissions();
                    $message = __('employees.sync.completed_successfully', [
                        'processed' => $batch->processedJobs,
                        'total' => $batch->totalJobs,
                    ]);
                    $user->notify(new EmployeeSyncCompleted($message, 'success'));
                })->catch(callback: function (Batch $batch, \Throwable $e) use ($user) {
                    $message = __('employees.sync.failed');
                    Log::error('Employee sync batch failed.', ['batch_id' => $batch->id, 'exception' => $e]);
                    $user->notify(new EmployeeSyncCompleted($message, 'error'));
                })
                ->onQueue('sync')
                ->name('Employee Full Sync')
                ->dispatch();
        } else {
            Bus::batch($this->getEmployeeDetailsStartJob($this->legalEntity, null))
                ->withOption('legal_entity_id', $this->legalEntity->id)
                ->withOption('token', Crypt::encryptString($token))
                ->withOption('user', $user)
                ->then(function (Batch $batch) use ($user) {
                    $message = __('employees.sync.completed_successfully', [
                        'processed' => $batch->processedJobs,
                        'total' => $batch->totalJobs,
                    ]);
                    $user->notify(new EmployeeSyncCompleted($message, 'success'));
                })->catch(callback: function (Batch $batch, \Throwable $e) use ($user) {
                    $message = __('employees.sync.failed');
                    Log::error('Employee sync batch failed.', ['batch_id' => $batch->id, 'exception' => $e]);
                    $user->notify(new EmployeeSyncCompleted($message, 'error'));
                })
                ->onQueue('sync')
                ->name('Employee Full Sync')
                ->dispatch();

            // $this->batchId = $batch->id;
        }
    }

    /**
     * Synchronize a specific employee.
     * Uses the parent syncEmployeeData method.
     */
    public function syncOne(int $employeeId): void
    {
        $employee = Employee::with(['user', 'party'])->find($employeeId);

        if (!$employee) {
            $this->dispatch('flashMessage', ['message' => 'Співробітника не знайдено', 'type' => 'error']);

            return;
        }

        // Call the core logic from EmployeeComponent
        $success = $this->syncEmployeeData($employee);

        if ($success) {
            $this->refreshTrigger++;
        }
    }

    public function confirmRequestDeletion(int $id): void
    {
        $request = EmployeeRequest::with('party')->find($id);

        if (!$request) {
            return;
        }

        $this->requestToDeleteId = $id;
        $this->deleteRequestName = $request->party?->fullName ?? __('employees.modals.delete_draft.default_name');

        $this->showDeleteModal = true;
    }

    /**
     * This method is triggered by the "Delete" button in the modal window.
     * It retrieves the stored ID and executes the deletion logic.
     */
    public function deleteRequest(): void
    {
        if ($this->requestToDeleteId) {
            $request = EmployeeRequest::find($this->requestToDeleteId);

            // Ensure the request exists and is a local draft (no UUID) before deleting
            if ($request && !$request->uuid) {
                $request->delete();
                $this->dispatch(
                    'flashMessage',
                    ['message' => __('employees.draft.delete_success'), 'type' => 'success']
                );
            }

            // Close the modal after deletion
            $this->showDeleteModal = false;
            $this->requestToDeleteId = null;

            // Dispatch a success message (optional, requires a listener)
            // Note: You have two dispatches here, ensure you don't show double notifications
            $this->dispatch('flashMessage', ['message' => 'Request deleted successfully', 'type' => 'success']);
        }
    }

    /**
     * Renders the component view.
     *
     * @throws JsonException
     */
    public function render(): object
    {
        $filterKey = md5(
            $this->search .
            implode(',', $this->status) .
            json_encode($this->filter, JSON_THROW_ON_ERROR) .
            $this->getPage() .
            $this->refreshTrigger
        );

        return view('livewire.employee.employee-index', [
            'parties' => $this->parties,
            'dictionaries' => $this->dictionaries,
            'filterKey' => $filterKey,
        ]);
    }
}
