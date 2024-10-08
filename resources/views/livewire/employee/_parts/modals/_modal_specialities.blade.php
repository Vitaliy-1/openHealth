

<x-dialog-modal maxWidth="3xl" class="w-3 h-full" wire:model.live="showModal">
    <x-slot name="title">
        {{__('Додати Спеціальність')}}
    </x-slot>
    <x-slot name="content">
         <x-forms.forms-section-modal submit="{!! $mode === 'edit' ? 'update(\'specialities\',' . $key_property . ')' : 'store(\'specialities\')' !!}">
            <x-slot name="form">
                <div  class="pt-4 grid grid gap-4 grid-cols-2">
                    <x-forms.form-group class="">
                        <x-slot name="label">
                            <x-forms.label for="speciality_officio" class="default-label">
                                {{__('forms.speciality_officio')}} *
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.input  wire:model="employee_request.specialities.speciality_officio" type="checkbox"
                                           id="speciality_officio"/>
                        </x-slot>
                        @error('employee_request.specialities.speciality')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>

                    <x-forms.form-group class="">
                        <x-slot name="label">
                            <x-forms.label  for="specialities_speciality" class="default-label">
                                {{__('forms.speciality')}} *
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.select
                                class="default-input" wire:model="employee_request.specialities.speciality"
                                id="specialities_speciality">
                                <x-slot name="option">
                                    <option>{{__('forms.select')}}</option>
                                    @foreach($this->dictionaries['SPECIALITY_TYPE'] as $k=>$type)
                                        <option value="{{$k}}">{{$type}}</option>
                                    @endforeach
                                </x-slot>
                            </x-forms.select>
                        </x-slot>
                        @error('employee_request.specialities.speciality')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>
                    <x-forms.form-group class="">
                        <x-slot name="label">
                            <x-forms.label for="specialities_country" class="default-label">
                                {{__('forms.level_speciality')}}*
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.select
                                class="default-input" wire:model="employee_request.specialities.level" type="text"
                                id="specialities_country"
                            >
                                <x-slot name="option">
                                    <option>{{__('forms.select_country')}}</option>
                                    @foreach($this->dictionaries['SPECIALITY_LEVEL'] as $k=>$level)
                                        <option value="{{$k}}">{{$level}}</option>
                                    @endforeach
                                </x-slot>
                            </x-forms.select>

                        </x-slot>
                        @error('employee_request.specialities.country')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>
                    <x-forms.form-group class="">
                        <x-slot name="label">
                            <x-forms.label for="qualification_type" class="default-label">
                                {{__('forms.qualification_type')}}*
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.select
                                class="default-input" wire:model="employee_request.specialities.qualification_type" type="text"
                                id="qualification_type"
                            >
                                <x-slot name="option">
                                    <option>{{__('forms.qualification_type')}}</option>
                                    @foreach($this->dictionaries['SPEC_QUALIFICATION_TYPE'] as $k=>$qualification_type)
                                        <option value="{{$k}}">{{$qualification_type}}</option>
                                    @endforeach
                                </x-slot>
                            </x-forms.select>

                        </x-slot>
                        @error('employee_request.specialities.country')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>
                    <x-forms.form-group class="">
                        <x-slot name="label">
                            <x-forms.label for="attestation_name" class="default-label">
                                {{__('forms.attestation_name')}} *
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.input class="default-input" wire:model="employee_request.specialities.attestation_name" type="text"
                                           id="attestation_name"/>
                        </x-slot>
                        @error('employee_request.specialities.attestation_name')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>

                    <x-forms.form-group class="">
                        <x-slot name="label">
                            <x-forms.label for="certificate_number" class="default-label">
                                {{__('forms.certificate_number')}} *
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.input class="default-input" wire:model="employee_request.specialities.certificate_number" type="text"
                                           id="certificate_number"/>
                        </x-slot>
                        @error('employee_request.specialities.certificate_number')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>
                    <x-forms.form-group class="">
                        <x-slot name="label">
                            <x-forms.label for="attestation_date" class="default-label">
                                {{__('forms.attestation_date')}} *
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.input class="default-input" wire:model="employee_request.specialities.attestation_date" type="date"
                                           id="attestation_name"/>
                        </x-slot>
                        @error('employee_request.specialities.attestation_date')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>
                    <x-forms.form-group class="">
                        <x-slot name="label">
                            <x-forms.label for="certificate_number" class="default-label">
                                {{__('forms.valid_to_date')}}
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.input class="default-input" wire:model="employee_request.specialities.valid_to_date" type="date"
                                           id="valid_to_date"/>
                        </x-slot>
                        @error('employee_request.specialities.valid_to_date')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>
                </div>

                <div class="mb-4.5 mt-4.5 flex flex-col gap-6 xl:flex-row justify-between items-center ">
                    <div class="xl:w-1/4 text-left">
                        <x-secondary-button wire:click="closeModalModel()">
                            {{__('Закрити ')}}
                        </x-secondary-button>
                    </div>
                    <div class="xl:w-1/4 text-right">
                        <x-button type="submit" class="btn-primary">
                            {{__('Додати освіту')}}
                        </x-button>
                    </div>
                </div>
                <div wire:loading role="status" class="absolute -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2">
                    <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                         viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor"/>
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill"/>
                    </svg>
                </div>
            </x-slot>
        </x-forms.forms-section-modal>
    </x-slot>
</x-dialog-modal>
