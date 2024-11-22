<x-dialog-modal maxWidth="3xl" class="w-3 h-full" wire:model="showModal">
    <x-slot name="title">
        {{__('forms.education')}}
    </x-slot>
    <x-slot name="content">
        <x-forms.forms-section-modal
            submit="{!! $mode === 'edit' ? 'update(\'educations\',' . $keyProperty . ')' : 'store(\'educations\')' !!}">
            <x-slot name="form">
                <x-forms.form-row :gap="'gap-2'">
                    <x-forms.form-group class="w-1/3">
                        <x-slot name="label">
                            <x-forms.label for="degree" class="default-label">
                                {{__('forms.degree')}} *
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.select
                                class="default-input" wire:model="employeeRequest.educations.degree"
                                id="educationCountry"
                            >
                                <x-slot name="option">
                                    <option>{{__('forms.select')}}</option>
                                    @foreach($this->dictionaries['EDUCATION_DEGREE'] as $k=>$country)
                                        <option value="{{$k}}">{{$country}}</option>
                                    @endforeach
                                </x-slot>
                            </x-forms.select>

                        </x-slot>
                        @error('employeeRequest.educations.degree')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>
                    <x-forms.form-group class="w-1/3">
                        <x-slot name="label">
                            <x-forms.label for="institutionName" class="default-label">
                                {{__('forms.institutionName')}} *
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.input class="default-input"
                                           wire:model="employeeRequest.educations.institutionName" type="text"
                                           id="institutionName"/>
                        </x-slot>
                        @error('employeeRequest.educations.institutionName')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>
                    <x-forms.form-group class="w-1/3">
                        <x-slot name="label">
                            <x-forms.label for="speciality" class="default-label">
                                {{__('forms.speciality')}} *
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.input class="default-input" wire:model="employeeRequest.educations.speciality"
                                           type="text"
                                           id="speciality"/>
                        </x-slot>
                        @error('employeeRequest.educations.speciality')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>
                </x-forms.form-row>

                <x-forms.form-row :gap="'gap-2'">
                    <x-forms.form-group class="w-1/3">
                        <x-slot name="label">
                            <x-forms.label for="educationCountry" class="default-label">
                                {{__('forms.country')}}*
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.select
                                class="default-input" wire:model="employeeRequest.educations.country" type="text"
                                id="educationCountry"
                            >
                                <x-slot name="option">
                                    <option>{{__('forms.selectCountry')}}</option>
                                    @foreach($this->dictionaries['COUNTRY'] as $k=>$country)
                                        <option value="{{$k}}">{{$country}}</option>
                                    @endforeach
                                </x-slot>
                            </x-forms.select>

                        </x-slot>
                        @error('employeeRequest.educations.country')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>
                    <x-forms.form-group class="w-1/3">
                        <x-slot name="label">
                            <x-forms.label for="city" class="default-label">
                                {{__('forms.city')}} *
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.input class="default-input" wire:model="employeeRequest.educations.city"
                                           type="text"
                                           id="city"/>
                        </x-slot>
                        @error('employeeRequest.educations.city')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>
                    <x-forms.form-group class="w-1/3">
                        <x-slot name="label">
                            <x-forms.label for="speciality" class="default-label">
                                {{__('forms.diplomaNumber')}} *
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.input class="default-input" wire:model="employeeRequest.educations.diplomaNumber"
                                           type="text"
                                           id="diplomaNumber"/>
                        </x-slot>
                        @error('employeeRequest.educations.diplomaNumber')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>
                </x-forms.form-row>
                <x-forms.form-row class="mb-4.5">
                    <x-forms.form-group class="w-1/3">
                        <x-slot name="label">
                            <x-forms.label for="speciality" class="default-label">
                                {{__('forms.issuedDate')}}
                            </x-forms.label>
                        </x-slot>
                        <x-slot name="input">
                            <x-forms.input-date  wire:model="employeeRequest.educations.issuedDate"
                                           id="issuedDate"/>
                        </x-slot>
                        @error('employeeRequest.educations.issuedDate')
                        <x-slot name="error">
                            <x-forms.error>
                                {{$message}}
                            </x-forms.error>
                        </x-slot>
                        @enderror
                    </x-forms.form-group>

                </x-forms.form-row>
                <div class="mb-4.5 mt-4.5 flex flex-col gap-6 xl:flex-row justify-between items-center ">
                    <div class="xl:w-1/4 text-left">
                        <x-secondary-button wire:click="closeModalModel()">
                            {{__('Закрити ')}}
                        </x-secondary-button>
                    </div>
                    <div class="xl:w-1/4 text-right">
                        <x-button type="submit" class="default-button">
                            {{__('Додати освіту')}}
                        </x-button>
                    </div>
                </div>
            </x-slot>
        </x-forms.forms-section-modal>
    </x-slot>
</x-dialog-modal>



