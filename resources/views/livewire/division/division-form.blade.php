<div>

    <x-section-navigation x-data="{ showFilter: false }" class="">
        <x-slot name="title">{{ __('Місця надання послуг') }}</x-slot>
        {{-- <x-slot name="description">{{ __('Місця надання послуг') }}</x-slot> --}}

        <x-slot name="navigation">
            <div class="rounded-sm border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="flex justify-end border-stroke px-7 py-4 dark:border-strokedark">
                    <a
                        href="{{ route('division.form') }}"
                        type="button"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                    >
                        {{ __('Додати місце надання послуг') }}
                    </a>
                    <button wire:click="syncDivisions" class="button-sync">
                        {{ __('forms.synchronise_with_eHealth') }}
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
                            @nonempty($divisions->items())
                                @foreach ($divisions as $division)
                                    <tr x-data="{ divisionTypes: @entangle('dictionaries.DIVISION_TYPE') }">
                                        <td class="p-4 text-sm text-center font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            <p class="font-semibold text-gray-900 dark:text-white">
                                                {{ $division->uuid ?? '' }}
                                            </p>
                                        </td>
                                        <td class="p-4 text-sm font-normal text-center text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            <p class="text-gray-900 dark:text-white">
                                                {{ $division->name ?? '' }}
                                            </p>
                                        </td>
                                        <td x-text="divisionTypes['{{ $division->type }}']" class="p-4 text-sm font-normal text-center text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            <p class="text-gray-900 dark:text-white"></p>
                                        </td>
                                        <td class="p-4 text-sm font-normal text-center text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            <p class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500">
                                                {{ $division->phones['number'] ?? '' }}
                                            </p>
                                        </td>
                                        <td class="p-4 text-sm font-normal text-center text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            <p class="inline-flex items-center font-medium text-gray-600 dark:text-gray-500">
                                                {{ $division->email ?? '' }}
                                            </p>
                                        </td>
                                        <td class="p-4 text-sm font-normal text-center text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            @if ($division->status == 'INACTIVE')
                                                <span class="rejected text-meta-1">{{ __('Не активний') }}</span>
                                            @else
                                                <span class="approved text-meta-3">{{ __('Активний') }}</span>
                                            @endif
                                        </td>
                                        <td class="border-b border-[#eee] py-5 px-4">
                                            <div class="flex justify-center relative">
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
                                                        class="absolute right-0 mt-2 w-40 rounded-md bg-white shadow-md z-50"
                                                    >
                                                        @if ($division->status == 'ACTIVE')
                                                            <a
                                                                href="{{ route('division.form', $division) }}"
                                                                class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500"
                                                            >
                                                                {{ __('forms.edit') }}
                                                            </a>
                                                            <a
                                                                wire:click="deactivate({{ $division }}); open = !open"
                                                                href="#"
                                                                class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500"
                                                            >
                                                                {{ __('forms.deactivate') }}
                                                            </a>
                                                            <a
                                                            href="{{ route('healthcare_service.index', $division) }}"
                                                            class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500"
                                                            >
                                                                {{ __('forms.services') }}
                                                            </a>
                                                        @else
                                                            <a
                                                                wire:click="activate({{ $division }}); open = !open"
                                                                href="#"
                                                                class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500"
                                                            >
                                                                {{ __('forms.activate') }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
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
                    <x-pagination :pagination="$divisions" class="pagination" style="margin-block-start: -80px;"/>

                </div>
            </div>
        </div>

    {{-- </div> --}}

    @include('livewire.division._parts._division_form')
</div>
