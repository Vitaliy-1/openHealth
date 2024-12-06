<x-forms.form-row :gap="'gap-2'">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="specialityOfficio" class="default-label">
                {{__('forms.specialityOfficio')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.checkbox type="checkbox" wire:model="employeeRequest.specialities.specialityOfficio"
                              id="specialityOfficio"/>
        </x-slot>
        @error('employeeRequest.specialities.speciality')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="specialitiesSpeciality" class="default-label">
                {{__('forms.speciality')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select
                class="default-input" wire:model="employeeRequest.specialities.speciality"
                id="specialitiesSpeciality">
                <x-slot name="option">
                    <option>{{__('forms.select')}}</option>
                    @foreach($this->dictionaries['SPECIALITY_TYPE'] as $k=>$type)
                        <option value="{{$k}}">{{$type}}</option>
                    @endforeach
                </x-slot>
            </x-forms.select>
        </x-slot>
        @error('employeeRequest.specialities.speciality')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</x-forms.form-row>
<x-forms.form-row :gap="'gap-2'">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="specialitiesCountry" class="default-label">
                {{__('forms.levelSpeciality')}}*
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select
                class="default-input" wire:model="employeeRequest.specialities.level" type="text"
                id="specialitiesCountry"
            >
                <x-slot name="option">
                    <option>{{__('forms.selectCountry')}}</option>
                    @foreach($this->dictionaries['SPECIALITY_LEVEL'] as $k=>$level)
                        <option value="{{$k}}">{{$level}}</option>
                    @endforeach
                </x-slot>
            </x-forms.select>

        </x-slot>
        @error('employeeRequest.specialities.country')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="qualificationType" class="default-label">
                {{__('forms.qualificationType')}}*
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select
                class="default-input" wire:model="employeeRequest.specialities.qualificationType"
                type="text"
                id="qualificationType"
            >
                <x-slot name="option">
                    <option>{{__('forms.qualificationType')}}</option>
                    @foreach($this->dictionaries['SPEC_QUALIFICATION_TYPE'] as $k=>$qualificationType)
                        <option value="{{$k}}">{{$qualificationType}}</option>
                    @endforeach
                </x-slot>
            </x-forms.select>

        </x-slot>
        @error('employeeRequest.specialities.country')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</x-forms.form-row>
<x-forms.form-row :gap="'gap-2'">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="attestationName" class="default-label">
                {{__('forms.attestationName')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="employeeRequest.specialities.attestationName"
                           type="text"
                           id="attestationName"/>
        </x-slot>
        @error('employeeRequest.specialities.attestationName')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="certificateNumber" class="default-label">
                {{__('forms.certificateNumber')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="employeeRequest.specialities.certificateNumber"
                           type="text"
                           id="certificateNumber"/>
        </x-slot>
        @error('employeeRequest.specialities.certificateNumber')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</x-forms.form-row>
<x-forms.form-row :gap="'gap-2'">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="attestationDate" class="default-label">
                {{__('forms.attestationDate')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input-date id="attestationDate" wire:model="employeeRequest.specialities.attestationDate"/>

        </x-slot>
        @error('employeeRequest.specialities.attestationDate')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="certificateNumber" class="default-label">
                {{__('forms.validToDate')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input-date id="validToDate" wire:model="employeeRequest.specialities.validToDate"/>
        </x-slot>
        @error('employeeRequest.specialities.validToDate')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
</x-forms.form-row>

<x-forms.form-row class="mb-4.5 mt-4.5 flex flex-col gap-6 xl:flex-row justify-between items-center ">
    <div class="xl:w-1/4 text-left">
        <x-secondary-button wire:click="closeModalModel()">
            {{__('forms.close')}}
        </x-secondary-button>
    </div>
    <div class="xl:w-1/4 text-right">
        <x-button type="submit" class="default-button">
            {{__('forms.save')}}
        </x-button>
    </div>
</x-forms.form-row>

