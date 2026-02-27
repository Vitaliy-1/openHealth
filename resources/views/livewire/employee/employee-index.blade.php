<div>
    @php
        use App\Enums\User\Role;
        use App\Enums\JobStatus;

        $currentUser = auth()->user();
        // We cache the hospital ID so as not to call the legalEntity() function 100 times in a loop
        $currentLegalEntityId = legalEntity()->id;

        // Cache access rights with an array
       $permissions = [
        'employee_view' => $currentUser->can('employee:details'),
        'employee_write' => $currentUser->can('employee:write'),
        'employee_deactivate' => $currentUser->can('employee:deactivate'),
        'request_view' => $currentUser->can('employee_request:details'),
        'request_write' => $currentUser->can('employee_request:write'),
        'request_delete' => $currentUser->can('employee_request:write'),
    ];
    @endphp

    <x-header-navigation class="items-start" x-data="{ showFilter: false }">

        <x-slot name="title">
            {{ __('forms.employees') }}
        </x-slot>

        @can('create', \App\Models\Employee\EmployeeRequest::class)
            <div class="mt-3 ml-0 flex flex-col sm:flex-row sm:flex-wrap gap-2 self-start">
                <a href="{{ route('employee-request.create', ['legalEntity' => $currentLegalEntityId]) }}"
                   class="button-primary flex items-center gap-2">
                    @icon('plus', 'w-4 h-4')
                    {{ __('forms.new_employee') }}</a>
                <button
                    wire:click="{{ !$this->isSync ? 'sync' : '' }}"
                    type="button"
                    class="{{ $this->isSync ? 'button-sync-disabled' : 'button-sync' }} flex items-center gap-2 whitespace-nowrap"
                    {{ $this->isSync ? 'disabled' : '' }}
                >
                    @icon('refresh', 'w-4 h-4')
                    {{ __('forms.synchronise_with_eHealth') }}
                </button>
            </div>
        @endcan

        <x-slot name="navigation">
            <div class="flex flex-col -my-4">
                <form wire:submit.prevent="applyFilters">
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div class="flex flex-col lg:flex-row items-stretch lg:items-end gap-2 lg:gap-4 w-full">
                            <div class="w-full lg:w-96">
                                <x-forms.form-group>
                                    <x-slot name="label">
                                        <label for="employee_search"
                                               class="text-sm font-medium text-gray-900 dark:text-white block mb-2 flex items-center gap-1">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                            <span>{{ __('forms.employee_search') }}</span>
                                        </label>
                                    </x-slot>
                                    <x-slot name="input">
                                        <div class="form-group group w-full">
                                            <input type="text"
                                                   id="employee_search"
                                                   placeholder=" "
                                                   class="input peer"
                                                   wire:model.defer="search"
                                                   autocomplete="off" />
                                            <label for="employee_search" class="label">ПІБ</label>
                                        </div>
                                    </x-slot>
                                </x-forms.form-group>
                            </div>
                            <button type="button"
                                    class="button-minor flex items-center justify-center gap-2 w-full lg:w-auto self-stretch lg:self-auto lg:-translate-y-[9px]"
                                    @click="showFilter = !showFilter">
                                @icon('adjustments', 'w-4 h-4')
                                <span>{{ __('forms.additional_search_parameters') }}</span>
                            </button>
                        </div>

                        <div x-cloak x-show="showFilter" x-transition class="pt-0 mt-1">
                            <div class="form-row-4">
                                <div class="form-group phone-wrapper">
                                    <input wire:model.defer="filter.phone"
                                           wire:keydown.enter="applyFilters"
                                           type="tel" placeholder=" "
                                           class="peer input pl-10 with-leading-icon text-gray-500"
                                           x-mask="+380999999999" id="filter_phone" />
                                    <label for="filter_phone" class="label pl-10">{{ __('forms.phone') }}</label>
                                </div>
                                <div class="form-group group">
                                    <input wire:model.defer="filter.email" wire:keydown.enter="applyFilters"
                                           name="filter_email" id="filter_email" class="input peer" placeholder=" "
                                           autocomplete="off" />
                                    <label for="filter_email" class="label">Email</label>
                                </div>
                            </div>
                            <div class="form-row-4">
                                <div class="form-group group">
                                    <select wire:model.defer="filter.role" wire:keydown.enter="applyFilters"
                                            id="filter_role"
                                            class="input peer text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                                    >
                                        <option value="">Всі ролі</option>
                                        @foreach($dictionaries['EMPLOYEE_TYPE'] ?? [] as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <label for="filter_role" class="label">Роль працівника</label>
                                </div>
                                <div class="form-group group">
                                    <select wire:model.defer="filter.position" wire:keydown.enter="applyFilters"
                                            id="filter_position"
                                            class="input peer text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                                    >
                                        <option value="">Всі посади</option>
                                        @foreach($dictionaries['POSITION'] ?? [] as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <label for="filter_position" class="label">{{ __('forms.position') }}</label>
                                </div>
                            </div>
                            <div class="form-row-4">
                                <div class="form-group group">
                                    <select wire:model.defer="filter.division_id" wire:keydown.enter="applyFilters"
                                            name="filter_division"
                                            id="filter_division"
                                            class="input peer text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                                    >
                                        <option value="">Всі підрозділи</option>
                                        @foreach($divisions ?? [] as $division)
                                            <option value="{{ $division['id'] }}">{{ $division['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <label for="filter_division" class="label">Медичний заклад</label>
                                </div>
                                <div class="form-group group"
                                     x-data="{ open: false, selectedStatuses: $wire.entangle('status') }">
                                    <label for="statusFilter" class="label">{{ __('forms.status.label') }}</label>
                                    <div class="relative">

                                        <input type="text"
                                               id="statusFilter"
                                               class="input peer w-full cursor-pointer text-gray-500 dark:text-gray-400"
                                               placeholder="Оберіть статуси"
                                               x-on:click="open = !open"
                                               :value="selectedStatuses.length ? selectedStatuses.map(s => {
                                                   if (s === 'APPROVED') return '{{ __('forms.active') }}';
                                                   if (s === 'NEW') return '{{ __('forms.draft') }}';
                                                   if (s === 'SIGNED') return '{{ __('forms.status.sent') }}';
                                                   if (s === 'DISMISSED') return '{{ __('forms.dismissed') }}';

{{--                                                   if (s === 'VERIFIED') return '{{ __('forms.verified') ';--}}
{{--                                                   if (s === 'NOT_VERIFIED') return '{{ __('forms.not_verified') ';--}}

                                                   return s;
                                               }).join(', ') : ''"
                                               readonly
                                        />
                                        <svg
                                            class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 pointer-events-none"
                                            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                        <div x-show="open"
                                             x-on:click.away="open = false"
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             class="absolute z-10 mt-2 w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md shadow-lg">
                                            <ul class="py-2 px-3 space-y-2 text-sm text-gray-700 dark:text-gray-200">
                                                <li>
                                                    <label class="flex items-center space-x-2 cursor-pointer">
                                                        <input type="checkbox" value="APPROVED" wire:model="status"
                                                               class="rounded-sm text-blue-600 focus:ring-blue-500 border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-600 dark:checked:border-transparent" />
                                                        <span>{{ __('forms.active') }}</span>
                                                    </label>
                                                </li>
                                                <li>
                                                    <label class="flex items-center space-x-2 cursor-pointer">
                                                        <input type="checkbox" value="NEW" wire:model="status"
                                                               class="rounded-sm text-blue-600 focus:ring-blue-500 border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-600 dark:checked:border-transparent" />
                                                        <span>{{ __('forms.draft') }}</span>
                                                    </label>
                                                </li>

                                                <li>
                                                    <label class="flex items-center space-x-2 cursor-pointer">
                                                        <input type="checkbox" value="SIGNED" wire:model="status"
                                                               class="rounded-sm text-blue-600 focus:ring-blue-500 border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-600 dark:checked:border-transparent" />
                                                        <span>{{ __('forms.status.sent') }}</span>
                                                    </label>
                                                </li>

                                                <li>
                                                    <label class="flex items-center space-x-2 cursor-pointer">
                                                        <input type="checkbox" value="DISMISSED" wire:model="status"
                                                               class="rounded-sm text-blue-600 focus:ring-blue-500 border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:checked:bg-blue-600 dark:checked:border-transparent" />
                                                        <span>{{ __('forms.dismissed') }}</span>
                                                    </label>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-9 mt-6 flex flex-col sm:flex-row gap-2 w-full">
                            <button type="submit" class="flex items-center gap-2 button-primary">
                                @icon('search', 'w-4 h-4')
                                <span>{{ __('forms.search') }}</span>
                            </button>

                            <button type="button" wire:click="resetFilters" class="button-primary-outline-red">
                                {{ __('forms.reset_all_filters') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </x-slot>
    </x-header-navigation>

    <x-section class="shift-content pl-3.5">
        <div class="space-y-6 employee-section-no-left-padding mt-6">
            <div class="table-container-responsive overflow-x-auto" style="max-width:100%;" wire:key="{{ $filterKey }}">
                @forelse($parties as $party)
                    @php
                        // Filter requests: exclude those that are already APPROVED and have an applied_at date.
                        // We use strict filtering on the collection to avoid showing historical processed requests.
                        $drafts = $party->employeeRequests->reject(function ($request) {
                            $status = $request->status instanceof \UnitEnum ? $request->status->value : $request->status;
                            return $status === 'APPROVED';
                        });

                        $employees = $party->employees;

                        $positions = $drafts->merge($employees)->sortByDesc('updated_at');

                        // Check permissions for actions.
                        // We iterate through the filtered list of positions.
                        $hasAnyActionInTable = $positions->contains(function ($pos) use ($permissions) {
                            $isEmp = $pos instanceof \App\Models\Employee\Employee;
                            // Safe access to status value handling both Enum objects and strings
                            $status = $pos->status instanceof \UnitEnum ? $pos->status->value : $pos->status;

                            if ($isEmp) {
                                return $permissions['employee_view'] ||
                                       $permissions['employee_write'] ||
                                       ($status === 'APPROVED' && $permissions['employee_deactivate']);
                            }

                            // Request checks
                            $isProcessed = !empty($pos->uuid);
                            return $permissions['request_view'] ||
                                   (!$isProcessed && $permissions['request_write']);
                        });
                    @endphp

                    <fieldset
                        class="p-4 sm:p-8 sm:pb-10 mb-16 mt-6 border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 max-w-[1280px]"
                        wire:key="party-{{ $party->id }}">
                        <legend class="legend">{{ $party->fullName }}</legend>

                        <div
                            class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 dark:border-gray-700 pb-4">

                            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-500 mt-2"
                                 x-data="{ showEmails_{{ $party->id }}: false }">

                                {{-- Phone --}}
                                @if ($mobilePhone = $party->phones->firstWhere('type', 'MOBILE'))
                                    <span class="flex items-center gap-1.5 min-w-0">
                                        <svg class="w-5 h-5 text-gray-500" aria-hidden="true"
                                             xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                             viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round"
                                                                       stroke-linejoin="round" stroke-width="2"
                                                                       d="M18.427 14.768 17.2 13.542a1.733 1.733 0 0 0-2.45 0l-.613.613a1.732 1.732 0 0 1-2.45 0l-1.838-1.84a1.735 1.735 0 0 1 0-2.452l.612-.613a1.735 1.735 0 0 0 0-2.452L9.237 5.572a1.6 1.6 0 0 0-2.45 0c-3.223 3.2-1.702 6.896 1.519 10.117 3.22 3.221 6.914 4.745 10.12 1.535a1.601 1.601 0 0 0 0-2.456Z" /></svg>
                                        <a href="tel:{{ $mobilePhone->number }}" class="truncate hover:underline"
                                           title="{{ $mobilePhone->number }}">{{ $mobilePhone->number }}</a>
                                    </span>
                                @endif

                                {{-- Email --}}
                                @php

                                    $emailsCollection = $party->loadMissing('users')->employees
                                        ->where('legal_entity_id', $currentLegalEntityId)
                                        ->map(fn($emp) => $emp->loadMissing('party.users')->party->users?->map(fn($user) => $user->email))
                                        ->flatten()
                                        ->filter()
                                        ->unique();

                                    $visibleEmail = $emailsCollection->first();
                                    $hiddenEmails = $emailsCollection->slice(1);
                                    $hiddenCount = $hiddenEmails->count();
                                @endphp

                                @if ($visibleEmail)
                                    <span class="flex items-center gap-1.5 min-w-0 relative">
                                        <svg class="w-6 h-6 text-gray-800 dark:text-white shrink-0" aria-hidden="true"
                                             xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                             viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                                  d="m3.5 5.5 7.893 6.036a1 1 0 0 0 1.214 0L20.5 5.5M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z" />
                                        </svg>
                                        <a href="mailto:{{ $visibleEmail }}" class="hover:underline truncate"
                                           title="{{ $visibleEmail }}">{{ $visibleEmail }}</a>

                                        @if ($hiddenCount > 0)
                                            <button type="button"
                                                    @click.stop="showEmails_{{ $party->id }} = !showEmails_{{ $party->id }}"
                                                    class="text-sm font-semibold text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer p-0.5 rounded-full"
                                                    title="Показати {{ $hiddenCount }} додаткових email"
                                            >
                                                +{{ $hiddenCount }}
                                            </button>
                                        @endif

                                        @if ($hiddenCount > 0)
                                            <div x-show="showEmails_{{ $party->id }}"
                                                 x-on:click.away="showEmails_{{ $party->id }} = false"
                                                 x-collapse.duration.300ms
                                                 class="flex flex-col gap-y-0.5 absolute bg-white dark:bg-gray-800 z-10 p-2 rounded-md shadow-lg top-full left-0 mt-1 min-w-max border border-gray-200 dark:border-gray-700"
                                                 x-cloak
                                            >
                                                @foreach ($hiddenEmails as $email)
                                                    <a href="mailto:{{ $email }}"
                                                       class="hover:underline text-gray-500 dark:text-gray-400 text-sm">{{ $email }}</a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-4">
                                @if($party->employees->isNotEmpty())
                                    @php
                                        // Find the last active employee of this person to check the rights
                                        $latestEmployee = $party->employees->first(); // or through the method by which you get a topical position

                                        $isOwner = $latestEmployee && $latestEmployee->employeeType === Role::OWNER->value;
                                        $hasUserLinked = $latestEmployee && !empty($latestEmployee->userId);

                                        // We check the possibility of editing personal data according to your rules:
                                        // 1. Not the owner 2. There is a tethered user 3. Not exempt
                                        $canEditParty = $latestEmployee
                                            && !$isOwner
                                            && $hasUserLinked
                                            && $latestEmployee->status !== \App\Enums\Status::DISMISSED;
                                    @endphp
                                    @can('create', \App\Models\Employee\EmployeeRequest::class)
                                        {{-- Edit personal data button --}}
                                        @if($canEditParty)
                                            <a href="{{ route('party.edit', ['legalEntity' => $currentLegalEntityId, 'party' => $party->id]) }}"
                                               class="cursor-pointer text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                                @icon('file-lines', 'w-4 h-4 text-blue-600 hover:text-blue-800')
                                                <span class="text-sm">{{ __('forms.edit_personal_data') }}</span>
                                            </a>
                                        @endif

                                        {{-- Add position button (if you want to restrict for owners too) --}}

                                        <a href="{{ route('employee-request.position-add', ['legalEntity' => $currentLegalEntityId, 'party' => $party->id]) }}"
                                           class="item-add text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                            <span class="text-xl leading-none">+</span>
                                            <span>{{ __('forms.add_position') }}</span>
                                        </a>
                                    @endif
                                @endcan
                            </div>
                        </div>

                        <div class="flow-root mt-4">
                            <div class="max-w-screen-xl">
                                <table class="table-input w-full table-fixed min-w-[600px] text-sm">
                                    <thead class="thead-input">
                                    <tr>
                                        <th scope="col" class="th-input w-[25%]">{{ __('forms.position') }}</th>
                                        <th scope="col" class="th-input w-[29%]">{{ __('forms.role') }}</th>
                                        <th scope="col" class="th-input w-[15%]">{{ __('forms.division') }}</th>
                                        <th scope="col" class="th-input w-[24%]">{{ __('forms.email') }}</th>
                                        <th scope="col" class="th-input w-[10%]">{{ __('forms.status.label') }}</th>
                                        @if($hasAnyActionInTable)
                                            <th scope="col" class="th-input w-[7%] text-center">
                                                {{ __('forms.actions') }}
                                            </th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($positions as $position)
                                        @php
                                            $positionEmail = null;
                                            if ($position instanceof \App\Models\Employee\Employee) {
                                                $positionEmail = $position->loadMissing('party.users')->party->users()->first()?->email ?? null;
                                            } else if ($position instanceof \App\Models\Employee\EmployeeRequest) {
                                                $positionEmail = $position->revision->data['party']['email'] ?? null;
                                            }
                                        @endphp
                                        <tr>
                                            <td class="td-input break-words whitespace-normal align-top">
                                                {{ $dictionaries['POSITION'][$position->position] ?? $position->position }}
                                            </td>
                                            <td class="td-input break-words whitespace-normal align-top">
                                                {{ $dictionaries['EMPLOYEE_TYPE'][$position->employee_type] ?? $position->employee_type }}
                                            </td>
                                            <td class="td-input break-words whitespace-normal align-top">
                                                {{ $position->division->name ?? 'N/A' }}
                                            </td>

                                            <td class="td-input break-words whitespace-normal align-top">
                                                @if($positionEmail)
                                                    <a href="mailto:{{ $positionEmail }}" class="hover:underline"
                                                       title="{{ $positionEmail }}">{{ $positionEmail }}</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>

                                            <td class="td-input break-words whitespace-nowrap align-middle">
                                                @php $isEmployee = $position instanceof \App\Models\Employee\Employee; @endphp
                                                @if($isEmployee)
                                                    @if($position->status?->value === 'APPROVED')
                                                        <span class="badge-green">{{__('forms.status.active')}}</span>
                                                    @else
                                                        <span class="badge-red">{{__('forms.status.dismissed')}}</span>
                                                    @endif
                                                @else
                                                    @if($position->status?->value === 'NEW')
                                                        <span class="badge-red">{{__('forms.status.draft')}}</span>
                                                    @elseif($position->status?->value === 'SIGNED')
                                                        <span class="badge-yellow">{{__('forms.status.sent')}}</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="td-input text-center">
                                                @if($position)
                                                    @include('livewire.employee.parts.actions-dropdown', ['position' => $position])
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                @empty
                    <fieldset class="fieldset mx-auto shift-content">
                        <legend class="legend relative -top-5">@icon('nothing-found', 'w-28 h-28')</legend>
                        <div class="p-4 rounded-lg bg-blue-100 flex items-start mb-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-0.5">
                                    @icon('alert-circle', 'w-5 h-5 text-blue-500 mr-3 mt-1')
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-blue-800">
                                        {{ __('forms.nothing_found') }}
                                    </p>
                                    <p class="text-sm text-blue-600">
                                        {{ __('forms.changing_search_parameters') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                @endforelse
            </div>
        </div>

        <div class="mt-8 pl-3.5 pb-8 lg:pl-8 2xl:pl-5" wire:key="pagination-{{ $filterKey }}">
            {{ $parties->links() }}
        </div>
    </x-section>

    @include('livewire.employee.parts.modals.deactivate-modal')
    @include('livewire.employee.parts.modals.delete-draft-modal')
</div>
