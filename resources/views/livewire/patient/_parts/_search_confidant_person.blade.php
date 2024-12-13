<div>
    <x-section-navigation x-data="{ showFilter: true }" x-on:close-search-form.window="showFilter = false">
        <x-slot name="navigation">

            <!-- Button to show/hide the form -->
            <div class="flex items-center mb-4 sm:mb-0">
                <div class="flex items-center w-full sm:justify-start">
                    <div class="flex pl-2 space-x-1">
                        <a x-on:click.prevent="showFilter = !showFilter" href="#"
                           class="inline-flex justify-center p-1 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                 xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M1 5h1.424a3.228 3.228 0 0 0 6.152 0H19a1 1 0 1 0 0-2H8.576a3.228 3.228 0 0 0-6.152 0H1a1 1 0 1 0 0 2Zm18 4h-1.424a3.228 3.228 0 0 0-6.152 0H1a1 1 0 1 0 0 2h10.424a3.228 3.228 0 0 0 6.152 0H19a1 1 0 0 0 0-2Zm0 6H8.576a3.228 3.228 0 0 0-6.152 0H1a1 1 0 0 0 0 2h1.424a3.228 3.228 0 0 0 6.152 0H19a1 1 0 0 0 0-2Z"/>
                            </svg>
                            <span class="ml-1.5 txt-sm:hidden">{{ __('Обрати іншого') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search input fields -->
            <div x-show="showFilter" class="w-full">
                @include('livewire.patient.patients-filter')
            </div>
        </x-slot>
    </x-section-navigation>

    <!-- Patient list -->
    <div class="flex flex-col h-auto">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    @if($patients && count($patients) > 0)
                        <x-tables.table>
                            <x-slot name="headers" :list="$tableHeaders"></x-slot>

                            <x-slot name="tbody">
                                @foreach($patients as $patient)
                                    <tr>
                                        <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            <p class="text-base font-semibold text-gray-900 dark:text-white">
                                                {{ $patient['first_name'] }}
                                            </p>
                                        </td>
                                        <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            <p class="text-base font-semibold text-gray-900 dark:text-white">
                                                {{ $patient['last_name'] }}
                                            </p>
                                        </td>
                                        <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            <p class="text-base font-semibold text-gray-900 dark:text-white">
                                                {{ $patient['second_name'] }}
                                            </p>
                                        </td>
                                        <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            <p class="text-black dark:text-white">
                                                {{ $patient['birth_date'] }}
                                            </p>
                                        </td>
                                        <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            <p class="text-black dark:text-white">
                                                {{ $patient['birth_settlement'] }}
                                            </p>
                                        </td>
                                        <td>
                                            <a type="button" href="#" class="text-green-700 hover:text-white border border-green-700
                                                hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300
                                                font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2
                                                dark:border-green-500 dark:text-green-500 dark:hover:text-white
                                                dark:hover:bg-green-600 dark:focus:ring-green-800
                                            {{ $selectedPatientId === $patient['id'] ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : '' }}"
                                               wire:click.prevent="chooseConfidantPerson('{{ $patient['id'] }}')"
                                                {{ $selectedPatientId === $patient['id'] ? 'disabled' : '' }}>
                                                {{ $selectedPatientId === $patient['id'] ? __('Обрано') : __('Обрати') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </x-slot>
                        </x-tables.table>
                    @else
                        <div
                            class="bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
                            <p class="text-black dark:text-white">
                                {{ __('Нічого не знайдено') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
