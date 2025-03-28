<x-forms.form-row :gap="'gap-2'">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="specialityOfficio" class="default-label">
                {{__('forms.specialityOfficio')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.checkbox type="checkbox" wire:model="employeeRequest.speciality.specialityOfficio"
                              id="specialityOfficio"/>
        </x-slot>
        @error('employeeRequest.speciality.speciality')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="specialitySpeciality" class="default-label">
                {{__('forms.speciality')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select
                class="default-input" wire:model="employeeRequest.speciality.speciality"
                id="specialitySpeciality">
                <x-slot name="option">
                    <option>{{__('forms.select')}}</option>
                    @foreach($this->dictionaries['SPECIALITY_TYPE'] as $k=>$type)
                        <option value="{{$k}}">{{$type}}</option>
                    @endforeach
                </x-slot>
            </x-forms.select>
        </x-slot>
        @error('employeeRequest.speciality.speciality')
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
            <x-forms.label for="specialityCountry" class="default-label">
                {{__('forms.speciality_level')}}*
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select
                class="default-input" wire:model="employeeRequest.speciality.level" type="text"
                id="specialityCountry"
            >
                <x-slot name="option">
                    <option>{{__('forms.selectCountry')}}</option>
                    @foreach($this->dictionaries['SPECIALITY_LEVEL'] as $k=>$level)
                        <option value="{{$k}}">{{$level}}</option>
                    @endforeach
                </x-slot>
            </x-forms.select>

        </x-slot>
        @error('employeeRequest.speciality.country')
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
                class="default-input" wire:model="employeeRequest.speciality.qualificationType"
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
        @error('employeeRequest.speciality.country')
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
            <x-forms.input class="default-input" wire:model="employeeRequest.speciality.attestationName"
                           type="text"
                           id="attestationName"/>
        </x-slot>
        @error('employeeRequest.speciality.attestationName')
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
            <x-forms.input class="default-input" wire:model="employeeRequest.speciality.certificateNumber"
                           type="text"
                           id="certificateNumber"/>
        </x-slot>
        @error('employeeRequest.speciality.certificateNumber')
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
            <x-forms.input-date id="attestationDate" wire:model="employeeRequest.speciality.attestationDate"/>

        </x-slot>
        @error('employeeRequest.speciality.attestationDate')
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
                {{__('forms.valid_until')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input-date id="validToDate" wire:model="employeeRequest.speciality.validToDate"/>
        </x-slot>
        @error('employeeRequest.speciality.validToDate')
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

