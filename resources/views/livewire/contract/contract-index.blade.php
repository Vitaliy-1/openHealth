<div>

    <x-section-title>
        <x-slot name="title">{{ __('forms.contract') }}</x-slot>
        <x-slot name="description">{{ __('forms.contract') }}</x-slot>
    </x-section-title>

    <div class="mb-10 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b items-center flex justify-end border-stroke px-7 py-4 dark:border-strokedark">
            <a href="" type="button" class="btn-green h-[66px]" wire:click.prevent="openModal('intialization_contract')">
                {{__('forms.add_contract')}}
            </a>
        </div>
        <x-tables.table>
            <x-slot name="headers" :list="$tableHeaders"></x-slot>
            <x-slot name="tbody">
                @if($legalEntity->contract)
                    @foreach($legalEntity->contract as $contract)
                        <tr>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{$contract->uuid ?? ''}}</p>
                                </p>
                            </td>

                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{$contract->contract_number ?? ''}}</p>
                                </p>
                            </td>

                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{$contract->start_date ?? ''}}</p>
                                </p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{$contract->end_date ?? ''}}</p>
                                </p>
                            </td>
                            <td class="border-b border-[#eee] py-5 px-4 ">
                                <p class="text-black dark:text-white">{{$contract->status ?? ''}}
                                    <a wire:click.prevent="showContract({{$contract->id}})" class="text-primary" href="">Деталі</a>
                                </p>
                                </p>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </x-slot>
        </x-tables.table>
    </div>

    @if($showModal == 'intialization_contract')
        <x-alert-modal name="title">
            <x-slot name="title">
                {{__('forms.initialization_contract')}}
            </x-slot>
            <x-slot name="text">
                    @if($hasInitContract)
                        <x-forms.select wire:model="contract_type" class="default-select">
                            <x-slot name="option">
                                <option value="">{{__('forms.contract_type')}}</option>
                                @foreach($this->dictionaries['CONTRACT_TYPE'] as $k=>$contract_type)
                                    <option value="{{$k}}">{{$contract_type}}</option>
                                @endforeach
                            </x-slot>
                        </x-forms.select>
                        @error('contract_type')
                        <x-forms.error>
                            {{$message}}
                        </x-forms.error>
                @enderror
                @else
                    <p> {{__('forms.alert_initialization_contract')}} </p>
                @endif
            </x-slot>
            <x-slot name="button">
                <div class="justify-between items-center pt-0 space-y-4 sm:flex sm:space-y-0">
                    <button wire:click="closeModal" type="button"
                            class="py-2 px-4 w-full text-sm font-medium text-gray-500 bg-white rounded-lg border border-gray-200 sm:w-auto hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                        {{__('forms.cansel')}}</button>
                        <button wire:click="createRequest" type="button"
                                class="py-2 bg-primary px-4 w-full text-sm font-medium text-center text-white rounded-lg bg-primary-700 sm:w-auto hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                            {{$hasInitContract ? __('forms.confirm') :__('forms.continue')}}
                        </button>
                </div>
            </x-slot>

        </x-alert-modal>
    @elseif($showModal == 'show_contract')
        <x-alert-modal name="title">
            <x-slot name="title">
                {{__('forms.contract')}}
            </x-slot>
            <x-slot name="text">
                <x-forms.form-group class="mb-4">
                    <x-slot name="label">
                        <x-forms.label for="inserted_at" class="default-label">
                            {{__('forms.inserted_at_contract')}} *
                        </x-forms.label>
                    </x-slot>
                    <x-slot name="input">
                        <x-forms.input disabled class="default-input"
                                       value="{{$contract->inserted_at ?? ''}}"
                                       type="datetime"
                                       id="inserted_at"/>
                    </x-slot>

                </x-forms.form-group>
                <x-forms.form-group class="mb-4">
                    <x-slot name="label">
                        <x-forms.label for="contractor_base" class="default-label">
                            {{__('forms.status_reason')}} *
                        </x-forms.label>
                    </x-slot>
                    <x-slot name="input">
                        <x-forms.input disabled class="default-input"
                                       value="{{$contract->status_reason?? ''}}"
                                       type="text"
                                       id="status_reason"/>
                    </x-slot>

                </x-forms.form-group>
                <x-forms.form-group class="mb-4">
                    <x-slot name="label">
                        <x-forms.label for="contractor_base" class="default-label">
                            {{__('forms.contractor_rmsp_amount')}} *
                        </x-forms.label>
                    </x-slot>
                    <x-slot name="input">
                        <x-forms.input disabled class="default-input"
                                       value="{{$contract->contractor_rmsp_amount?? ''}}"
                                       type="text"
                                       id="status_reason"/>
                    </x-slot>
                </x-forms.form-group>
            </x-slot>
            <x-slot name="button" >
                <div class="justify-between items-center pt-0 space-y-4 sm:flex sm:space-y-0">
                    <button wire:click="closeModal" type="button"
                            class="py-2 px-4 w-full text-sm font-medium text-gray-500 bg-white rounded-lg border border-gray-200 sm:w-auto hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                        {{__('forms.close')}}</button>

                </div>
            </x-slot>

        </x-alert-modal>
    @endif
    {{--    @include('livewire.employee._parts._employee_form')--}}
</div>

