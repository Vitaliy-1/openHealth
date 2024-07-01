<div>

    <x-section-title>
        <x-slot name="title">{{ __('Ліцензії') }}</x-slot>
        <x-slot name="description">{{ __('Ліцензії') }}</x-slot>
    </x-section-title>

    <div class="mb-10 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b items-center flex justify-between border-stroke px-7 py-4 dark:border-strokedark">

            <div class="flex">
                <div>
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                        {{ __('Статус ліцензії') }}
                    </label>
                    <div  class="relative z-20 bg-white dark:bg-form-input">
                        <select wire:model="selectedStatusOption" wire:change="sortTypeLicenses()"
                                class="relative z-20 w-full appearance-none rounded border border-stroke bg-transparent py-3 pl-5 pr-12 outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input"
                            >
                            <option selected value="all" class="text-body">{{ __('Усі') }}</option>
                            <option value="is_primary" class="text-body">{{ __('Основна') }}</option>
                            <option  value="is_additional" class="text-body">{{ __('Додаткова') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <a href="{{ route('license.create') }}" class="btn-green h-[66px]" wiree:click="create('license.create')">
                {{ __('Додати ліцензію') }}
            </a>
        </div>

        @if (count($licenses) > 0)
            <x-tables.table>
                <x-slot name="headers" :list="$tableHeaders"></x-slot>
                <x-slot name="tbody">
                    @foreach ($licenses as $k => $license)
                        <tr>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{ $license->type ?? '' }}</p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{ $license->issued_date ?? '' }}</p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{ $license->what_licensed ?? '' }}</p>
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
                                    }" x-on:keydown.escape.prevent.stop="close($refs.button)"
                                        x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
                                        x-id="['dropdown-button']" class="relative">
                                        <button x-ref="button" x-on:click="toggle()" :aria-expanded="open"
                                            :aria-controls="$id('dropdown-button')" type="button"
                                            class="hover:text-primary">
                                            <svg class="fill-current" width="18" height="18"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                            </svg>
                                        </button>
                                        <div x-ref="panel" x-show="open" x-transition.origin.top.left
                                            x-on:click.outside="close($refs.button)" :id="$id('dropdown-button')"
                                            style="display: none;"
                                            class="absolute right-0 mt-2 w-40 rounded-md bg-white shadow-md z-50">

                                            <a href="{{ route('license.show', $license->id) }}"
                                                class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
                                                {{ __('forms.info') }}
                                            </a>

                                            <a href="{{ route('license.form', $license->id) }}"
                                                class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
                                                {{ __('forms.update_info') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>

                        </tr>
                    @endforeach

                </x-slot>
            </x-tables.table>

        @else
            <div class="border-b items-center flex justify-between border-stroke px-7 py-4 dark:border-strokedark">
                <div class="flex">
                    <div>
                        <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                            {{ __('Немає ліцензій') }}
                        </label>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
