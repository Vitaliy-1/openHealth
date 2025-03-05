@php
    $svgSprite = file_get_contents(resource_path('images/sprite.svg'));
    $tableHeaders = [
        __('forms.full_name'),
        __('forms.phone'),
        __('Д.Н.'),
        __('forms.rnokpp') . '(' . __('forms.ipn') . ')',
        __('forms.birth_certificate'),
        __('forms.status'),
        __('forms.action')
    ];
@endphp

<div>
    <div aria-hidden="true" class="hidden">
        {!! $svgSprite !!}
    </div>

    <section>
        <x-section-navigation x-data="{ showFilter: true }" class="breadcrumb-form">
            <x-slot name="title">{{ __('patients.patients') }}</x-slot>
            <x-slot name="navigation">

                <div class="justify-end block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700 mb-8">
                    <div class="button-group">
                        <button type="button" class="default-button">
                            <a href="{{ route('patient.form') }}">
                                {{ __('patients.add_patient') }}
                            </a>
                        </button>
                        <button class="button-sync">
                            {{ __('forms.synchronise_with_eHealth') }}
                        </button>
                    </div>
                </div>

                <div class="mb-8 flex items-center gap-1 font-semibold text-gray-900 dark:text-white">
                    <svg width="18" height="18">
                        <use xlink:href="#svg-search-outline"></use>
                    </svg>
                    <p>{{ __('patients.patient_search') }}</p>
                </div>

                @include('livewire.patient._parts._search_filter')
                <div class="py-4">
                    <button wire:click.prevent="searchForPerson('patientsFilter')"
                            class="flex items-center gap-2 default-button"
                    >
                        <svg width="16" height="16">
                            <use xlink:href="#svg-search"></use>
                        </svg>
                        <span>{{ __('patients.search') }}</span>
                    </button>
                </div>
            </x-slot>
        </x-section-navigation>

        @if($paginatedPatients && count($paginatedPatients) > 0)
            <div class="table-section"
                 x-data="{
                         activeFilter: 'all',
                         patients: {{ json_encode($paginatedPatients->items()) }},

                         filteredPatients() {
                             if (this.activeFilter === 'all') return this.patients;
                             return this.patients.filter(patient => patient.status === this.activeFilter);
                         },

                         init() {
                              Livewire.on('patientsUpdated', (updatedPatients) => {
                                  this.patients = updatedPatients[0];
                              });

                             Livewire.on('patientRemoved', (id) => {
                                 this.patients = this.patients.filter(patient => patient.id !== id[0]);
                             });
                         }
                     }"
            >
                <div class="mb-6 flex items-center gap-8">
                    <button @click="activeFilter = 'all'"
                            :class="activeFilter === 'all' ? 'default-button' : 'light-button'"
                    >
                        {{ __('patients.all') }}
                    </button>
                    <button @click="activeFilter = 'eHEALTH'"
                            :class="activeFilter === 'eHEALTH' ? 'default-button' : 'light-button'"
                    >
                        {{ __('patients.patients') }}
                    </button>
                    <button @click="activeFilter = 'APPLICATION'"
                            :class="activeFilter === 'APPLICATION' ? 'default-button' : 'light-button'"
                    >
                        {{ __('patients.applications') }}
                    </button>
                </div>
                <div class="table-container">
                    <div class="overflow-x-auto">
                        <table class="table-base">
                            <thead class="table-header">
                            <tr>
                                @foreach($tableHeaders as $tableHeader)
                                    <th wire:key="{{ $loop->index }}" scope="col" class="px-4 py-3">
                                        {{ $tableHeader }}
                                    </th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            <template x-for="patient in filteredPatients()" :key="patient.id">
                                <tr class="border-b dark:border-gray-700">
                                    <th scope="row" class="table-cell-primary">
                                        <div
                                            x-text="`${patient.last_name} ${patient.first_name} ${patient.second_name || ''}`"></div>
                                        <template x-if="patient.status === 'APPLICATION'">
                                            <div class="flex gap-2 mt-2">
                                                <a :href="`{{ route('patient.form', ['id' => '']) }}/${patient.id}`"
                                                   class="default-button">
                                                    {{ __('patients.continue_registration') }}
                                                </a>
                                            </div>
                                        </template>
                                        <template x-if="patient.status !== 'APPLICATION'">
                                            <div class="flex gap-2 mt-2">
                                                <button @click.prevent="$wire.redirectToPatient(patient)"
                                                        type="button"
                                                        class="default-button"
                                                >
                                                    {{ __('patients.view_record') }}
                                                </button>
                                                <button @click.prevent="$wire.redirectToEncounter(patient)"
                                                        class="button-sync"
                                                >
                                                    <svg width="16" height="16">
                                                        <use xlink:href="#svg-plus"></use>
                                                    </svg>
                                                    {{ __('patients.start_interacting') }}
                                                </button>
                                            </div>
                                        </template>
                                    </th>
                                    <td class="px-4 py-3" x-text="patient.phones?.[0]?.number || '-'"></td>
                                    <td class="px-4 py-3" x-text="patient.birth_date"></td>
                                    <td class="px-4 py-3" x-text="patient.tax_id || '-'"></td>
                                    <td class="px-4 py-3" x-text="patient.birth_certificate || '-'"></td>
                                    <td class="px-4 py-3">
                                        <span x-text="
                                                  patient.status === 'APPLICATION' ? 'ЗАЯВКА' :
                                                  patient.status === 'eHEALTH' ? 'ЕСОЗ' :
                                                  patient.status === 'IN_REVIEW' ? 'ОБРОБЛЯЄТЬСЯ' :
                                                  '-'
                                              "
                                              :class="{
                                                  'badge-purple': patient.status === 'APPLICATION',
                                                  'badge-green': patient.status === 'eHEALTH',
                                                  'badge-yellow': patient.status === 'IN_REVIEW'
                                              }"
                                        ></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div x-data="{ open: false }">
                                            <button @click="if (patient.status === 'APPLICATION') open = !open"
                                                    class="dropdown-button"
                                                    type="button"
                                            >
                                                <svg width="24" height="25">
                                                    <use xlink:href="#svg-edit-user-outline"></use>
                                                </svg>
                                            </button>
                                            <div x-show="open"
                                                 @click.away="open = false"
                                                 class="dropdown-menu absolute right-0 mt-3"
                                            >
                                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                                    <li>
                                                        <a @click.prevent="$wire.removeApplication(patient.id)"
                                                           href="#"
                                                           class="dropdown-item-with-icon"
                                                        >
                                                            <svg width="18" height="19">
                                                                <use xlink:href="#svg-edit"></use>
                                                            </svg>
                                                            {{ __('forms.delete') }}
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>
                    <nav class="table-nav" aria-label="Table navigation">
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                            {{ __('Показано') }}
                            <span class="table-nav-number">
                                {{ $paginatedPatients->firstItem() }}-{{ $paginatedPatients->lastItem() }}
                            </span>
                            {{ __('з') }}
                            <span class="table-nav-number">
                                {{ $paginatedPatients->total() }}
                            </span>
                        </span>
                        <ul class="pagination-list">
                            {{-- Previous page --}}
                            <li>
                                <a href="{{ $paginatedPatients->previousPageUrl() }}"
                                   class="pagination-prev-button">
                                    <span class="sr-only">{{ __('forms.previous') }}</span>
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                              d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                              clip-rule="evenodd"
                                        />
                                    </svg>
                                </a>
                            </li>
                            {{-- Page numbers --}}
                            @foreach ($paginatedPatients->getUrlRange(max(1, $paginatedPatients->currentPage() - 2), min($paginatedPatients->lastPage(), $paginatedPatients->currentPage() + 2)) as $page => $url)
                                <li>
                                    <a href="{{ 'patient' . $url }}"
                                       {{ $paginatedPatients->currentPage() === $page ? 'aria-current="page"' : '' }}
                                       class="pagination-number {{ $paginatedPatients->currentPage() === $page ? 'pagination-number-active' : 'pagination-number-inactive' }}"
                                    >
                                        {{ $page }}
                                    </a>
                                </li>
                            @endforeach
                            <li>
                                <a href="{{ $paginatedPatients->nextPageUrl() }}"
                                   class="pagination-next-button">
                                    <span class="sr-only">{{ __('forms.next') }}</span>
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                              d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                              clip-rule="evenodd"
                                        />
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        @elseif($searchPerformed && $paginatedPatients->isEmpty())
            <div class="rounded-lg p-4 bg-gray-200 dark:bg-gray-900">
                <div class="flex items-center gap-2">
                    <svg width="20" height="20">
                        <use xlink:href="#svg-exclamation-circle"></use>
                    </svg>
                    <p class="default-p font-semibold">{{ __('patients.nobody_found') }}</p>
                </div>
                <p class="default-p">{{ __('patients.try_change_search_parameters') }}</p>
            </div>
        @endif
    </section>

    <x-forms.loading/>
</div>
