<div>
    <x-section-navigation x-data="{ showFilter: false }" class="">
        <x-slot name="title">{{ __('Послуги') }}</x-slot>
        <x-slot name="description">{{ $currentDivision['type'] }} '{{ $currentDivision['name'] }}'</x-slot>
        <x-slot name="navigation">
            <div class="rounded-sm border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div
                    x-data="{ isDivisionActive: @js($divisionStatus) }"
                    class="flex justify-end border-stroke px-7 py-4 dark:border-strokedark"
                    x-cloak
                >
                    <button
                        x-show="isDivisionActive"
                        type="button"
                        wire:click="create"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                    >
                        {{ __('Додати Послугу') }}
                    </button>

                    <button
                        type="button"
                        wire:click="syncHealthcareServices"
                        class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                    >
                        {{ __('Синхронізувати з ЕСОЗ') }}
                    </button>
                </div>
            </div>
        </x-slot>
    </x-section-navigation>

    {{-- <div class="flex flex-col h-screen -mt-4 border-t"> --}}
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="shadow">
                    <x-tables.table class="mb-20">
                        <x-slot name="headers" :list="$tableHeaders"></x-slot>
                        <x-slot name="tbody">
                            @nonempty($healthcareServices->items())
                                @foreach ($healthcareServices as $k => $service)
                                    <tr>
                                        <td class="p-4 text-sm text-center font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            <p class="font-semibold text-gray-900 dark:text-white">
                                                {{ $service->uuid ?? '' }}
                                            </p>
                                        </td>

                                        <td class="p-4 text-sm font-normal text-center text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            <p class="inline-flex items-center font-medium text-gray-600 dark:text-gray-500">
                                                {{ $dictionaries['show']['HEALTHCARE_SERVICE_CATEGORIES'][$service->healthcare_category] ?? '' }}
                                            </p>
                                        </td>

                                        <td class="p-4 text-sm font-normal text-center text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            <p class="inline-flex items-center font-medium text-gray-600 dark:text-gray-500">
                                                {{ $dictionaries['show']['PROVIDING_CONDITION'][$service->providing_condition] ?? '' }}
                                            </p>
                                        </td>

                                        <td class="p-4 text-sm font-normal text-center text-gray-500 whitespace-nowrap dark:text-gray-400 ">
                                            <p class="text-gray-900 dark:text-white">
                                                {{ $dictionaries['show']['SPECIALITY_TYPE'][$service->speciality_type] ?? '' }}
                                            </p>
                                        </td>

                                        <td class="p-4 text-sm font-normal text-center text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            @if ($service->status == 'DEACTIVATED')
                                                <span class="rejected text-meta-1">{{ __('Не активний') }}</span>
                                            @else
                                                <span class="approved text-meta-3">{{ __('Активний') }}</span>
                                            @endif
                                        </td>

                                        <td class="border-b border-[#eee] py-5 px-4 ">
                                            @if ($divisionStatus)
                                                <div class="flex justify-center">
                                                    <div x-data="{
                                                            open: false,
                                                            toggle() {
                                                                if (this.open) {
                                                                    return this.close()
                                                                }

                                                                this.$refs.button.focus()

                                                                this.open = true
                                                            },
                                                            close(focusAfter) {
                                                                if (!this.open) return

                                                                this.open = false

                                                                focusAfter && focusAfter.focus()
                                                            }
                                                        }"
                                                        x-on:keydown.escape.prevent.stop="close($refs.button)"
                                                        x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
                                                        x-id="['dropdown-button']"
                                                        class="relative"
                                                    >
                                                        <button
                                                            x-ref="button"
                                                            x-on:click="toggle()"
                                                            :aria-expanded="open"
                                                            :aria-controls="$id('dropdown-button')"
                                                            type="button"
                                                            class="hover:text-primary"
                                                        >
                                                            <svg
                                                                class="fill-current"
                                                                width="18"
                                                                height="18"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                fill="none"
                                                                viewBox="0 0 24 24"
                                                                stroke-width="1.5"
                                                            >
                                                                <path
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"
                                                                />
                                                            </svg>
                                                        </button>

                                                        <div
                                                            x-ref="panel"
                                                            x-show="open"
                                                            x-transition.origin.top.left
                                                            x-on:click.outside="close($refs.button)"
                                                            :id="$id('dropdown-button')"
                                                            style="display: none;"
                                                            class="absolute right-0 mt-2 w-40 rounded-md bg-white shadow-md z-50"
                                                        >
                                                            @if ($service->status == 'ACTIVE')
                                                                <a
                                                                    wire:click="edit({{ $service }}); toggle()"
                                                                    href="#"
                                                                    class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500"
                                                                >
                                                                    {{ __('forms.edit') }}
                                                                </a>
                                                                <a
                                                                    href="#"
                                                                    wire:click="deactivate({{ $service }}); toggle()"
                                                                    class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500"
                                                                >
                                                                    {{ __('forms.deactivate') }}
                                                                </a>
                                                            @else
                                                                <a
                                                                    href="#"
                                                                    wire:click="activate({{ $service }}); toggle()"
                                                                    class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500"
                                                                >
                                                                    {{ __('forms.activate') }}
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @elsenonempty
                            <tr>
                                <td class="text-black w-full p-4 border-gray-200 text-center dark:bg-gray-800 dark:border-gray-700 dark:text-white" colspan="7">
                                    <p >
                                        {{ __('Нічого не знайдено') }}
                                    </p>
                                </td>
                            </tr>
                            @endnonempty
                        </x-slot>
                    </x-tables.table>
                    <x-pagination :pagination="$healthcareServices" class="pagination" style="margin-block-start: -80px;"/>
                </div>
            </div>
        </div>
    {{-- </div> --}}

    <div class="footer flex flex-start border-stroke px-7 py-2 my-4">
        <x-secondary-button>
            <a href="{{ route('division.index') }}">
                {{ __('Назад') }}
            </a>
        </x-secondary-button>
    </div>
    @include('livewire.division._parts._healthcare_service_form')

</div>
