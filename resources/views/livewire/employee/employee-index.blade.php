<div>

    <x-section-navigation class="">
        <x-slot name="title">{{ __('Співробітники') }}</x-slot>
        <x-slot name="navigation">
            <div class=" justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                <div class="flex items-center mb-4 sm:mb-0">
                    <x-forms.form-group class="sm:pr-3 о">
                        <x-slot name="label">
                            <x-forms.label for="ownerPosition" class="default-label">
                                {{__('Статус Співробітника')}}
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input" class="max-w-2xs">
                            <x-forms.select
                                class="default-input" wire:model.live="status" wire:change="sortEmployees()"
                                type="text"
                                id="ownerPosition"
                            >
                                <x-slot name="option">
                                    <option selected value="APPROVED" class="text-body">Активні</option>
                                    <option value="NEW" class="text-body">Нові</option>
                                    <option value="CACHE" class="text-body">Не завешені</option>
                                </x-slot>
                            </x-forms.select>
                        </x-slot>
                    </x-forms.form-group>
                </div>
                <div class="button-group border-0 ">
                    <a href="{{route('employee.form',['storeId' => $storeId])}}" type="button"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        {{__('Додати Співробітника')}}
                    </a>
                    <button wire:click="syncEmployees" type="button"
                            class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                        {{__('Синхрозувати з ЕСОЗ')}}
                    </button>
                </div>
            </div>

        </x-slot>
    </x-section-navigation>

    <x-section>
        @if(count($employees) > 0)
            <x-tables.table>
                <x-slot name="headers" :list="$tableHeaders"></x-slot>
                <x-slot name="tbody">
                    @if($employees)
                        @foreach($employees as $k=>$employee)
                            <tr>
                                <td class="border-b border-[#eee] py-5 px-4 ">
                                    <p class="text-black dark:text-white">{{ $employee->uuid ?? ''}}</p>
                                </td>
                                <td class="border-b border-[#eee] py-5 px-4 ">
                                    <p class="text-base font-semibold text-gray-900 dark:text-white">
                                        {{$employee->fullName}}
                                    </p>
                                </td>

                                <td class="border-b border-[#eee] py-5 px-4 ">
                                    <a href="tel:{{$employee->phone}}" class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                        {{$employee->phone}}
                                    </a>
                                </td>
                                <td x-data="{open: false}" class="border-b border-[#eee] py-5 px-4 ">
                                    @if($employee->email)
                                        <p class="text-black dark:text-white">{{$employee->email}}</p>
                                    @else
                                        <x-button x-on:click="open = ! open" class="text-left">Додати пошту</x-button>
                                    @endif
                                </td>
                                <td class="border-b border-[#eee] py-5 px-4 ">
                                    <p class="text-black dark:text-white">{{$employee->position ?? ''}}</p>
                                </td>
                                <td class="border-b border-[#eee] py-5 px-4 ">
                                    @if(isset($employee->status))
                                        <x-status-label :status="$employee->status"></x-status-label>
                                    @endif
                                </td>
                                <td class="border-b border-[#eee] py-5 px-4 ">
                                    <div class="flex justify-center">
                                        <div
                                            x-data="{
            open: false,
            toggle() {
                if (this.open) {
                    return this.close()
                }

                this.$refs.button.focus()

                this.open = true
            },
            close(focusAfter) {
                if (! this.open) return

                this.open = false

                focusAfter && focusAfter.focus()
            }
        }"
                                            x-on:keydown.escape.prevent.stop="close($refs.button)"
                                            x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
                                            x-id="['dropdown-button']"
                                            class="relative">
                                            <button
                                                x-ref="button"
                                                x-on:click="toggle()"
                                                :aria-expanded="open"
                                                :aria-controls="$id('dropdown-button')"
                                                type="button"
                                                class="hover:text-primary">
                                                <svg class="fill-current" width="18" height="18"
                                                     xmlns="http://www.w3.org/2000/svg" fill="none"
                                                     viewBox="0 0 24 24"
                                                     stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/>
                                                </svg>
                                            </button>
                                            <div
                                                x-ref="panel"
                                                x-show="open"
                                                x-transition.origin.top.left
                                                x-on:click.outside="close($refs.button)"
                                                :id="$id('dropdown-button')"
                                                style="display: none;"
                                                class="absolute right-0 mt-2 w-40 rounded-md bg-white shadow-md z-50">
                                                <a href="{{route('employee.form', $employee->id)}}"
                                                   class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
                                                    {{__('forms.edit')}}
                                                </a>
                                                <a wire:click="showModalDismissed({{$employee->id}})"
                                                   class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
                                                    {{__('forms.dismissed')}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    @endif
                </x-slot>
            </x-tables.table>
            <div
                class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
                {{--                            {{ $employees->links() }}--}}
            </div>
        @else
            <div
                class=" bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
                <p class="text-black dark:text-white">
                    {{__('Нічого не знайдено')}}
                </p>
            </div>
        @endif
    </x-section>


    {{--    <div class="mb-10 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">--}}
    {{--       --}}
    {{--        <x-tables.table>--}}
    {{--            <x-slot name="headers" :list="$tableHeaders"></x-slot>--}}
    {{--            <x-slot name="tbody">--}}
    {{--                @if($employeesCache )--}}
    {{--                    @foreach($employeesCache as $k=>$item)--}}
    {{--                        <tr>--}}
    {{--                            <td class="border-b border-[#eee] py-5 px-4 ">--}}
    {{--                                <p class="text-black dark:text-white">{{ ''}}</p>--}}
    {{--                            </td>--}}
    {{--                            <td class="border-b border-[#eee] py-5 px-4 ">--}}
    {{--                                <p class="text-black dark:text-white">{{$item->employee->party['firstName'] ?? ''}}--}}
    {{--                                    {{$item->employee->party['lastName'] ?? ' '}}--}}
    {{--                                    {{$item->employee->party['secondName'] ?? ' '}}--}}
    {{--                                </p>--}}
    {{--                            </td>--}}

    {{--                            <td class="border-b border-[#eee] py-5 px-4 ">--}}
    {{--                                <p class="text-black dark:text-white">{{$item->employee->party['phones'][0]['number'] ?? ''}}</p>--}}
    {{--                            </td>--}}
    {{--                            <td class="border-b border-[#eee] py-5 px-4 ">--}}
    {{--                                <p class="text-black dark:text-white">{{$item->employee['email']  ?? ''}}</p>--}}
    {{--                            </td>--}}
    {{--                            <td class="border-b border-[#eee] py-5 px-4 ">--}}
    {{--                                <p class="text-black dark:text-white">{{$item->employee['position'] ?? ''}}</p>--}}
    {{--                            </td>--}}
    {{--                            <td class="border-b border-[#eee] py-5 px-4 ">--}}
    {{--                                {{__('Завершить реестрацію')}}--}}
    {{--                            </td>--}}
    {{--                            <td class="border-b border-[#eee] py-5 px-4 ">--}}
    {{--                                <div class="flex justify-center">--}}
    {{--                                    <div--}}
    {{--                                        x-data="{--}}
    {{--            open: false,--}}
    {{--            toggle() {--}}
    {{--                if (this.open) {--}}
    {{--                    return this.close()--}}
    {{--                }--}}

    {{--                this.$refs.button.focus()--}}

    {{--                this.open = true--}}
    {{--            },--}}
    {{--            close(focusAfter) {--}}
    {{--                if (! this.open) return--}}

    {{--                this.open = false--}}

    {{--                focusAfter && focusAfter.focus()--}}
    {{--            }--}}
    {{--        }"--}}
    {{--                                        x-on:keydown.escape.prevent.stop="close($refs.button)"--}}
    {{--                                        x-on:focusin.window="! $refs.panel.contains($event.target) && close()"--}}
    {{--                                        x-id="['dropdown-button']"--}}
    {{--                                        class="relative">--}}
    {{--                                        <button--}}
    {{--                                            x-ref="button"--}}
    {{--                                            x-on:click="toggle()"--}}
    {{--                                            :aria-expanded="open"--}}
    {{--                                            :aria-controls="$id('dropdown-button')"--}}
    {{--                                            type="button"--}}
    {{--                                            class="hover:text-primary">--}}
    {{--                                            <svg class="fill-current" width="18" height="18"--}}
    {{--                                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"--}}
    {{--                                                 stroke-width="1.5">--}}
    {{--                                                <path stroke-linecap="round" stroke-linejoin="round"--}}
    {{--                                                      d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/>--}}
    {{--                                            </svg>--}}
    {{--                                        </button>--}}
    {{--                                        <div--}}
    {{--                                            x-ref="panel"--}}
    {{--                                            x-show="open"--}}
    {{--                                            x-transition.origin.top.left--}}
    {{--                                            x-on:click.outside="close($refs.button)"--}}
    {{--                                            :id="$id('dropdown-button')"--}}
    {{--                                            style="display: none;"--}}
    {{--                                            class="absolute right-0 mt-2 w-40 rounded-md bg-white shadow-md z-50">--}}

    {{--                                            <a href="{{route('employee.form',['storeId'=>$k])}}"--}}
    {{--                                               class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">--}}
    {{--                                                {{__('forms.edit')}}--}}
    {{--                                            </a>--}}



    {{--                                        </div>--}}
    {{--                                    </div>--}}
    {{--                                </div>--}}
    {{--                            </td>--}}

    {{--                        </tr>--}}
    {{--                    @endforeach--}}
    {{--                @endif--}}
    {{--               --}}
    {{--                @if($employees)--}}

    {{--                @endif--}}
    {{--            </x-slot>--}}
    {{--        </x-tables.table>--}}
    {{--    </div>--}}

    @if($showModal)
        <x-alert-modal name="title">
            <x-slot name="title">
                {{__('forms.dismissed')}}
            </x-slot>
            <x-slot name="text">
                {{$dismissText}}
            </x-slot>
            <x-slot name="button">
                <div class="justify-between items-center pt-0 space-y-4 sm:flex sm:space-y-0">
                    <button wire:click="closeModal" type="button"
                            class="py-2 px-4 w-full text-sm font-medium text-gray-500 bg-white rounded-lg border border-gray-200 sm:w-auto hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                        {{__('forms.cansel')}}</button>
                    <button wire:click="dismissed({{$dismissedId}})" type="button"
                            class="py-2 bg-primary px-4 w-full text-sm font-medium text-center text-white rounded-lg bg-primary-700 sm:w-auto hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                        {{__('forms.confirm')}}
                    </button>
                </div>
            </x-slot>

        </x-alert-modal>

    @endif
    {{--    @include('livewire.employee.Parts.EmployeeForm')--}}
</div>


