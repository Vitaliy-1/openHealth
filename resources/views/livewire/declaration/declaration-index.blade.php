<div>
    <x-section-navigation x-data="{ showFilter: false }" class="">
        <x-slot name="title">{{ __('Декларації') }}</x-slot>

        <x-slot name="navigation">
            <div class="justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0">
                    <x-forms.form-group class="sm:pr-3">
                        <x-slot name="input" class="max-w-2xs">
                            <div x-data="{ showDropdown: false }" class="relative w-48 mt-1 sm:w-64 xl:w-96">
                                <x-forms.input
                                    class="default-input"
                                    wire:model.live="employee_filter.full_name"
                                    type="text"
                                    x-on:keyup="showDropdown = true"
                                    x-on:keydown.escape="showDropdown = false"
                                    x-on:click.away="showDropdown = false"
                                    id="employee_name"
                                    placeholder="{{ __('ПІБ лікаря') }}"
                                    autocomplete="off"
                                />
                                <x-dropdown-list x-show="showDropdown" class="absolute z-10">
                                    <x-slot name="lists">
                                        @if ($employees && count($employees) > 0)
                                            @foreach ($employees as $employee)
                                                <li class="mb-3 cursor-pointer"
                                                    x-on:click.prevent="
                                                        $wire.set('employee_filter.employee_uuid', '{{ $employee['uuid'] }}');
                                                        $wire.set('employee_filter.full_name', '{{ $employee->fullName }}');
                                                        showDropdown = false;
                                                    "
                                                >
                                                    {{ $employee->fullName }}
                                                </li>
                                            @endforeach
                                        @endif
                                    </x-slot>
                                </x-dropdown-list>
                            </div>
                        </x-slot>
                    </x-forms.form-group>
                    <div class="flex items-center mb-4 sm:mb-0">
                        <div class="flex items-center w-full sm:justify-end">
                            <div class="flex pl-2 space-x-1">
                                <a href="#"
                                    x-on:click="showFilter = !showFilter"
                                    class="inline-flex justify-center p-1 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                                >
                                    <svg
                                        class="w-6 h-6 text-gray-800 dark:text-white"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path d="M1 5h1.424a3.228 3.228 0 0 0 6.152 0H19a1 1 0 1 0 0-2H8.576a3.228 3.228 0 0 0-6.152 0H1a1 1 0 1 0 0 2Zm18 4h-1.424a3.228 3.228 0 0 0-6.152 0H1a1 1 0 1 0 0 2h10.424a3.228 3.228 0 0 0 6.152 0H19a1 1 0 0 0 0-2Zm0 6H8.576a3.228 3.228 0 0 0-6.152 0H1a1 1 0 0 0 0 2h1.424a3.228 3.228 0 0 0 6.152 0H19a1 1 0 0 0 0-2Z"/>
                                    </svg>
                                    <span class="ml-1.5 txt-sm:hidden">{{ __('Параметри пошуку') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="button-group">
                    <button
                        type="button"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                    >
                        Додати декларацію
                    </button>
                    <button
                        wire:click="callSeeder"
                        type="button"
                        class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                    >
                        Синхронізувати з ЕСОЗ
                    </button>
                </div>
            </div>
            <livewire:components.declaration.declarations-filter />

        </x-slot>
    </x-section-navigation>

    <div class="flex flex-col h-screen	">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    @if (count($declarations) > 0)
                        <x-tables.table>
                            <x-slot name="headers" :list="$tableHeaders"></x-slot>

                            <x-slot name="tbody">
                                @foreach ($declarations as $declaration)
                                    <tr>
                                        <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400 ">
                                            <p class="text-base font-semibold text-gray-900 dark:text-white">
                                                {{ $declaration->fullName }}
                                            </p>
                                        </td>
                                        <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400 ">
                                            <a href="tel:{{ $declaration->phone }}"
                                                class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline"
                                            >
                                                {{ $declaration->phone }}
                                            </a>
                                        </td>
                                        <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400 ">
                                            <p class="text-black dark:text-white">
                                                {{ $declaration->birthDate }}
                                            </p>
                                        </td>
                                        <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400 ">
                                            <p class="text-base font-semibold text-gray-900 dark:text-white">
                                                {{ $declaration->declaration_number }}
                                            </p>
                                        </td>
                                        <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400 ">
                                            <x-status-label :status="$declaration->status"></x-status-label>
                                        </td>
                                        <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400 ">
                                            <p class="text-black dark:text-white">
                                                {{ $declaration->startDateDeclaration }}
                                            </p>
                                        </td>
                                        <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400 ">
                                            <p class="text-base font-semibold text-gray-900 dark:text-white">
                                                {{ $declaration->doctorFullName ?? '' }}
                                            </p>
                                        </td>
                                        <td>
                                            <a href="#" wire:click="showDeclaration({{ json_encode($declaration) }})">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="30px"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="#fff"
                                                    class="size-1 fill-blue-500"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"
                                                    />
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                                    />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                            </x-slot>
                        </x-tables.table>
                        <div class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
                            {{ $declarations->links() }}
                        </div>
                    @else
                        <div class=" bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
                            <p class="text-black dark:text-white">
                                {{ __('Нічого не знайдено') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($declaration_show)
        @include('livewire.declaration._parts.pop-up-show')
    @endif
</div>
