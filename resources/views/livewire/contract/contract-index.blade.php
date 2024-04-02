<div>

    <x-section-title>
        <x-slot name="title">{{ __('forms.contract') }}</x-slot>
        <x-slot name="description">{{ __('forms.contract') }}</x-slot>
    </x-section-title>

    <div class="mb-10 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b items-center flex justify-end border-stroke px-7 py-4 dark:border-strokedark">
            <a href="" type="button" class="btn-green h-[66px]" wire:click.prevent="openModal()">
                {{__('forms.add_contract')}}
            </a>
        </div>
        <x-tables.table>
            <x-slot name="headers" :list="$tableHeaders"></x-slot>
            <x-slot name="tbody">
            </x-slot>
        </x-tables.table>
    </div>

    @if($showModal)
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

    @endif
    {{--    @include('livewire.employee._parts._employee_form')--}}
</div>


