<div>

    <x-section-title>
        <x-slot name="title">{{ __('Співробітники') }}</x-slot>
        <x-slot name="description">{{ __('Співробітники') }}</x-slot>
    </x-section-title>

    <div class="mb-10 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b items-center flex justify-between border-stroke px-7 py-4 dark:border-strokedark">

            <div>
                <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                    Статус співробітника
                </label>
                <div  class="relative z-20 bg-white dark:bg-form-input">
                    <select wire:model="selectedOption" wire:change="sortEmployees()"
                            class="relative z-20 w-full appearance-none rounded border border-stroke bg-transparent py-3 pl-5 pr-12 outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input"
                           >
                        <option selected value="is_active" class="text-body">Активні</option>

                        <option  value="is_inactive" class="text-body">Не активні </option>

                        <option value="is_cache" class="text-body">Не завешені</option>
                    </select>
                </div>
            </div>
            <a href="" type="button" class="btn-green h-[66px]" wire:click="create('employee.employee-form')">
                {{__('Додати Співробітника')}}
            </a>
        </div>
        <x-tables.table>
            <x-slot name="headers" :list="$tableHeaders"></x-slot>
            <x-slot name="tbody">
                @if($employeesCache )
                    @foreach($employeesCache as $k=>$item)
                        <tr>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{ ''}}</p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{$item->employee['first_name'] ?? ''}}
                                    {{$item->employee['last_name'] ?? ' '}}
                                    {{$item->employee['second_name'] ?? ' '}}
                                </p>
                            </td>

                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{$item->employee['phones'][0]['number'] ?? ''}}</p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{$item->employee['email']  ?? ''}}</p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{$item->employee['position'] ?? ''}}</p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                {{__('Завершить реестрацію')}}
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
                                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
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

                                            <a href="{{route('employee.form',['store_id'=>$k])}}"
                                               class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
                                                {{__('forms.edit')}}
                                            </a>



                                        </div>
                                    </div>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                @endif
                @if($employees)
                    @foreach($employees as $k=>$employee)
                        <tr>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{ $employee->uuid ?? ''}}</p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{$employee->full_name ?? ''}}

                                </p>
                            </td>

                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{$employee->phones[0]['number'] ?? ''}}</p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{$employee->email  ?? ''}}</p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{$employee->position ?? ''}}</p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                {{$employee->status}}
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
                                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
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
                @if($employees)

                @endif
            </x-slot>
        </x-tables.table>
    </div>

    @if($showModal)
        <x-alert-modal  name="title">
            <x-slot name="title">
                {{__('forms.dismissed')}}
            </x-slot>
            <x-slot name="text">
                {{$dismiss_text}}
            </x-slot>
            <x-slot name="button">
                <div class="justify-between items-center pt-0 space-y-4 sm:flex sm:space-y-0">
                        <button  wire:click="closeModal" type="button"  class="py-2 px-4 w-full text-sm font-medium text-gray-500 bg-white rounded-lg border border-gray-200 sm:w-auto hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                            {{__('forms.cansel')}}</button>
                        <button  wire:click="dismissed({{$dismissed_id}})" type="button" class="py-2 bg-primary px-4 w-full text-sm font-medium text-center text-white rounded-lg bg-primary-700 sm:w-auto hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                            {{__('forms.confirm')}}
                        </button>
                </div>
            </x-slot>

        </x-alert-modal>

    @endif
{{--    @include('livewire.employee._parts._employee_form')--}}
</div>


