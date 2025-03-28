<x-forms.form-row :gap="'gap-2'">
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="educationCountry" class="default-label">
                {{__('forms.type')}}*
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.select
                class="default-input" wire:model="employeeRequest.qualification.type" type="text"
                id="type"
            >
                <x-slot name="option">
                    <option>{{__('forms.type')}}</option>
                    @foreach($this->dictionaries['QUALIFICATION_TYPE'] as $k=>$type)
                        <option value="{{$k}}">{{$type}}</option>
                    @endforeach
                </x-slot>
            </x-forms.select>

        </x-slot>
        @error('employeeRequest.qualification.type')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="institutionName" class="default-label">
                {{__('forms.institutionName')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input"
                           wire:model="employeeRequest.qualification.institutionName" type="text"
                           id="institutionName"/>
        </x-slot>
        @error('employeeRequest.qualification.institutionName')
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
            <x-forms.label for="speciality" class="default-label">
                {{__('forms.speciality')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input" wire:model="employeeRequest.qualification.speciality"
                           type="text"
                           id="speciality"/>
        </x-slot>
        @error('employeeRequest.qualification.speciality')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="issuedDate" class="default-label">
                {{__('forms.issuedDate')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input-date id="issuedDate" wire:model="employeeRequest.qualification.issuedDate"
            />

        </x-slot>
        @error('employeeRequest.qualification.issuedDate')
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
            <x-forms.label for="certificateNumber" class="default-label">
                {{__('forms.certificateNumber')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input class="default-input"
                           wire:model="employeeRequest.qualification.certificateNumber" type="text"
                           id="certificateNumber"/>
        </x-slot>
        @error('employeeRequest.qualification.certificateNumber')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>
    <x-forms.form-group class="xl:w-1/2">
        <x-slot name="label">
            <x-forms.label for="validTo" class="default-label">
                {{__('forms.valid_until')}} *
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.input-date id="validTo" wire:model="employeeRequest.qualification.validTo"/>
        </x-slot>
        @error('employeeRequest.qualification.validTo')
        <x-slot name="error">
            <x-forms.error>
                {{$message}}
            </x-forms.error>
        </x-slot>
        @enderror
    </x-forms.form-group>

</x-forms.form-row>
<x-forms.form-row :gap="'gap-2'">
    <x-forms.form-group class="w-full">
        <x-slot name="label">
            <x-forms.label for="additionalInfo" class="default-label">
                {{__('forms.additionalInfo')}}
            </x-forms.label>
        </x-slot>
        <x-slot name="input">
            <x-forms.textarea class="default-input"
                              wire:model="employeeRequest.qualification.additionalInfo" type="text"
                              id="validTo"/>
        </x-slot>
        @error('employeeRequest.qualification.additionalInfo')
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


