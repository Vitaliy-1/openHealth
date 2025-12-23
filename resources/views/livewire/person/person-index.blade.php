@php
    use App\Models\MedicalEvents\Sql\{DiagnosticReport, Encounter, Procedure};
    use App\Models\DeclarationRequest;
    use App\Models\Person\{Person, PersonRequest};
    use App\Enums\Person\{VerificationStatus, Status, Gender};
@endphp

<div>
    <section>
        <x-header-navigation x-data="{ showFilter: true }" class="breadcrumb-form">
            <x-slot name="title">{{ __('patients.patients') }}</x-slot>
            <x-slot name="navigation">

                <div class="justify-end block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700 mb-4">
                    @can('create', PersonRequest::class)
                        <a href="{{ route('persons.create', [legalEntity()]) }}"
                           class="button-primary flex items-center gap-2"
                        >
                            @icon('plus', 'w-4 h-4')
                            {{ __('patients.add_patient') }}
                        </a>
                    @endcan
                </div>

                <div class="mb-4 flex items-center gap-1 font-semibold text-gray-900 dark:text-white">
                    @icon('search-outline', 'w-4.5 h-4.5')
                    <p>{{ __('patients.patient_search') }}</p>
                </div>

                @include('livewire.person.parts.search-filter', ['context' => 'index'])

                <div class="mb-4 mt-6 flex gap-2">
                    @can('viewAny', Person::class)
                        <button wire:click.prevent="searchForPerson" class="flex items-center gap-2 button-primary">
                            @icon('search', 'w-4 h-4')
                            <span>{{ __('patients.search') }}</span>
                        </button>
                    @endcan
                    <button type="button" wire:click="resetFilters" class="button-primary-outline-red">
                        {{ __('forms.reset_all_filters') }}
                    </button>
                </div>
            </x-slot>
        </x-header-navigation>

        <div class="space-y-6 shift-content pl-3.5" wire:key="patients-{{ $paginatedPatients->total() }}">
            @forelse($paginatedPatients->items() as $patient)
                <fieldset wire:key="patient-{{ $patient['id'] }}"
                          class="p-4 sm:p-8 sm:pb-10 mb-16 mt-6 border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 max-w-[1280px]"
                >
                    <legend class="legend">
                        {{ $patient['lastName'] }} {{ $patient['firstName'] }} {{ $patient['secondName'] ?? '' }}
                    </legend>

                    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 dark:border-gray-700 pb-4">
                        <div class="flex items-center flex-wrap gap-x-6 gap-y-2 text-sm text-gray-500 mt-2">

                            @if($patient['birthDate'])
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                         xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                         viewBox="0 0 24 24">
                                           <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                                 d="M8 4H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H8z" />
                                           <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                                 d="M16 2v4M8 2v4M3 10h18" />
                                        </svg>
                                    <span>{{ $patient['birthDate'] }}</span>
                                </span>
                            @endif

                            @if(isset($patient['phones'][0]['number']))
                                <span class="flex items-center gap-1.5 min-w-0">
                                    @icon('tabler-phone', 'w-6 h-6 text-gray-800 dark:text-white')
                                    <a href="tel:{{ $patient['phones'][0]['number'] }}"
                                       class="truncate hover:underline font-medium text-gray-900 dark:text-gray-200 text-base"
                                       title="{{ $patient['phones'][0]['number'] }}"
                                    >
                                        {{ $patient['phones'][0]['number'] }}
                                    </a>
                                </span>
                            @endif

                            @if(isset($patient['gender']))
                                <span class="flex items-center gap-1.5">
                                    @if($patient['gender'] === Gender::MALE->value)
                                        @icon('men', 'w-6 h-6 text-gray-800 dark:text-white')
                                        <span>{{ __('patients.male') }}</span>
                                    @elseif($patient['gender'] === Gender::FEMALE->value)
                                        @icon('women', 'w-6 h-6 text-gray-800 dark:text-white')
                                        <span>{{ __('patients.female') }}</span>
                                    @endif
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center space-x-6">
                            @if($patient['source'] === 'request')
                                <a href="{{ route('persons.edit', [legalEntity(), $patient['id']]) }}"
                                   class="cursor-pointer text-blue-600 hover:text-blue-800 flex items-center gap-1.5 font-medium"
                                >
                                    @icon('file-lines', 'w-4 h-4')
                                    <span class="text-sm">{{ __('patients.continue_registration') }}</span>
                                </a>
                            @else
                                @can('view', Person::class)
                                    <button wire:click="redirectTo('{{ $patient['id'] }}', 'persons.patient-data')"
                                            class="cursor-pointer text-blue-600 hover:text-blue-800 flex items-center gap-1.5 font-medium"
                                    >
                                        @icon('file-lines', 'w-4 h-4')
                                        <span class="text-sm">{{ __('patients.view_record') }}</span>
                                    </button>
                                @endcan
                                @can('create', Encounter::class)
                                    <button wire:click="redirectTo('{{ $patient['id'] }}', 'encounter.create')"
                                            class="cursor-pointer text-blue-600 hover:text-blue-800 flex items-center gap-1.5 font-medium"
                                    >
                                        @icon('plus', 'w-4 h-4')
                                        <span class="text-sm">+ {{ __('patients.start_interacting') }}</span>
                                    </button>
                                @endcan
                            @endif
                        </div>
                    </div>

                    <div class="flow-root mt-4">
                        <div class="max-w-screen-xl">
                            <table class="table-input w-full table-auto">
                                <thead class="thead-input">
                                <tr>
                                    <th scope="col" class="th-input">{{ __('forms.city') ?? 'МІСТО' }}</th>
                                    <th scope="col" class="th-input">{{ __('forms.rnokpp') }} (ІПН)</th>
                                    <th scope="col" class="th-input">{{ __('patients.birth_certificate') }}</th>
                                    <th scope="col" class="th-input">{{ __('forms.status.label') }}</th>
                                    <th scope="col" class="th-input text-center">{{ __('forms.actions') }}</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr>
                                    <td class="td-input whitespace-nowrap overflow-hidden text-ellipsis align-top font-bold text-gray-900 dark:text-white">
                                        {{ $patient['address']['city'] ?? $patient['city'] ?? '-' }}
                                    </td>
                                    <td class="td-input whitespace-nowrap overflow-hidden text-ellipsis align-top font-bold text-gray-900 dark:text-white">
                                        {{ $patient['taxId'] ?? '-' }}
                                    </td>
                                    <td class="td-input whitespace-nowrap overflow-hidden text-ellipsis align-top font-bold text-gray-900 dark:text-white">
                                        {{ $patient['birthCertificate'] ?? '-' }}
                                    </td>
                                    <td class="td-input whitespace-nowrap align-top">
                                        @php
                                            if ($patient['source'] === 'request') {
                                                $color = Status::from($patient['status'])->color();
                                                $label = Status::from($patient['status'])->label();
                                            } elseif($patient['source'] === 'local') {
                                                $color = VerificationStatus::from($patient['verificationStatus'])->color();
                                                $label = VerificationStatus::from($patient['verificationStatus'])->label();
                                            } elseif($patient['source'] === 'ehealth') {
                                                $color = 'badge-green';
                                                $label = __('patients.source.ehealth');
                                            }
                                        @endphp

                                        <span class="{{ $color }} px-2 py-0.5 rounded text-xs">{{ $label }}</span>
                                    </td>
                                    <td class="td-input text-center">
                                        <div class="relative"
                                             x-data="{ openDropdown: false }"
                                             @click.outside="openDropdown = false"
                                        >
                                            <button @click="openDropdown = !openDropdown"
                                                    type="button"
                                                    class="cursor-pointer p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                            >
                                                @icon('edit-user-outline', 'w-6 h-6 text-gray-800 dark:text-gray-200')
                                            </button>

                                            <div x-show="openDropdown"
                                                 x-transition
                                                 x-cloak
                                                 class="absolute right-0 z-10 w-56 bg-white rounded shadow-lg border border-gray-200 dark:bg-gray-700 dark:border-gray-600"
                                            >
                                                @if($patient['source'] === 'request')
                                                    <div class="py-1" @click="openDropdown = false">
                                                        <button wire:click="deleteDraft({{ $patient['id'] }})"
                                                                class="dropdown-button !flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                                                                type="button"
                                                        >
                                                            @icon('delete', 'w-5 h-5')
                                                            {{ __('forms.delete') }}
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="py-1">
                                                        @can('create', DeclarationRequest::class)
                                                            <a wire:click="redirectTo('{{ $patient['id'] }}', 'declaration.create')"
                                                               class="dropdown-button !flex items-center gap-2 px-4 py-2 text-sm border-b border-gray-100 dark:border-gray-600 w-full hover:bg-gray-50 cursor-pointer text-left"
                                                               @click="openDropdown = false"
                                                            >
                                                                @icon('file-text', 'w-4 h-4')
                                                                {{ __('patients.sign_declaration') }}
                                                            </a>
                                                        @endcan

                                                        @can('create', DiagnosticReport::class)
                                                            <a wire:click="redirectTo('{{ $patient['id'] }}', 'diagnostic-report.create')"
                                                               class="dropdown-button !flex items-center gap-2 px-4 py-2 text-sm border-b border-gray-100 dark:border-gray-600 w-full hover:bg-gray-50 cursor-pointer text-left"
                                                               @click="openDropdown = false"
                                                            >
                                                                @icon('activity', 'w-4 h-4')
                                                                {{ __('patients.create_diagnostic_report') }}
                                                            </a>
                                                        @endcan

                                                        @can('create', Procedure::class)
                                                            <a wire:click="redirectTo('{{ $patient['id'] }}', 'procedure.create')"
                                                               class="dropdown-button !flex items-center gap-2 px-4 py-2 text-sm w-full hover:bg-gray-50 cursor-pointer text-left"
                                                               @click="openDropdown = false"
                                                            >
                                                                @icon('settings', 'w-4 h-4')
                                                                {{ __('patients.create_procedure') }}
                                                            </a>
                                                        @endcan
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </fieldset>

            @empty
                <fieldset class="fieldset max-w-[1120px]">
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

        <div class="mt-8">
            {{ $paginatedPatients->links() }}
        </div>
    </section>

    <x-forms.loading />
    <x-messages />
</div>
