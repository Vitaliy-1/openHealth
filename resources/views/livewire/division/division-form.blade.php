<div>

    <x-section-navigation x-data="{ showFilter: false }" class="">
        <x-slot name="title">{{ __('Місця надання послуг') }}</x-slot>
        <x-slot name="description">{{ __('Місця надання послуг') }}</x-slot>

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
                    <button
                        wire:click="syncDivisions"
                        type="button"
                        class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                    >
                        {{ __('Синхрозувати з ЕСОЗ') }}
                    </button>
                </div>
            </div>
        </x-slot>
    </x-section-navigation>

    <div class="flex flex-col h-screen -mt-4 border-t">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <x-tables.table>
                        <x-slot name="headers" :list="$tableHeaders"></x-slot>
                        <x-slot name="tbody">
                            @if ($divisions)
                                @foreach ($divisions as $item)
                                    <tr>
                                        <td class="border-b border-[#eee] py-5 px-4 ">
                                            <p class="text-black dark:text-white">
                                                {{ $item->uuid ?? '' }}
                                            </p>
                                        </td>
                                        <td class="border-b border-[#eee] py-5 px-4 ">
                                            <p class="text-black dark:text-white">
                                                {{ $item->name ?? '' }}
                                            </p>
                                        </td>
                                        <td class="border-b border-[#eee] py-5 px-4 ">
                                            <p class="text-black dark:text-white">
                                                {{ $item->type ?? '' }}
                                            </p>
                                        </td>
                                        <td class="border-b border-[#eee] py-5 px-4 ">
                                            <p class="text-black dark:text-white">
                                                {{ $item->phones['number'] ?? '' }}
                                            </p>
                                        </td>
                                        <td class="border-b border-[#eee] py-5 px-4 ">
                                            <p class="text-black dark:text-white">
                                                {{ $item->email ?? '' }}
                                            </p>
                                        </td>
                                        <td class="border-b border-[#eee] py-5 px-4 ">
                                            @if ($item->status == 'INACTIVE')
                                                <span class="text-meta-1">{{ __('Не активний') }}</span>
                                            @else
                                                <span class="text-meta-3">{{ __('Активний') }}</span>
                                            @endif
                                        </td>
                                        <td class="border-b border-[#eee] py-5 px-4 ">
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
                                                        <a
                                                            href="{{ route('division.form', $item) }}"
                                                            class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500"
                                                        >
                                                            {{ __('forms.edit') }}
                                                        </a>
                                                        @if ($item->status == 'ACTIVE')
                                                            <a
                                                                wire:click="deactivate({{ $item }}); open = !open"
                                                                href="#"
                                                                class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500"
                                                            >
                                                                {{ __('forms.deactivate') }}
                                                            </a>
                                                        @else
                                                            <a
                                                                wire:click="activate({{ $item }}); open = !open"
                                                                href="#"
                                                                class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500"
                                                            >
                                                                {{ __('forms.activate') }}
                                                            </a>
                                                        @endif

                                                        <a
                                                            href="{{ route('healthcare_service.index', $item) }}"
                                                            class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500"
                                                        >
                                                            {{ __('forms.services') }}
                                                        </a>
                                                    </divx-ref=>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </x-slot>
                    </x-tables.table>
                </div>
            </div>
        </div>
    </div>

    @include('livewire.division._parts._division_form')
</div>
