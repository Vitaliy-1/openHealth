<?php

declare(strict_types=1);

namespace App\Livewire\Division;

use App\Classes\eHealth\Api\Job;
use App\Enums\JobStatus;
use App\Jobs\EmployeeSync;
use App\Notifications\EmployeeSyncCompleted;
use App\Notifications\SyncNotification;
use Throwable;
use Exception;
use Livewire\WithPagination;
use App\Classes\eHealth\EHealth;
use App\Repositories\Repository;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\View;
use App\Livewire\Division\Trait\HasAction;
use Illuminate\Http\Client\ConnectionException;
use App\Exceptions\EHealth\EHealthResponseException;
use App\Exceptions\EHealth\EHealthValidationException;
use App\Jobs\DivisionSync;
use App\Models\LegalEntity;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Crypt;

class DivisionIndex extends DivisionComponent
{
    use WithPagination,
        HasAction;

    #[Computed]
    public function tableHeaders(): array
    {
        return [
            __('forms.name'),
            __('forms.type'),
            __('Телефон'),
            __('Email'),
            __('Статус'),
            __('forms.action'),
        ];
    }

    public function mount(): void
    {
        $this->setDictionary();
    }

    /**
     * Resets the pagination when the search term is updated.
     *
     * It ensures that when a user starts searching, the pagination
     * is reset to the first page to show the most relevant results.
     *
     * @return void
     */
    public function updatingDivisionFormSearch()
    {
        $this->resetPage();
    }

    /**
     * Synchronize all the Divisisons with stored on the eHealths side
     *
     * @return void
     *
     * @throws Exception|ConnectionException
     */
    public function sync(): void
    {
        if (! $this->isDivisionCanSync()) {
            session()->flash('error', __('divisions.request.sync.errors.cannot_sync'));

            return;
        }

        $response = null;

        try {
            session()->flash('success', __('Синхронізація запущена. Будь ласка, зачекайте...'));

            $response = EHealth::division()->getMany();

            $divisions = $response->validate();

            Repository::division()->saveDivisionsList($divisions);
        } catch (EHealthResponseException $err) {
            Log::channel('e_health_errors')->error(self::class . ':createDivision', ['error' => $err->getMessage()]);
            session()->flash('error', __('errors.ehealth.messages.server_error'));

            return;
        } catch (EHealthValidationException $err) {
            Log::channel('e_health_errors')->error(self::class . ':createDivision', ['error' => $err->getDetails()]);

            session()->flash('error', __('errors.ehealth.messages.server_error'));

            return;
        } catch (Throwable $err) {
            Log::channel('db_errors')->error(static::class . ': [syncDivisions]: ', ['error' => $err->getMessage()]);

            session()->flash('error', __('divisions.request.sync.errors.fail'));

            return;
        }

        // If there are more pages, dispatch a job to handle the rest
        if ($response?->isNotLast()) {

            $token = session()->get(config('ehealth.api.oauth.bearer_token'));
            $user = Auth::user();

            Bus::batch([
                new DivisionSync(
                        legalEntity: legalEntity(),
                        page: 2,
                        nextEntity: null
                    )
            ])
            ->withOption('legal_entity_id', legalEntity()->id)
            ->withOption('token', Crypt::encryptString($token))
            ->withOption('user', $user)
            ->then(function (Batch $batch) use ($user) {
                $user->notify(new SyncNotification('division', 'complete'));
            })->catch(callback: function (Batch $batch, Throwable $e) use ($user) {
                Log::error('Division sync batch failed.', [
                    'batch_id' => $batch->id,
                    'exception' => $e
                ]);

                $user->notify(new SyncNotification('division', 'failed'));
            })
            ->onQueue('sync')
            ->name('DivisionSync')
            ->dispatch();
        } else {
            session()->flash('success', __(__('Інформацію успішно оновлено')));
        }
    }

    /**
     * Checks if division synchronization is allowed for the current legal entity.
     *
     * Synchronization is allowed if the division sync status is not COMPLETED, PAUSED, or FAILED.
     *
     * @return bool
     */
    protected function isDivisionCanSync(): bool
    {
        $syncStatus = legalEntity()?->getEntityStatus(LegalEntity::ENTITY_DIVISION);

        // If $syncStatus is null it means that sync was never run for this entity (so can be synced)
        return !$syncStatus ||
            $syncStatus === JobStatus::COMPLETED->value ||
            $syncStatus === JobStatus::PAUSED->value ||
            $syncStatus === JobStatus::FAILED->value;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render(): View
    {
        $perPage = config('pagination.per_page');

        $divisions= legalEntity()
            ?->divisions()
            ->orderBy('uuid')
            ->search($this->divisionForm->search)
            ->paginate($perPage);

        return view('livewire.division.division-index', compact('divisions'));
    }
}
