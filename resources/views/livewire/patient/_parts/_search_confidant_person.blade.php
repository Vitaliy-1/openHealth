@php use Carbon\Carbon; @endphp

<div x-data="{ showFilter: true }" x-on:patient-selected.window="showFilter = false">
    <x-section-navigation class="!p-0">
        <x-slot name="navigation">
            <!-- Search input fields -->
            @include('livewire.patient.patients-filter')
        </x-slot>
    </x-section-navigation>

    <!-- Patient list -->
    @if($patients && count($patients) > 0)
        <div class="flex flex-col h-auto my-6">
            <div class="inline-block w-[70%] align-middle">
                <div class="overflow-hidden shadow">
                    <x-tables.table align="left">
                        <x-slot name="headers" :list="$tableHeaders"></x-slot>
                        <x-slot name="tbody">
                            @foreach($patients as $patient)
                                <tr>
                                    <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                        <p class="text-base text-gray-900 dark:text-white">
                                            {{ $patient['first_name'] }} {{ $patient['last_name'] }} {{ $patient['second_name'] }}
                                        </p>
                                    </td>
                                    <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                        <p class="text-base text-gray-500 dark:text-white">
                                            {{ $patient['phones'][0]['number'] ?? '-' }}
                                        </p>
                                    </td>
                                    <td class="p-4 text-sm font-semibold text-gray-900 whitespace-nowrap dark:text-gray-400">
                                        <p class="text-base dark:text-white">
                                            {{ Carbon::parse($patient['birth_date'])->format('j.m.Y') }}
                                        </p>
                                    </td>
                                    <td class="p-4 text-sm font-semibold text-gray-500 whitespace-nowrap dark:text-gray-400">
                                        <p class="text-base dark:text-white">
                                            {{ $patient['tax_id'] ?? '-' }}
                                        </p>
                                    </td>
                                    <td>
                                        <a type="button" href="#" class="flex items-center gap-1">
                                            @if($selectedPatientId === $patient['id'])
                                                <svg width="14" height="15" viewBox="0 0 14 15" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M11.6667 4.12283H9.33333V2.89476C9.33333 2.56905 9.21042 2.25669 8.99162 2.02638C8.77283 1.79607 8.47609 1.66669 8.16667 1.66669H5.83333C5.52391 1.66669 5.22717 1.79607 5.00838 2.02638C4.78958 2.25669 4.66667 2.56905 4.66667 2.89476V4.12283H2.33333C2.17862 4.12283 2.03025 4.18752 1.92085 4.30267C1.81146 4.41783 1.75 4.57401 1.75 4.73686C1.75 4.89971 1.81146 5.0559 1.92085 5.17105C2.03025 5.2862 2.17862 5.3509 2.33333 5.3509H2.91667V12.1053C2.91667 12.431 3.03958 12.7434 3.25838 12.9737C3.47717 13.204 3.77391 13.3334 4.08333 13.3334H9.91667C10.2261 13.3334 10.5228 13.204 10.7416 12.9737C10.9604 12.7434 11.0833 12.431 11.0833 12.1053V5.3509H11.6667C11.8214 5.3509 11.9697 5.2862 12.0791 5.17105C12.1885 5.0559 12.25 4.89971 12.25 4.73686C12.25 4.57401 12.1885 4.41783 12.0791 4.30267C11.9697 4.18752 11.8214 4.12283 11.6667 4.12283ZM5.83333 2.89476H8.16667V4.12283H5.83333V2.89476ZM6.41667 10.8772C6.41667 11.0401 6.35521 11.1962 6.24581 11.3114C6.13642 11.4266 5.98804 11.4912 5.83333 11.4912C5.67862 11.4912 5.53025 11.4266 5.42085 11.3114C5.31146 11.1962 5.25 11.0401 5.25 10.8772V6.57897C5.25 6.41612 5.31146 6.25993 5.42085 6.14478C5.53025 6.02963 5.67862 5.96493 5.83333 5.96493C5.98804 5.96493 6.13642 6.02963 6.24581 6.14478C6.35521 6.25993 6.41667 6.41612 6.41667 6.57897V10.8772ZM8.75 10.8772C8.75 11.0401 8.68854 11.1962 8.57915 11.3114C8.46975 11.4266 8.32138 11.4912 8.16667 11.4912C8.01196 11.4912 7.86358 11.4266 7.75419 11.3114C7.64479 11.1962 7.58333 11.0401 7.58333 10.8772V6.57897C7.58333 6.41612 7.64479 6.25993 7.75419 6.14478C7.86358 6.02963 8.01196 5.96493 8.16667 5.96493C8.32138 5.96493 8.46975 6.02963 8.57915 6.14478C8.68854 6.25993 8.75 6.41612 8.75 6.57897V10.8772Z"
                                                        fill="#E02424"/>
                                                </svg>
                                                <span class="text-sm font-medium text-red-600"
                                                      wire:click.prevent="removeConfidantPerson"
                                                      x-on:click="showFilter = true">
                                                    {{ __('Видалити') }}
                                                </span>
                                            @else
                                                <svg width="40" height="41" viewBox="0 0 40 41" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg"
                                                     wire:click.prevent="chooseConfidantPerson('{{ $patient['id'] }}')"
                                                     x-on:click="showFilter = false"
                                                >
                                                    <path
                                                        d="M0 20.5C0 9.45431 8.95431 0.5 20 0.5C31.0457 0.5 40 9.45431 40 20.5C40 31.5457 31.0457 40.5 20 40.5C8.95431 40.5 0 31.5457 0 20.5Z"
                                                        fill="#1A56DB"/>
                                                    <path d="M20 20.5H13H20Z" fill="white"/>
                                                    <path d="M20 13.5V20.5M20 20.5V27.5M20 20.5H27M20 20.5H13"
                                                          stroke="white" stroke-width="2" stroke-linecap="round"
                                                          stroke-linejoin="round"/>
                                                </svg>
                                            @endif
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </x-slot>
                    </x-tables.table>
                </div>
            </div>
            @elseif($searchPerformed && empty($patients))
                <div class="mb-6">
                    <div class="w-[70%] rounded-lg p-4 bg-gray-100">
                        <div class="flex items-center gap-2">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M9 6V9M9 12H9.0075M16.5 9C16.5 13.1421 13.1421 16.5 9 16.5C4.85786 16.5 1.5 13.1421 1.5 9C1.5 4.85786 4.85786 1.5 9 1.5C13.1421 1.5 16.5 4.85786 16.5 9Z"
                                    stroke="#1E1E1E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p class="font-semibold text-gray-900">{{ __('Нікого не знайдено') }}</p>
                        </div>
                        <span class="text-gray-900">{{ __('Спробуйте змінити параметри пошуку') }}</span>
                    </div>
                </div>
        </div>
    @endif
</div>
