
<div>

    <x-section-navigation x-data="{ showFilter: false }" class="">
     <x-slot name="title">{{ __('Декларації') }}</x-slot>
    <x-slot name="navigation">

        <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
            <div class="flex items-center mb-4 sm:mb-0">
                <x-forms.form-group class="sm:pr-3">

                    <x-slot name="input">
                        <div x-data="{ showDropdown: false }" class="relative w-48 mt-1 sm:w-64 xl:w-96">
                            <x-forms.input class="default-input"
                                           wire:model.live="employee_name"
                                           type="text"
                                           x-on:keyup="showDropdown = true"
                                           x-on:keydown.escape="showDropdown = false"
                                           x-on:click.away="showDropdown = false"
                                           id="employee_name"
                                           placeholder="ФІО співробітника"
                                           autocomplete="off"/>

                            <x-dropdown-list x-show="showDropdown" class="absolute z-10">
                                <x-slot name="lists">
                                    @if($employees && count($employees) > 0)
                                        @foreach($employees as $employee)
                                            <li class="mb-3 cursor-pointer"
                                                x-on:click.prevent="
                                $wire.set('employee_uuid', '{{ $employee['uuid'] }}');
                                $wire.set('employee_name', '{{ $employee->fullName }}');
                                showDropdown = false;
                            ">
                                                {{ $employee->fullName }}
                                            </li>
                                        @endforeach
                                    @else
                                        <li class="mb-3">Нет сотрудников</li>
                                    @endif
                                </x-slot>
                            </x-dropdown-list>
                        </div>
                    </x-slot>

                    @error('employee_name')
                    <x-slot name="error">
                        <x-forms.error>
                            {{$message}}
                        </x-forms.error>
                    </x-slot>
                    @enderror
                </x-forms.form-group>
                <div class="flex items-center mb-4 sm:mb-0">
                    <div class="flex items-center w-full sm:justify-end">
                        <div class="flex pl-2 space-x-1">
                            <a  x-on:click="showFilter = !showFilter" href="#" class="inline-flex justify-center p-1 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M1 5h1.424a3.228 3.228 0 0 0 6.152 0H19a1 1 0 1 0 0-2H8.576a3.228 3.228 0 0 0-6.152 0H1a1 1 0 1 0 0 2Zm18 4h-1.424a3.228 3.228 0 0 0-6.152 0H1a1 1 0 1 0 0 2h10.424a3.228 3.228 0 0 0 6.152 0H19a1 1 0 0 0 0-2Zm0 6H8.576a3.228 3.228 0 0 0-6.152 0H1a1 1 0 0 0 0 2h1.424a3.228 3.228 0 0 0 6.152 0H19a1 1 0 0 0 0-2Z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="button-group">
                <button  type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Додати декларацію</button>
                <button wire:click="callSeeder" type="button" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Синхронізувати з ЕСОЗ</button>
            </div>

        </div>


        <form x-show="showFilter" class=" mt-6">

                <div class="grid gap-6 mb-6 md:grid-cols-4">
                    <x-forms.form-group>
                        <x-slot name="label">
                            <x-forms.label   :isRequired="true" for="patients_first_name" >
                               {{__('forms.first_name')}}
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.input class="default-input"
                                           wire:model.live="patients.first_name"
                                           type="text"
                                           autocomplete="off"/>
                        </x-slot>
                    </x-forms.form-group>
                    <div>
                        <label for="last_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Last name</label>
                        <input type="text" id="last_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Doe" required />
                    </div>
                    <div>
                        <label for="company" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Company</label>
                        <input type="text" id="company" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Flowbite" required />
                    </div>
                    <div>
                        <label for="phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Phone number</label>
                        <input type="tel" id="phone" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="123-45-678" pattern="[0-9]{3}-[0-9]{2}-[0-9]{3}" required />
                    </div>
                    <div>
                        <label for="website" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Website URL</label>
                        <input type="url" id="website" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="flowbite.com" required />
                    </div>
                    <div>
                        <label for="visitors" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unique visitors (per month)</label>
                        <input type="number" id="visitors" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="" required />
                    </div>
                </div>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
            </form>



    </x-slot>
    </x-section-navigation>
    @if($declarations)

        <x-tables.table>
            <x-slot name="headers" :list="$tableHeaders"></x-slot>

            <x-slot name="tbody">

                    @foreach($declarations as $k=>$declaration)
                        <tr>
                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400 ">
                                <div class="text-base font-semibold text-gray-900 dark:text-white">
                             {{$declaration->FullName}}
                                </div>
                            </td>
                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400 ">
                                <p class="text-black dark:text-white"></p>
                            </td>
                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400 ">
                                <p class="text-black dark:text-white"></p>
                            </td>
                        </tr>
                    @endforeach

            </x-slot>
        </x-tables.table>
    @endif

</div>

