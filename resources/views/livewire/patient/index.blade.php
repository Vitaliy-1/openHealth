@php
    $svgSprite = file_get_contents(resource_path('images/sprite.svg'));
    $tableHeaders = [
            __('forms.full_name'),
            __('forms.phone'),
            __('Д.Н.'),
            __('forms.RNOCPP') . '(' . __('forms.ipn') . ')',
            __('forms.birthCertificate'),
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
                        <button type="button"
                                class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                            {{ __('Синхронізувати з ЕСОЗ') }}
                        </button>
                    </div>
                </div>

                <div class="mb-8 flex items-center gap-1 font-semibold text-gray-900">
                    <svg width="17" height="17">
                        <use xlink:href="#svg-search-outline"></use>
                    </svg>
                    <p>{{ __('Пошук пацієнта') }}</p>
                </div>

                @include('livewire.patient._parts._search_filter')
                <x-forms.form-group class="py-4">
                    <x-slot name="label">
                        <x-forms.button-with-icon wire:click.prevent="searchForPerson('patientsFilter')"
                                                  class="default-button"
                                                  label="{{ __('Шукати') }}"
                                                  svgId="svg-search"
                        />
                    </x-slot>
                </x-forms.form-group>
            </x-slot>
        </x-section-navigation>

        @if($paginatedPatients && count($paginatedPatients) > 0)
            <section class="table-section"
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
                <div class="mb-6 flex items-center gap-7">
                    <button @click="activeFilter = 'all'"
                            :class="activeFilter === 'all' ? 'default-button' : 'light-button'">
                        {{ __('Всі') }}
                    </button>
                    <button @click="activeFilter = 'eHEALTH'"
                            :class="activeFilter === 'eHEALTH' ? 'default-button' : 'light-button'">
                        {{ __('Пацієнти') }}
                    </button>
                    <button @click="activeFilter = 'APPLICATION'"
                            :class="activeFilter === 'APPLICATION' ? 'default-button' : 'light-button'">
                        {{ __('Заявки') }}
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
                                                    {{ __('Продовжити реєстрацію') }}
                                                </a>
                                            </div>
                                        </template>
                                        <template x-if="patient.status !== 'APPLICATION'">
                                            <div class="flex gap-2 mt-2">
                                                <button @click.prevent="$wire.redirectToPatient(patient)"
                                                        type="button"
                                                        class="default-button"
                                                >
                                                    {{ __('Переглянути карту') }}
                                                </button>
                                                <button type="button"
                                                        class="flex items-center gap-2 focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
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
                                                            {{ __('Видалити') }}
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
                                    <span class="sr-only">{{ __('Попередня') }}</span>
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                              d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            </li>
                            {{-- Page numbers --}}
                            @foreach ($paginatedPatients->getUrlRange(max(1, $paginatedPatients->currentPage() - 2), min($paginatedPatients->lastPage(), $paginatedPatients->currentPage() + 2)) as $page => $url)
                                <li>
                                    <a href="{{ 'patient' . $url }}"
                                       {{ $paginatedPatients->currentPage() === $page ? 'aria-current="page"' : '' }}
                                       class="pagination-number {{ $paginatedPatients->currentPage() === $page ? 'pagination-number-active' : 'pagination-number-inactive' }}">
                                        {{ $page }}
                                    </a>
                                </li>
                            @endforeach
                            <li>
                                <a href="{{ $paginatedPatients->nextPageUrl() }}"
                                   class="pagination-next-button">
                                    <span class="sr-only">{{ __('Наступна') }}</span>
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                              d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </section>
        @elseif($searchPerformed && $paginatedPatients->isEmpty())
            <div class="rounded-lg p-4 bg-gray-100">
                <div class="flex items-center gap-2">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M9 6V9M9 12H9.0075M16.5 9C16.5 13.1421 13.1421 16.5 9 16.5C4.85786 16.5 1.5 13.1421 1.5 9C1.5 4.85786 4.85786 1.5 9 1.5C13.1421 1.5 16.5 4.85786 16.5 9Z"
                            stroke="#1E1E1E" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"/>
                    </svg>
                    <p class="font-semibold text-gray-900">{{ __('Нікого не знайдено') }}</p>
                </div>
                <span class="text-gray-900">{{ __('Спробуйте змінити параметри пошуку') }}</span>
            </div>
        @endif
    </section>
</div>
